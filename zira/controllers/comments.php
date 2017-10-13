<?php
/**
 * Zira project.
 * comments.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Comments extends Zira\Controller {
    /**
     * AJAX action
     * Loads record comments
     */
    public function index() {
        if (Zira\Request::isPost()) {
            $record_id = (int)Zira\Request::post('record_id');
            $page = (int)Zira\Request::post('page');
            $reload = (bool)Zira\Request::post('reload');

            if (!$record_id || $page<0 || ($page==0 && !$reload)) return;

            $preview = Zira\Page::allowPreview();
            $limit = Zira\Config::get('comments_limit', 10);
            $comments = Zira\Models\Comment::getComments($record_id, $limit, $page*$limit, !$preview);

            $commenting_allowed = Zira\Config::get('comments_allowed',true);
            if (!Zira\Config::get('comment_anonymous',true) &&
                !Zira\User::isAuthorized()
            ) {
                $commenting_allowed = false;
            }
        
            Zira\View::renderView(array(
                'record_id'=>$record_id,
                'comments'=>$comments,
                'limit'=>$limit,
                'page'=>$page,
                'total'=>Zira\Models\Comment::countComments($record_id, !$preview),
                'ajax'=>true,
                'commenting_allowed'=>$commenting_allowed
            ), 'zira/comments');
        }
    }

    /**
     * Comment action
     */
    public function comment() {
        $form = new Zira\Forms\Comment();
        $commenting_allowed = Zira\Config::get('comments_allowed',true);
        if (!Zira\Config::get('comment_anonymous',true) &&
            !Zira\User::isAuthorized()
        ) {
            $commenting_allowed = false;
        }
        if (Zira\Request::isPost() && $commenting_allowed) {
            if ($form->isValid()) {
                $record = new Zira\Models\Record($form->getValue('record_id'));
                if (!$record->loaded()) {
                    $form->setError(Zira\Locale::t('An error occurred'));
                } else {
                    $parent = null;
                    $reply = null;
                    $parent_id = (int)$form->getValue('parent_id');
                    $reply_id = (int)$form->getValue('reply_id');
                    if ($parent_id>0) {
                        $parent = new Zira\Models\Comment($parent_id);
                        if (!$parent->loaded()) {
                            $parent = null;
                            $parent_id = 0;
                            $reply = null;
                            $reply_id = 0;
                        }
                        if ($parent && $reply_id>0 && $reply_id != $parent_id) {
                            $reply = new Zira\Models\Comment($reply_id);
                            if (!$reply->loaded()) {
                                $reply = null;
                                $reply_id = 0;
                            }
                        } else if ($parent) {
                            $reply = $parent;
                        }
                    }
                    $comment = new Zira\Models\Comment();
                    $comment->record_id = $record->id;
                    if (Zira\User::isAuthorized()) {
                        $comment->author_id = Zira\User::getCurrent()->id;
                        $comment->sender_name = Zira\User::getProfileName();
                    } else {
                        $comment->author_id = 0;
                        $comment->sender_name = strip_tags($form->getValue('sender_name'));
                    }
                    $comment->parent_id = $parent_id;
                    if ($reply!==null) {
                        $comment->recipient_name = $reply->sender_name;
                    }
                    $path_offset = Zira\Models\Comment::getPathOffset($record->id, $parent);
                    $comment->path_offset = $path_offset+1;
                    $comment->sort_path = Zira\Models\Comment::getSortPath($path_offset, $parent);
                    $comment->content = Zira\Helper::utf8Entity(html_entity_decode($form->getValue('comment')));
                    $comment->creation_date = date('Y-m-d H:i:s');
                    if (Zira\Permission::check(Zira\Permission::TO_MODERATE_COMMENTS) ||
                        !Zira\Config::get('comment_moderate', true)
                    ) {
                        $comment->published = Zira\Models\Comment::STATUS_PUBLISHED;
                    } else {
                        $comment->published = Zira\Models\Comment::STATUS_NOT_PUBLISHED;
                    }
                    try {
                        $comment->save();
                        if ($comment->published != Zira\Models\Comment::STATUS_PUBLISHED) {
                            $form->setMessage(Zira\Locale::t('Thank you. Your message is awaiting moderation'));
                        } else {
                            $form->setMessage(Zira\Locale::t('Thank you. Your message was published'));
                            $record->comments++;
                            $record->save();
                            if (Zira\User::isAuthorized()) {
                                Zira\User::increaseCommentsCount();
                            }
                        }
                        try {
                            Zira\Models\Comment::notify($record, $comment);
                        } catch (\Exception $e) {
                            Zira\Log::exception($e);
                        }
                    } catch(\Exception $err) {
                        $form->setError(Zira\Locale::t('An error occurred'));
                    }
                }
            }
        }
        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => $form
        ));
    }
}