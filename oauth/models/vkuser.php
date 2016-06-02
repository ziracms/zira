<?php
/**
 * Zira project.
 * vkuser.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Models;

use Zira;
use Zira\Orm;

class Vkuser extends Orm {
    public static $table = 'vk_users';
    public static $pk = 'id';
    public static $alias = 'vk_usr';

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
}