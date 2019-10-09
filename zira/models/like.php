<?php
/**
 * Zira project.
 * like.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Like extends Orm {
    public static $table = 'likes';
    public static $pk = 'id';
    public static $alias = 'lke';

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
        return array();
    }
    
    public static function removeRecordLikes($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
    
    public static function removeUserLikes($user_id) {
        self::getCollection()
                ->delete()
                ->where('user_id', '=', $user_id)
                ->execute();
    }
}