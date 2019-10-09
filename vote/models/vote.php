<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Vote\Models;

use Zira;
use Zira\Orm;

class Vote extends Orm {
    const WIDGET_CLASS = '\Vote\Widgets\Vote';

    public static $table = 'votes';
    public static $pk = 'id';
    public static $alias = 'vot';

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