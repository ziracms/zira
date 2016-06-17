<?php
/**
 * Zira project.
 * eformfield.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Models;

use Zira;
use Zira\Orm;

class Eformfield extends Orm {
    public static $table = 'eform_fields';
    public static $pk = 'id';
    public static $alias = 'efm_fld';

    protected static $_types = array(
        'email' => 'Email address',
        'input' => 'Text field',
        'textarea' => 'Text area',
        'datepicker' => 'Date picker',
        'checkbox' => 'Check box',
        'radio' => 'Radio button',
        'select' => 'Select dropdown',
        'file' => 'File'
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
            Eform::getClass() => 'eform_id'
        );
    }

    public static function getTypes() {
        return self::$_types;
    }
}