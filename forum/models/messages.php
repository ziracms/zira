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
}