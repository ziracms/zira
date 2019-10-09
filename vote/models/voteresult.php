<?php
/**
 * Zira project.
 * voteresult.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Vote\Models;

use Zira;
use Zira\Orm;

class Voteresult extends Orm {
    public static $table = 'vote_results';
    public static $pk = 'id';
    public static $alias = 'vot_rst';

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