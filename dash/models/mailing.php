<?php
/**
 * Zira project.
 * mailing.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Mailing extends Model {
    public function mail() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new Dash\Forms\Mailing();
        if ($form->isValid()) {
            $type = $form->getValue('type');
            $offset = (int)$form->getValue('offset');
            $language = $form->getValue('language');
            if ($type == 'email') {
                return $this->send_emails($form->getValue('subject'), $form->getValue('message'), $offset, $language);
            } else if ($type == 'message') {
                return $this->send_messages($form->getValue('subject'), $form->getValue('message'), $offset, $language);
            } else {
                return array('error'=>Zira\Locale::t('An error occurred'));
            }
        } else {
            return array('error'=>$form->getError());
        }
    }

    protected function send_emails($subject, $content, $page, $language='') {
        $total_q = Zira\Models\User::getCollection()
                                ->count()
                                ->where('verified','=',Zira\Models\User::STATUS_VERIFIED)
                                ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->and_where('subscribed','=',Zira\Models\User::STATUS_SUBSCRIBED)
                                ;

        if (!empty($language)) {
            $total_q->and_where('language', '=', $language);
        }

        $total = $total_q->get('co');

        $offset = Dash\Windows\Mailing::LIMIT * ($page);
        $left = 0;
        $sent = 0;

        if ($offset<$total) {
            $subscribers_q = Zira\Models\User::getCollection()
                                ->where('verified','=',Zira\Models\User::STATUS_VERIFIED)
                                ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->and_where('subscribed','=',Zira\Models\User::STATUS_SUBSCRIBED)
                                ;

            if (!empty($language)) {
                $subscribers_q->and_where('language', '=', $language);
            }

            $subscribers = $subscribers_q->limit(Dash\Windows\Mailing::LIMIT, $offset)
                                        ->order_by('id','asc')
                                        ->get();

            foreach ($subscribers as $recipient) {
                try {
                    $_content = str_replace('$user', Zira\User::getProfileName($recipient), $content);
                    Zira\Mail::send($recipient->email, Zira\Helper::html($subject), Zira\Helper::html($_content));
                } catch (\Exception $e) {
                    Zira\Log::exception($e);
                }
            }

            $sent = $offset + count($subscribers);
            $left = $total - $sent;
        }

        return array(
            'total' => $total,
            'sent' => $sent,
            'left' => $left,
            'offset' => $page,
            'type' => 'email'
        );
    }

    protected function send_messages($subject, $content, $page, $language='') {
        $total_q = Zira\Models\User::getCollection()
                                ->count()
                                ->where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ;

        if (!empty($language)) {
            $total_q->and_where('language', '=', $language);
        }
        
        $total = $total_q->get('co');

        $offset = Dash\Windows\Mailing::LIMIT * ($page);
        $left = 0;
        $sent = 0;

        if ($offset<$total) {
            $subscribers_q = Zira\Models\User::getCollection()
                                ->where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ;

            if (!empty($language)) {
                $subscribers_q->and_where('language', '=', $language);
            }

            $subscribers = $subscribers_q->limit(Dash\Windows\Mailing::LIMIT, $offset)
                                        ->order_by('id','asc')
                                        ->get(null, true);

            Zira\Locale::load(null, 'zira');

            foreach ($subscribers as $_recipient) {
                $recipient = new Zira\Models\User();
                $recipient->loadFromArray($_recipient);
                $_content = str_replace('$user', Zira\User::getProfileName($recipient), $content);

                $max_id = Zira\Models\Message::getCollection()->max('conversation_id')->get('mx');
                $conversation_id = ++$max_id;

                $conversation = new Zira\Models\Conversation();
                $conversation->conversation_id = $conversation_id;
                $conversation->user_id = $recipient->id;
                $conversation->subject = $subject;
                $conversation->creation_date = date('Y-m-d H:i:s');
                $conversation->modified_date = date('Y-m-d H:i:s');
                $conversation->highlight = 1;
                $conversation->save();

                if ($conversation_id) {
                    $message = new Zira\Models\Message();
                    $message->conversation_id = $conversation_id;
                    $message->user_id = Zira\User::getCurrent()->id;
                    $message->content = $_content;
                    $message->creation_date = date('Y-m-d H:i:s');
                    $message->save();
                }
                Zira\User::increaseMessagesCount($recipient);
                try {
                    Zira\Models\Message::notify($recipient, Zira\User::getCurrent());
                } catch (\Exception $e) {
                    Zira\Log::exception($e);
                }
            }

            $sent = $offset + count($subscribers);
            $left = $total - $sent;
        }

        return array(
            'total' => $total,
            'sent' => $sent,
            'left' => $left,
            'offset' => $page,
            'type' => 'message'
        );
    }
}