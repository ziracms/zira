<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Models;

use Zira;
use Zira\Orm;

class Chat extends Orm {
    public static $table = 'chats';
    public static $pk = 'id';
    public static $alias = 'chts';

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
}