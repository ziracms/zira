<?php
/**
 * Zira project.
 * audio.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Audio extends Orm {
    public static $table = 'audio';
    public static $pk = 'id';
    public static $alias = 'aud';

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
    
    public static function removeRecordAudio($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
}