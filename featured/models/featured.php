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

    public static function getRecords() {
        return Zira\Models\Record::getCollection()
                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
                        ->join(\Featured\Models\Featured::getClass(), array('featured_id' => 'id', 'featured_sort_order' => 'sort_order'))
                        //->join(Zira\Models\User::getClass(), array('author_username'=>'username', 'author_firstname'=>'firstname', 'author_secondname'=>'secondname'))
                        ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ->where('language', '=', Zira\Locale::getLanguage())
                        ->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED)
                        ->order_by('featured_sort_order', 'asc')
                        ->get();
    }
}