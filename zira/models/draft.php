<?php
/**
 * Zira project.
 * draft.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Draft extends Orm {
    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;

    public static $table = 'drafts';
    public static $pk = 'id';
    public static $alias = 'drt';

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
            Record::getClass() => 'record_id',
            User::getClass() => 'author_id'
        );
    }

    public static function cleanUp() {
        self::getCollection()
            ->delete()
            ->where('published','=',self::STATUS_PUBLISHED)
            ->and_where('modified_date','<',date('Y-m-d H:i:s',time()-2592000)) // 30 days
            ->execute();
    }
    
    public static function removeRecordDrafts($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
}