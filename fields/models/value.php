<?php
/**
 * Zira project.
 * value.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Zira\Orm;

class Value extends Orm {
    public static $table = 'field_values';
    public static $pk = 'id';
    public static $alias = 'fld_val';

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