<?php
/**
 * Zira project.
 * comments.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Comments extends Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $comment_id) {
            $comment = new Zira\Models\Comment($comment_id);
            if (!$comment->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $comment->delete();

            Zira\Models\Comment::getCollection()
                                ->where('record_id','=',$comment->record_id)
                                ->and_where('sort_path','like',$comment->sort_path.Zira\Models\Comment::PATH_DELIMITER.'%')
                                ->delete()
                                ->execute();

            if ($comment->published == Zira\Models\Comment::STATUS_PUBLISHED) {
                $record = new Zira\Models\Record($comment->record_id);
                if ($record->loaded()) {
                    $record->comments--;
                }
                if ($record->comments<0) $record->comments = 0;
                $record->save();

                if ($comment->author_id>0) {
                    $user = new Zira\Models\User($comment->author_id);
                    if ($user->loaded()) {
                        Zira\User::decreaseCommentsCount($user);
                    }
                }
            }
            
            // deleting likes
            Zira\Models\Commentlike::removeCommentLikes($comment->id);
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function activate($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $co = 0;
        foreach($data as $comment_id) {
            $comment = new Zira\Models\Comment($comment_id);
            if (!$comment->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            if ($comment->published == Zira\Models\Comment::STATUS_PUBLISHED) continue;

            $comment->published = Zira\Models\Comment::STATUS_PUBLISHED;
            $comment->save();
            $co++;

            $record = new Zira\Models\Record($comment->record_id);
            if ($record->loaded()) {
                $record->comments++;
            }
            $record->save();

            if ($comment->author_id>0) {
                $user = new Zira\Models\User($comment->author_id);
                if ($user->loaded()) {
                    Zira\User::increaseCommentsCount($user);
                }
            }
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s comments', $co), 'reload'=>$this->getJSClassName());
    }

    public function edit($id, $content) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $comment = new Zira\Models\Comment($id);
        if (!$comment->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        };

        $content = str_replace("\r",'',$content);
        $content = str_replace("\n","\r\n",$content);

        $comment->content = Zira\Helper::utf8Entity($content);
        $comment->save();

        return array('message' => Zira\Locale::t('Successfully saved'), 'reload'=>$this->getJSClassName());
    }

    public function info($id) {
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            return array();
        }

        $info = array();

        $comment = Zira\Models\Comment::getCollection()
                    ->select(Zira\Models\Comment::getFields())
                    ->join(Zira\Models\Record::getClass(), array('record_title' => 'title'))
                    ->left_join(Zira\Models\User::getClass(), array('author_username'=>'username'))
                    ->where('id','=',$id)
                    ->get(0);

        if (!$comment) return array();

        $info[] = '<span class="glyphicon glyphicon-tag"></span> ' . Zira\Helper::html($comment->record_title);
        if ($comment->author_id>0) {
            $info[] = '<span class="glyphicon glyphicon-user"></span> ' . Zira\Helper::html($comment->author_username);
        } else {
            $info[] = '<span class="glyphicon glyphicon-eye-close"></span> ' . ($comment->sender_name ? Zira\Helper::html($comment->sender_name) : Zira\Locale::t('Guest'));
        }
        $info[] = '<span class="glyphicon glyphicon-time"></span> ' . date(Zira\Config::get('date_format'), strtotime($comment->creation_date));
        if ($comment->likes) $info[] = '<span class="glyphicon glyphicon-thumbs-up"></span> ' . $comment->likes;
        if ($comment->dislikes) $info[] = '<span class="glyphicon glyphicon-thumbs-down"></span> ' . $comment->dislikes;

        return $info;
    }

    public function preview($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $comment = Zira\Models\Comment::getCollection()
                    ->select(Zira\Models\Comment::getFields())
                    ->left_join(Zira\Models\Record::getClass(), array('record_id'=>'id', 'record_title' => 'title'))
                    ->where('id','=',$id)
                    ->get(0);

        if (!$comment) return array('error' => Zira\Locale::t('An error occurred'));

        if ($comment->author_id > 0) {
            $user = new Zira\Models\User($comment->author_id);
            if ($user->loaded()) {
                $username = Zira\User::getProfileName($user);
            } else {
                $username = Zira\Locale::tm('User deleted', 'forum');
            }
        } else {
            $username = $comment->sender_name ? $comment->sender_name.' ('.Zira\Locale::t('Guest').')' : Zira\Locale::t('Guest');
        }

        return array(
            'user'=>$username,
            'content'=>'<p class="parse-content">'.Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($comment->content))).'</p>',
            'record'=>$comment->record_id ? Zira\Locale::t('Record').': '.$comment->record_title : Zira\Locale::t('Record deleted')
        );
    }

    public function getNewCommentsCount() {
        return Zira\Models\Comment::getCollection()
                                ->count()
                                ->where('published','=',Zira\Models\Comment::STATUS_NOT_PUBLISHED)
                                ->get('co');
    }
}