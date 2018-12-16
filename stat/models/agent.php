<?php
/**
 * Zira project.
 * agent.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Models;

use Zira;
use Zira\Orm;

class Agent extends Orm {
    public static $table = 'stat_agents';
    public static $pk = 'id';
    public static $alias = 'st_a';

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
    
    public static function cleanUp() {
        self::getCollection()
                ->delete()
                ->where('access_day','<',date('Y-m-d', time()-2592000))
                ->execute();
    }
}