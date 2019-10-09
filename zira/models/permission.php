<?php
/**
 * Zira project.
 * permission.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Orm;

class Permission extends Orm {
    public static $table = 'permissions';
    public static $pk = 'id';
    public static $alias = 'prm';

    const CUSTOM_PERMISSIONS_GROUP = 'custom';

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
            Group::getClass() => 'group_id'
        );
    }

    public static function getGroupPermissions($group_id) {
        return self::getCollection()
            ->select('name', 'allow')
            ->join(Group::getClass(), array('group_name'=>'name'))
            ->where('group_id','=',$group_id)
            ->and_where('active','=',Group::STATUS_ACTIVE, Group::getAlias())
            ->get()
            ;
    }
}