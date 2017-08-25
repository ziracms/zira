<?php
/**
 * Zira project.
 * file.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class File extends Orm {
    public static $table = 'files';
    public static $pk = 'id';
    public static $alias = 'fls';

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
    
    public static function removeRecordFiles($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
}