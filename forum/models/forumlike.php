<?php
/**
 * Zira project.
 * forumlike.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Forumlike extends Orm {
    public static $table = 'forum_likes';
    public static $pk = 'id';
    public static $alias = 'frm_lke';

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
            Message::getClass() => 'message_id',
            Zira\Models\User::getClass() => 'user_id'
        );
    }
}