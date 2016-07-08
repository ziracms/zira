<?php
/**
 * Zira project.
 * messages.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Messages extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Forum\Forms\Message();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            $content = $form->getValue('content');
            $content = str_replace("\r",'',$content);
            $content = str_replace("\n","\r\n",$content);
            $content = Zira\Helper::utf8Entity($content);

            if ($id) {
                $message = new Forum\Models\Message($id);
                if (!$message->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
                $message->modified_by = Zira\User::getCurrent()->id;
                $message->content = $content;
                $message->date_modified = date('Y-m-d H:i:s');
                $message->status = (int)$form->getValue('status');

                $message->save();
            } else {
                $topic_id = $form->getValue('topic_id');

                $category_fields = Forum\Models\Category::getFields();
                $_category_fields = array();
                foreach($category_fields as $field) {
                    $_category_fields['category_'.$field] = $field;
                }

                $forum_fields = Forum\Models\Forum::getFields();
                $_forum_fields = array();
                foreach($forum_fields as $field) {
                    $_forum_fields['forum_'.$field] = $field;
                }

                $topic = Forum\Models\Topic::getCollection()
                                        ->select(Forum\Models\Topic::getFields())
                                        ->join(Forum\Models\Category::getClass(), $_category_fields)
                                        ->join(Forum\Models\Forum::getClass(), $_forum_fields)
                                        ->where('id','=',$topic_id)
                                        ->get(0);

                if (!$topic) return array('error' => Zira\Locale::t('An error occurred'));

                Forum\Models\Message::createNewMessage($topic->forum_id, $topic->id, $content, ++$topic->messages, $topic->forum_topics, (int)$form->getValue('status'));
            }

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $message_id) {
            Forum\Models\Message::deleteMessage($message_id);
        }

        return array('reload' => $this->getJSClassName());
    }

    public function activate($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $message = new Forum\Models\Message($id);
        if (!$message->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $topic = new Forum\Models\Topic($message->topic_id);
        if (!$topic->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $forum = new Forum\Models\Forum($topic->forum_id);
        if (!$forum->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $user = new Zira\Models\User($message->creator_id);
        if (!$user->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $message->published = Forum\Models\Message::STATUS_PUBLISHED;
        $message->save();

        if ($topic->published != Forum\Models\Topic::STATUS_PUBLISHED && $topic->creator_id = $message->creator_id) {
            $topic->published = Forum\Models\Topic::STATUS_PUBLISHED;
            $topic->save();

            Forum\Models\Forum::getCollection()
                ->update(array(
                    'date_modified' => date('Y-m-d H:i:s'),
                    'last_user_id' => $user->id,
                    'topics' => ++$forum->topics
                ))->where('id','=',$forum->id)
                ->execute();

            \Forum\Models\Search::indexTopic($topic);
        }

        Topic::getCollection()
                ->update(array(
                    'date_modified' => date('Y-m-d H:i:s'),
                    'last_user_id' => $user->id,
                    'messages' => ++$topic->messages
                ))->where('id','=',$topic->id)
                ->execute();

        $user->posts++;
        $user->save();

        return array('reload' => $this->getJSClassName());
    }

    public function preview($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $message = Forum\Models\Message::getCollection()
                                    ->select(Forum\Models\Message::getFields())
                                    ->join(Forum\Models\Topic::getClass(), array('topic_title'=>'title'))
                                    ->where('id','=',$id)
                                    ->get(0);

        if (!$message) return array('error' => Zira\Locale::t('An error occurred'));

        $user = new Zira\Models\User($message->creator_id);
        if ($user->loaded()) {
            $username = Zira\User::getProfileName($user);
        } else {
            $username = Zira\Locale::tm('User deleted', 'forum');
        }

        $attaches = '';

        $files = Forum\Models\File::getCollection()
                                    ->where('message_id','=',$message->id)
                                    ->get(0);

        if ($files) {
            $images = Forum\Models\File::extractItemFiles($files, '', 'images');
            $files = Forum\Models\File::extractItemFiles($files, '', 'files');

            if (!empty($images) || !empty($files)) {
                foreach ($images as $filepath => $filename) {
                    if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $filepath)) {
                        $filesrc = UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $filepath);
                        $attaches .= '<a class="forum-message-attach" data-lightbox="forum-message-' . Zira\Helper::html($message->id) . '" href="' . Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) . '" title="' . Zira\Helper::html($filename) . '">';
                        $attaches .= '<img src="' . Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) . '" alt="' . Zira\Helper::html($filename) . '" height="' . Zira\Config::get('thumbs_height') . '" style="margin: 5px" />';
                        $attaches .= '</a>';
                    } else {
                        $attaches .= '<span class="forum-message-attach" style="margin: 5px">' . Zira\Locale::tm('File "%s" not found', 'forum', Zira\Helper::html($filename)) . '</span>';
                    }
                }
                foreach ($files as $filepath => $filename) {
                    if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $filepath)) {
                        $filesrc = UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $filepath);
                        $attaches .= '<a class="forum-message-attach" href="' . Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) . '" title="' . Zira\Helper::html($filename) . '" download="' . Zira\Helper::html($filename) . '" target="_blank" rel="nofollow" style="margin: 5px">';
                        $attaches .= Zira\Helper::html($filename);
                        $attaches .= '</a>';
                    } else {
                        $attaches .= '<span class="forum-message-attach" style="margin: 5px">' . Zira\Locale::tm('File "%s" not found', 'forum', Zira\Helper::html($filename)) . '</span>';
                    }
                }
            }
        }

        return array(
            'user'=>$username,
            'content'=>'<p class="parse-content">'.Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($message->content))).'</p>',
            'attaches'=>$attaches,
            'topic'=>Zira\Locale::tm('Forum thread','forum').': '.$message->topic_title
        );
    }
}