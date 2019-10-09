<?php
/**
 * Zira project.
 * record.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Models;

use Zira;
use Zira\Orm;

class Record extends Orm {
    public static $table = 'stat_records';
    public static $pk = 'id';
    public static $alias = 'st_r';

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
            Zira\Models\Record::getClass() => 'record_id',
            Zira\Models\Category::getClass() => 'category_id'
        );
    }
}