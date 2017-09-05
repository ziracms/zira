<?php
/**
 * Zira project.
 * messages.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Models;

use Zira;
use Dash;
use Chat;
use Zira\Permission;

class Messages extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Chat\Chat::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Chat\Forms\Message();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            $content = $form->getValue('content');
            $content = str_replace("\r",'',$content);
            $content = str_replace("\n","\r\n",$content);
            $content = Zira\Helper::utf8Entity($content);

            if ($id) {
                $message = new Chat\Models\Message($id);
                if (!$message->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $message = new Chat\Models\Message();
                $message->chat_id = (int)$form->getValue('chat_id');
                $message->creator_id = Zira\User::getCurrent()->id;
                $message->date_created = date('Y-m-d H:i:s');
            }
            
            $message->content = $content;
            $message->status = (int)$form->getValue('status');

            $message->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Chat\Chat::PERMISSION_MODERATE)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $message_id) {
            $message = new Chat\Models\Message($message_id);
            if (!$message->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };

            $message->delete();
        }

        return array('reload' => $this->getJSClassName());
    }

    public function preview($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Chat\Chat::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $message = Chat\Models\Message::getCollection()
                                    ->select(Chat\Models\Message::getFields())
                                    ->join(Chat\Models\Chat::getClass(), array('chat_title'=>'title'))
                                    ->where('id','=',$id)
                                    ->get(0);

        if (!$message) return array('error' => Zira\Locale::t('An error occurred'));

        $username = Zira\Locale::tm('Guest', 'chat');
        if ($message->creator_id>0) {
            $user = new Zira\Models\User($message->creator_id);
            if ($user->loaded()) {
                $username = Zira\User::getProfileName($user);
            }
        }

        return array(
            'user'=>Zira\Helper::html($username),
            'content'=>'<p class="parse-content">'.Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($message->content))).'</p>',
            'chat'=>Zira\Locale::tm('Chat','chat').': '.Zira\Helper::html($message->chat_title)
        );
    }
}