<?php
/**
 * Zira project.
 * blacklist.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira;
use Zira\Orm;

class Blacklist extends Orm {
    public static $table = 'black_lists';
    public static $pk = 'id';
    public static $alias = 'bls';

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
            User::getClass() => 'user_id',
            User::getClass() => 'blocked_user_id'
        );
    }
}