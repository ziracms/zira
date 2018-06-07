<?php
/**
 * Zira project.
 * conversation.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Conversation extends Orm {
    public static $table = 'conversations';
    public static $pk = 'id';
    public static $alias = 'con';

    public static function getTable() {
        return self::$table;
    }

    public static function getPk() {
        return self::$pk;
    }

    public static function getAlias() {
        return self::$alias;
    }

    public static function getReferences() {
        return array(
            User::getClass() => 'user_id'
        );
    }

    public static function createConversation($sender_id, $recipient_id, $subject) {
        \Zira\Db\Db::begin();
        try {
            //$max_id = self::getCollection()->max('conversation_id')->get('mx');
            $max_id = Message::getCollection()->max('conversation_id')->for_update()->get('mx');
            $conversation_id = ++$max_id;

            $conversation = new self;
            $conversation->conversation_id = $conversation_id;
            $conversation->user_id = $sender_id;
            $conversation->subject = $subject;
            $conversation->creation_date = date('Y-m-d H:i:s');
            $conversation->modified_date = date('Y-m-d H:i:s');
            $conversation->highlight = 0;
            $conversation->save();

            $conversation = new self;
            $conversation->conversation_id = $conversation_id;
            $conversation->user_id = $recipient_id;
            $conversation->subject = $subject;
            $conversation->creation_date = date('Y-m-d H:i:s');
            $conversation->modified_date = date('Y-m-d H:i:s');
            $conversation->highlight = 1;
            $conversation->save();

            \Zira\Db\Db::commit();
            return $conversation_id;
        } catch(\Exception $e) {
            \Zira\Db\Db::rollback();
            return false;
        }
    }

    public static function createGroupConversation($sender_id, array $recipient_ids, $subject) {
        \Zira\Db\Db::begin();
        try {
            //$max_id = self::getCollection()->max('conversation_id')->get('mx');
            $max_id = Message::getCollection()->max('conversation_id')->for_update()->get('mx');
            $conversation_id = ++$max_id;

            $conversation = new self;
            $conversation->conversation_id = $conversation_id;
            $conversation->user_id = $sender_id;
            $conversation->subject = $subject;
            $conversation->creation_date = date('Y-m-d H:i:s');
            $conversation->modified_date = date('Y-m-d H:i:s');
            $conversation->highlight = 0;
            $conversation->save();

            foreach ($recipient_ids as $recipient_id) {
                $conversation = new self;
                $conversation->conversation_id = $conversation_id;
                $conversation->user_id = $recipient_id;
                $conversation->subject = $subject;
                $conversation->creation_date = date('Y-m-d H:i:s');
                $conversation->modified_date = date('Y-m-d H:i:s');
                $conversation->highlight = 1;
                $conversation->save();
            }

            \Zira\Db\Db::commit();
            return $conversation_id;
        } catch(\Exception $e) {
            \Zira\Db\Db::rollback();
            return false;
        }
    }
}