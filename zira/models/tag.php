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

    public static function getRecords($text, $limit = 10, $offset = 0) {
        $text = trim($text);
        if (empty($text)) return array();
        $text = mb_strtolower($text, CHARSET);
        
        $query = self::getCollection();
        $query->select('record_id');
        $query->where('tag','=',$text);
        $query->and_where('language','=',\Zira\Locale::getLanguage());
        $query->group_by('record_id');
        $query->order_by('id', 'ASC');
        $query->limit($limit, $offset);
        $rows = $query->get();
        
        $results = array();
        foreach($rows as $row) {
            $results[]=$row->record_id;
        }
        
        if (empty($results)) return array();
        
//        return Record::getCollection()
//                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
//                        ->left_join(Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
//                        ->where('id','in',$results)
//                        ->get();

        $query = Record::getCollection();
        foreach($results as $index=>$result) {
            if ($index>0) $query->union();
            $query->select('id', 'name', 'author_id', 'title', 'description', 'thumb', 'creation_date', 'rating', 'comments')
                    ->left_join(Category::getClass(), array('category_name' => 'name', 'category_title' => 'title'))
                    ->where('id', '=', $result);
        }
        
        return $query->get();
    }
}