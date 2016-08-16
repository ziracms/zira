<?php
/**
 * Zira project.
 * Featured.php
 * (c)2016 http://dro1d.ru
 */

namespace Featured\Models;

use Zira;
use Zira\Orm;

class Featured extends Orm {
    public static $table = 'featured_records';
    public static $pk = 'id';
    public static $alias = 'feat_rcd';

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
            Zira\Models\Record::getClass() => 'record_id'
        );
    }
}