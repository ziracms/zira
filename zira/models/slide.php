<?php
/**
 * Zira project.
 * slide.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Slide extends Orm {
    public static $table = 'slides';
    public static $pk = 'id';
    public static $alias = 'sld';

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
            Record::getClass() => 'record_id'
        );
    }
}