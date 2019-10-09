<?php
/**
 * Zira project.
 * group.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira\Cache;
use Zira\Locale;
use Zira\Orm;

class Group extends Orm {
    const CACHE_KEY = 'groups';
    const CACHE_ACTIVE_KEY = 'groups.active';
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

    public static $table = 'groups';
    public static $pk = 'id';
    public static $alias = 'grp';

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

    public static function getList() {
        $rows = Cache::getArray(self::CACHE_KEY);
        if ($rows === false) {
            $rows = self::getCollection()
                ->order_by(self::getPk(), 'ASC')
                ->get();

            Cache::setArray(self::CACHE_KEY, $rows);
        }
        return $rows;
    }

    public static function getActiveList() {
        $rows = Cache::getArray(self::CACHE_ACTIVE_KEY);
        if ($rows === false) {
            $rows = self::getCollection()
                ->where('active', '=', self::STATUS_ACTIVE)
                ->order_by(self::getPk(), 'ASC')
                ->get();

            Cache::setArray(self::CACHE_ACTIVE_KEY, $rows);
        }
        return $rows;
    }

    public static function getArray($activeOnly=false) {
        $groupsArr = array();
        if (!$activeOnly) {
            $groups = self::getList();
        } else {
            $groups = self::getActiveList();
        }
        foreach($groups as $group) {
            $groupsArr[$group->id] = Locale::t($group->name);
        }
        return $groupsArr;
    }
}