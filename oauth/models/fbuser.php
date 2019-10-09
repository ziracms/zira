<?php
/**
 * Zira project.
 * fbuser.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Oauth\Models;

use Zira;
use Zira\Orm;

class Fbuser extends Orm {
    public static $table = 'fb_users';
    public static $pk = 'id';
    public static $alias = 'fb_usr';

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