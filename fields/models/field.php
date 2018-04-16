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
        'checkbox' => 'Check box',
        'radio' => 'Radio button',
        'select' => 'Select dropdown',
        'file' => 'File',
        'image' => 'Image',
        'link' => 'URL address',
        'html' => 'HTML code'
    );

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

        );
    }
    
    public static function getTypes() {
        return self::$_types;
    }
}