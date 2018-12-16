<?php
/**
 * Zira project.
 * visitor.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Models;

use Zira;
use Zira\Orm;

class Visitor extends Orm {
    public static $table = 'stat_visitors';
    public static $pk = 'id';
    public static $alias = 'st_v';

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
    
    public static function cleanUp() {
        self::getCollection()
                ->delete()
                ->where('access_day','<',date('Y-m-d', time()-2592000))
                ->execute();
    }
}