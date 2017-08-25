<?php
/**
 * Zira project.
 * video.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Video extends Orm {
    public static $table = 'videos';
    public static $pk = 'id';
    public static $alias = 'vid';

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
    
    public static function removeRecordVideos($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
}