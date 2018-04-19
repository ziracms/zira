<?php
/**
 * Zira project.
 * value.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Zira\Orm;

class Value extends Orm {
    public static $table = 'field_values';
    public static $pk = 'id';
    public static $alias = 'fld_val';

    public static function getFields() {
        return array(
            'id',
            'record_id',
            'field_item_id',
            'field_group_id',
            'content',
            'mark',
            'date_added'
        );
    }
    
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
            Field::getClass() => 'field_item_id',
            Group::getClass() => 'field_group_id'
        );
    }
    
    public static function clearRecordValues($record_id, array $field_ids = null) {
        $query = self::getCollection()
                    ->delete()
                    ->where('record_id', '=', $record_id)
                    ;
        
        if ($field_ids !== null) {
            $query->and_where('field_item_id', 'in', $field_ids);
        }
        
        return $query->execute();
    }
    
    public static function getRecordValues($record_id, array $field_ids = null) {
        $query = self::getCollection()
                    ->where('record_id', '=', $record_id)
                    ;
        
        if ($field_ids !== null) {
            $query->and_where('field_item_id', 'in', $field_ids);
        }
        
        return $query->get();
    }
    
    public static function loadRecordValues($record_id) {
        $values = array();
        $_values = self::getRecordValues($record_id);
        foreach($_values as $value) {
            $values[$value->field_item_id] = $value;
        }
        return $values;
    }
}