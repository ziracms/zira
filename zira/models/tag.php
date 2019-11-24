<?php
/**
* Zira project.
* tag.php
* (c)2019 https://github.com/ziracms/zira
*/

namespace Zira\Models;

use Zira\Orm;

class Tag extends Orm {
    public static $table = 'tags';
    public static $pk = 'id';
    public static $alias = 'tg';
    
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
    
    public static function removeRecordTags($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }

    public static function addTags($record_id, $language, $tags) {
        self::removeRecordTags($record_id);
        self::getCollection()
            ->where('record_id', '=', $record_id)
            ->delete();

        foreach($tags as $tag) {
            $tagObj = new self();
            $tagObj->tag = $tag;
            $tagObj->language = $language;
            $tagObj->record_id = $record_id;
            $tagObj->save();
        }
    }
}