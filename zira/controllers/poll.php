<?php
/**
 * Zira project.
 * poll.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Poll extends Zira\Controller {
    /**
     * AJAX action
     * Handles likes and dislikes requests
     */
    public function index() {
        Zira\View::setAjax(true);

        if (!Zira\Request::isPost()) return;
        $value = Zira\Request::post('value');
        $id = Zira\Request::post('id');
        $type = Zira\Request::post('type');
        $token = Zira\Request::post('token');

        if (!isset($value) || empty($id) || empty($type) || empty($token)) return;
        if (!Zira\User::checkToken($token)) return;

        $user_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
        $anonymous_id = Zira\User::getAnonymousUserId();
        if (empty($user_id) && empty($anonymous_id)) return;

        if ($type == 'record' && $value==1) {
            $record = new Zira\Models\Record($id);
            if (!$record->loaded()) return;

            $query = Zira\Models\Like::getCollection()
                            ->count()
                            ->where('record_id','=',$record->id);

            if (!empty($user_id)) {
                $query->and_where();
                $query->open_where();
                $query->where('user_id','=',$user_id);
                $query->or_where('anonymous_id','=',$anonymous_id);
                $query->close_where();
            } else if (!empty($anonymous_id)) {
                $query->and_where('anonymous_id','=',$anonymous_id);
            }

            $co = $query->get('co');

            if ($co==0) {
                $like = new Zira\Models\Like();
                $like->record_id = $record->id;
                $like->user_id = $user_id;
                $like->anonymous_id = $anonymous_id;
                $like->creation_date = date('Y-m-d H:i:s');
                $like->save();

                $record->rating++;
                $record->save();
            }

            Zira\Page::render(array('rating'=>$record->rating));
        } else if ($type == 'comment' && ($value==1 || $value==-1)) {
            $comment = new Zira\Models\Comment($id);
            if (!$comment->loaded()) return;

            $query = Zira\Models\Commentlike::getCollection()
                            ->where('comment_id','=',$comment->id);

            if (!empty($user_id)) {
                $query->and_where();
                $query->open_where();
                $query->where('user_id','=',$user_id);
                $query->or_where('anonymous_id','=',$anonymous_id);
                $query->close_where();
            } else if (!empty($anonymous_id)) {
                $query->and_where('anonymous_id','=',$anonymous_id);
            }

            $exists = $query->get(0, true);

            if (!$exists || $exists['rate'] != $value) {
                $like = new Zira\Models\Commentlike();
                if (!$exists) {
                    $like->comment_id = $comment->id;
                    $like->user_id = $user_id;
                    $like->anonymous_id = $anonymous_id;
                    $like->creation_date = date('Y-m-d H:i:s');
                } else {
                    $like->loadFromArray($exists);
                    if ($exists['rate']>0) $comment->likes--;
                    else $comment->dislikes--;
                }
                $like->rate = $value;
                $like->save();

                if ($value>0) $comment->likes++;
                else $comment->dislikes++;
                $comment->save();
            }

            if ($value>0)
                Zira\Page::render(array('rating'=>$comment->likes));
            else
                Zira\Page::render(array('rating'=>$comment->dislikes));
        }
    }
}