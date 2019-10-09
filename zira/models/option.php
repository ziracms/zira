<?php
/**
 * Zira project.
 * option.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Cache;
use Zira\Config;
use Zira\Orm;

class Option extends Orm {
    public static $table = 'options';
    public static $pk = 'id';
    public static $alias = 'opt';

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

    public static function convertArrayToString(array $value) {
        return '['.implode(',',$value).']';
    }

    public static function convertStringToArray($value) {
        $values = array();
        if (preg_match('/^\[(.+)\]$/',$value,$matches)) {
            $values = explode(',',$matches[1]);
        }
        return $values;
    }

    public static function write($name, $value) {
        $id = self::getCollection()
                ->select('id')
                ->where('name','=',$name)
                ->get('id');
        if (empty($id)) {
            $obj = new self();
        } else {
            $obj = new self($id);
        }

        $obj->module = 'zira';
        $obj->name = $name;
        $obj->value = $value;
        $obj->save();
    }

    public static function raiseVersion() {
        self::write('config_version', Config::get('config_version')+1);
        Cache::clear(true);
    }
}