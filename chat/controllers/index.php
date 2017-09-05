<?php
/**
 * Zira project.
 * index.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Controllers;

use Zira;
use Chat;

class Index extends Zira\Controller {
    public function index() {
        if (!Zira\Request::isPost() || !Zira\Request::isAjax()) Zira\Response::forbidden();
        $chat_id = Zira\Request::post('chat_id');
        $last_id = Zira\Request::post('last_id');
        
        $query = Chat\Models\Message::getCollection()
                                    ->select(Chat\Models\Message::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('author_username'=>'username','author_firstname'=>'firstname','author_secondname'=>'secondname','author_image'=>'image'))
                                    ->where('chat_id','=',$chat_id);
        
        if ($last_id) {
            $query->and_where('id','>',$last_id);
            $query->order_by('id', 'ASC');
        } else {
            $query->order_by('id', 'DESC');
        }

        $query->limit(Chat\Chat::WIDGET_LIMIT);
        
        $rows = $query->get();
        
        if (!$last_id) {
            $rows = array_reverse($rows);
        }
        
        $response = array('messages'=>array(), 'last_id'=>0);
        foreach($rows as $row) {
            $mclass = '';
            if ($row->status == Chat\Models\Message::STATUS_MESSAGE) $mclass = ' alert alert-success';
            if ($row->status == Chat\Models\Message::STATUS_INFO) $mclass = ' alert alert-info';
            if ($row->status == Chat\Models\Message::STATUS_WARNING) $mclass = ' alert alert-danger';
            $micon = '';
            if ($row->status == Chat\Models\Message::STATUS_MESSAGE) $micon = '<span class="glyphicon glyphicon-info-sign"></span> ';
            if ($row->status == Chat\Models\Message::STATUS_INFO) $micon = '<span class="glyphicon glyphicon-exclamation-sign"></span> ';
            if ($row->status == Chat\Models\Message::STATUS_WARNING) $micon = '<span class="glyphicon glyphicon-warning-sign"></span> ';

            $message = '<div class="chat-message-wrapper">';
            $message .= '<div class="chat-message-image">';
            if ($row->creator_id > 0 && $row->author_username !== null && $row->author_firstname !== null && $row->author_secondname !== null)
                $message .= Zira\User::generateUserProfileThumbLink($row->creator_id, $row->author_firstname, $row->author_secondname, $row->author_username, null, $row->author_image, null, array('class'=>'chat-message-avatar'));
            else
                $message .= Zira\User::generateUserProfileThumb($row->author_image, null, array('class'=>'chat-message-avatar'));
            $message .= '</div>';
            $message .= '<div class="chat-message-author">';
            if ($row->creator_id > 0 && $row->author_username !== null && $row->author_firstname !== null && $row->author_secondname !== null)
                $message .= ($row->creator_name ? Zira\User::generateUserProfileLink($row->creator_id, null, null, $row->creator_name) : Zira\User::generateUserProfileLink($row->creator_id, $row->author_firstname, $row->author_secondname, $row->author_username));
            else 
                $message .= ($row->creator_name ? Zira\Helper::html($row->creator_name) : Zira\Locale::tm('Guest','chat'));
            $message .= '</div>';
            $message .= '<p class="chat-message-text parse-content'.$mclass.'">'.$micon;
            $message .= Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($row->content)));
            $message .= '</p>';
            $message .= '</div>';
            $response['messages'][]=$message;
            $response['last_id'] = $row->id;
        }
        
        Zira\Page::render($response);
    }
    
    public function submit() {
        if (!Zira\Request::isPost() || !Zira\Request::isAjax()) Zira\Response::forbidden();
        
        $form = new Chat\Forms\Submit();
        if ($form->isValid()) {
            $chat_id = (int)$form->getValue('chat_id');
            
            $chat = new Chat\Models\Chat($chat_id);
            if (!$chat->loaded()) Zira\Response::notFound();

            if ($chat->check_auth && !Zira\User::isAuthorized()) Zira\Response::forbidden();

            if ($chat->visible_group && 
                Zira\User::getCurrent()->group_id != $chat->visible_group &&
                !Zira\Permission::check(Chat\Chat::PERMISSION_MODERATE)
            ) {
                Zira\Response::forbidden();
            }
        
            $content = $form->getValue('message');
            $content = str_replace("\r",'',$content);
            $content = str_replace("\n","\r\n",$content);
            $content = Zira\Helper::utf8Entity($content);
            
            $message = new Chat\Models\Message();
            $message->chat_id = $chat->id;
            $message->creator_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
            $message->creator_name = Zira\User::isAuthorized() ? Zira\User::getProfileName() : Zira\Helper::utf8Entity($form->getValue('sender_name'));
            $message->content = $content;
            $message->date_created = date('Y-m-d H:i:s');
            $message->status = Chat\Models\Message::STATUS_NONE;
            $message->save();
            
            $response = array('status' => 1);
        } else {
            $response = array('status' => 0, 'error'=>$form->getError());
        }
        
        Zira\Page::render($response);
    }
}