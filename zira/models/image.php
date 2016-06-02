<?php
/**
 * Zira project.
 * image.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Image extends Orm {
    public static $table = 'images';
    public static $pk = 'id';
    public static $alias = 'img';

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