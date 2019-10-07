<?php
/**
 * Zira project.
 * search.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Fields;
use Zira\Orm;

class Search extends Orm {
    public static $table = 'field_search';
    public static $pk = 'id';
    public static $alias = 'fld_srch';
    
    const WIDGET_CLASS = '\Fields\Widgets\Search';
    const TYPE_SEARCH_OR = 'or';
    const TYPE_SEARCH_AND = 'and';
    
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
            Field::getClass() => 'field_item_id',
            Zira\Models\Record::getClass() => 'record_id'
        );
    }
    
    public static function getSearchTypes() {
        return array(
            self::TYPE_SEARCH_OR => Zira\Locale::tm('"or"', 'fields'),
            self::TYPE_SEARCH_AND => Zira\Locale::tm('"and"', 'fields')
        );
    }
    
    public static function clearRecordIndex($record_id) {
        self::getCollection()
            ->delete()
            ->where('record_id', '=', $record_id)
            ->execute();
    }

    public static function addRecordFieldIndex($language, $record_id, $field_id, $values, $published) {
        if ($values === null) $values = '';
        if (!is_array($values)) $values = array($values);
        foreach($values as $keyword) {
            if (mb_strlen($keyword, CHARSET)>255) $keyword = mb_substr($keyword, 0, 255, CHARSET);
            $index = new self();
            $index->field_item_id = $field_id;
            $index->keyword = $keyword;
            $index->record_id = $record_id;
            $index->language = $language;
            $index->published = $published;
            $index->save();
        }
    }
    
    public static function updateRecordIndex($record_id, $published) {
        self::getCollection()
            ->update(array(
                'published' => $published
            ))
            ->where('record_id', '=', $record_id)
            ->execute();
    }
    
    public static function searchRecords($values, $limit = 10, $offset = 0, $type = self::TYPE_SEARCH_OR) {
        if (empty($values)) return array();
        
        $query = self::getCollection();
        $query->select('record_id');
        
        if ($type == self::TYPE_SEARCH_OR) {
            $query->where('language','=',Zira\Locale::getLanguage());
            $query->and_where('published','=', Zira\Models\Record::STATUS_PUBLISHED);
            $query->and_where();
            $query->open_where();

            $co = 0;
            foreach ($values as $group_id=>$_values) {
                if (!is_array($_values)) continue;
                foreach($_values as $field_id=>$_value) {
                    if (!is_array($_value) || !isset($_value['value']) || !isset($_value['type'])) continue;
                    if ($co>0) {
                        $query->or_where();
                    }

                    $query->open_where()
                            ->where('field_item_id','=',$field_id)
                            ;

                    if (is_array($_value['value'])) {
                        $query->and_where('keyword', 'in', $_value['value']);
                    } else if (in_array($_value['type'], array('checkbox', 'select', 'radio', 'multiple'))) {
                        $query->and_where('keyword', '=', $_value['value']);
                    } else {
                        $query->and_where('keyword', 'like', $_value['value'].'%');
                    }

                    $query->close_where();
                    $co++;
                }
            }
            $query->close_where();
        } else if ($type == self::TYPE_SEARCH_AND) {
            $subqueries = array();
            $data = array();
            foreach ($values as $group_id=>$_values) {
                if (!is_array($_values)) continue;
                foreach($_values as $field_id=>$_value) {
                    if (!is_array($_value) || !isset($_value['value']) || !isset($_value['type'])) continue;

                    $subquery = self::getCollection();
                    $subquery->select('record_id');
                    $subquery->where('language','=',Zira\Locale::getLanguage());
                    $subquery->and_where('published','=', Zira\Models\Record::STATUS_PUBLISHED);
                    $subquery->and_where('field_item_id','=',$field_id);

                    if (is_array($_value['value'])) {
                        $subquery->and_where('keyword', 'in', $_value['value']);
                    } else if (in_array($_value['type'], array('checkbox', 'select', 'radio', 'multiple'))) {
                        $subquery->and_where('keyword', '=', $_value['value']);
                    } else {
                        $subquery->and_where('keyword', 'like', $_value['value'].'%');
                    }

                    $subqueries []= $subquery;
                    $data = array_merge($data, $subquery->getData());
                }
            }

            foreach($subqueries as $index=>$subquery) {
                if ($index==0) {
                    $query->where('record_id', 'in', $subquery);
                } else {
                    $query->and_where('record_id', 'in', $subquery);
                }
            }
            $query->setData($data);
        }

        $query->limit($limit, $offset);
        $query->group_by('record_id');

        $rows = $query->get();

        $results = array();
        foreach($rows as $row) {
            $results[]=$row->record_id;
        }

        if (empty($results)) return array();

        $query = Zira\Models\Record::getCollection();
        $query->select('id', 'name', 'author_id', 'title', 'description', 'thumb', 'creation_date', 'rating', 'comments', 'published')
                        ->left_join(Zira\Models\Category::getClass(), array('category_name' => 'name', 'category_title' => 'title'))
                        ->where('id', 'in', $results);

        return $query->get();
    }
}