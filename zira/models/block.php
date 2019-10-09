<?php
/**
 * Zira project.
 * block.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Block extends Orm {
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

    const WIDGET_CLASS = '\Zira\Widgets\Block';

    public static $table = 'blocks';
    public static $pk = 'id';
    public static $alias = 'blk';

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
}