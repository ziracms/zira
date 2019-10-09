<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Menu extends Orm {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public static $table = 'menu_items';
    public static $pk = 'id';
    public static $alias = 'mni';

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
        return array();
    }

    public static function sortAsc($a, $b) {
        if ($a->sort_order == $b->sort_order) return 0;
        else return ($a->sort_order < $b->sort_order) ? -1 : 1;
    }

    public static function sortDesc($a, $b) {
        if ($a->sort_order == $b->sort_order) return 0;
        else return ($a->sort_order > $b->sort_order) ? -1 : 1;
    }
}