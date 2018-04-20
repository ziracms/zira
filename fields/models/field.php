<?php
/**
 * Zira project.
 * field.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Zira\Orm;

class Field extends Orm {
    public static $table = 'field_items';
    public static $pk = 'id';
    public static $alias = 'fld_itm';
    
    protected static $_types = array(
        'input' => 'Text field',
        'textarea' => 'Text area',
        'select' => 'Select dropdown',
        'multiple' => 'Multiple select',
        'radio' => 'Radio button',
        'checkbox' => 'Check box',
        'file' => 'File',
        'image' => 'Image',
        'link' => 'URL address',
        'html' => 'HTML code'
    );
    
    public static function getFields() {
        return array(
            'id',
            'field_group_id',
            'field_type',
            'field_values',
            'title',
            'description',
            'sort_order',
            'active',
            'preview'
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
            Group::getClass() => 'field_group_id'
        );
    }
    
    public static function getTypes() {
        return self::$_types;
    }
    
    
    public static function getRecordFields($record_id, $category_id, $language, $preview=false) {
        $fields_select = array(
                        'field_id' => 'id',
                        'field_type' => 'field_type',
                        'field_values' => 'field_values',
                        'field_title' => 'title',
                        'field_description' => 'description',
                        'field_sort_order' => 'sort_order',
                        'field_preview' => 'preview'
                    );
        
        $groups_select = array(
                        'group_id' => 'id',
                        'group_title' => 'title',
                        'group_description' => 'description',
                        'group_placeholder' => 'placeholder',
                        'group_sort_order' => 'sort_order'
                    );
        
        $query = self::getCollection()
                    ->open_query()
                    ->select($fields_select)
                    ->join(Group::getClass(), $groups_select)
                    ->where('active','=',1,Group::getAlias())
                    ->and_where('category_id','=',Zira\Category::ROOT_CATEGORY_ID,Group::getAlias())
                    ->and_where('language','is',null,Group::getAlias())
                    ->and_where('active','=',1)
                    ;
        
        if ($preview) {
            $query->and_where('preview','=',1);
        }
        
        $query->close_query();
        
        $query->union()
                    ->open_query()
                    ->select($fields_select)
                    ->join(Group::getClass(), $groups_select)
                    ->where('active','=',1,Group::getAlias())
                    ->and_where('category_id','=',Zira\Category::ROOT_CATEGORY_ID,Group::getAlias())
                    ->and_where('language','=',$language,Group::getAlias())
                    ->and_where('active','=',1)
                    ;
        
        if ($preview) {
            $query->and_where('preview','=',1);
        }
        
        $query->close_query();
        
        if ($category_id != Zira\Category::ROOT_CATEGORY_ID) {
            $query->union()
                    ->open_query()
                    ->select($fields_select)
                    ->join(Group::getClass(), $groups_select)
                    ->where('active','=',1,Group::getAlias())
                    ->and_where('category_id','=',$category_id,Group::getAlias())
                    ->and_where('language','is',null,Group::getAlias())
                    ->and_where('active','=',1)
                    ;
            
            if ($preview) {
                $query->and_where('preview','=',1);
            }
        
            $query->close_query();
                    
            $query->union()
                    ->open_query()
                    ->select($fields_select)
                    ->join(Group::getClass(), $groups_select)
                    ->where('active','=',1,Group::getAlias())
                    ->and_where('category_id','=',$category_id,Group::getAlias())
                    ->and_where('language','=',$language,Group::getAlias())
                    ->and_where('active','=',1)
                    ;
        
            if ($preview) {
                $query->and_where('preview','=',1);
            }
        
            $query->close_query();
        }
        
        $query->merge()
                ->order_by('group_sort_order')
                ->order_by('field_sort_order')
            ;
        
        return $query->get();
    }
    
    public static function loadRecordFields($record_id, $category_id, $language, $preview=false) {
        $_fields = self::getRecordFields($record_id, $category_id, $language, $preview);
        $fields = array();
        foreach ($_fields as $field) {
            if (!array_key_exists($field->group_id, $fields)) {
                $fields[$field->group_id] = array(
                    'group' => array(
                        'id' => $field->group_id,
                        'title' => $field->group_title,
                        'description' => $field->group_description,
                        'placeholder' => $field->group_placeholder
                    ),
                    'fields' => array()
                );
            }
            $fields[$field->group_id]['fields'][] = $field;
        }
        return $fields;
    }
}