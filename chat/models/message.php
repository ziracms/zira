<?php
/**
 * Zira project.
 * message.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Models;

use Zira;
use Zira\Orm;

class Message extends Orm {
    const STATUS_NONE = 0;
    const STATUS_INFO = 1;
    const STATUS_MESSAGE = 2;
    const STATUS_WARNING = 3;
    
    public static $table = 'chat_messages';
    public static $pk = 'id';
    public static $alias = 'cht_msg';

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
            Chat::getClass() => 'chat_id',
            Zira\Models\User::getClass() => 'creator_id'
        );
    }
    
    public static function getFields() {
        return array(
            'id',
            'chat_id',
            'creator_id',
            'creator_name',
            'content',
            'date_created',
            'status'
        );
    }
    
    public static function getStatuses() {
        return array(
            self::STATUS_NONE => Zira\Locale::tm('Default', 'chat'),
            self::STATUS_INFO => Zira\Locale::tm('Information', 'chat'),
            self::STATUS_MESSAGE => Zira\Locale::tm('Message', 'chat'),
            self::STATUS_WARNING => Zira\Locale::tm('Warning', 'chat')
        );
    }
    
    public static function cleanUp() {
        self::getCollection()
                ->delete()
                ->where('date_created','<',date('Y-m-d H:i:s', time()-\Chat\Chat::TRASH_TIME))
                ->execute();
    }
}