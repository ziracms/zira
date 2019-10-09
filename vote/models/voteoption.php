<?php
/**
 * Zira project.
 * voteoption.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Vote\Models;

use Zira;
use Zira\Orm;

class Voteoption extends Orm {
    public static $table = 'vote_options';
    public static $pk = 'id';
    public static $alias = 'vot_opt';

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
            Vote::getClass() => 'vote_id'
        );
    }
}