<?php
/**
 * Zira project.
 * holiday.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Models;

use Zira;
use Zira\Orm;

class Holiday extends Orm {
    public static $table = 'holidays';
    public static $pk = 'id';
    public static $alias = 'hld';

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
    
    public static function getHolidays() {
        return self::getCollection()
                ->where('active', '=', 1)
                ->get();
    }
}