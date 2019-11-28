<?php
/**
 * Zira project.
 * subscription.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Models;

use Zira;
use Zira\Orm;

class Subscription extends Orm {
    public static $table = 'push_subscriptions';
    public static $pk = 'id';
    public static $alias = 'psbr';

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

        );
    }
}