<?php
/**
 * Zira project.
 * translate.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Translate extends Orm {
    public static $table = 'translates';
    public static $pk = 'id';
    public static $alias = 'trs';

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