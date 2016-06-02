<?php
/**
 * Zira project
 * mysql.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db\Mysql;

use PDO;

class Db {
    protected static $_db;
    protected static $_total = 0;

    public static function open() {
        $dsn = 'mysql'.
            ':host='.DB_HOST.
            ';port='.DB_PORT.
            ';dbname='.DB_NAME.
            ';charset='.MYSQL_CHARSET;

        static::$_db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        static::$_db->exec('SET NAMES '.MYSQL_CHARSET);
    }

    public static function close() {
        static::$_db = null;
    }

    public static function query($query, array $params = null) {
        if ($params === null) $params = array();
        $stmt = static::$_db->prepare($query);
        $stmt->execute($params);
        self::$_total++;
        return $stmt;
    }

    public static function fetch($stmt, $as_array = false) {
        if (!$as_array) {
            return $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public static function free($stmt) {
        $stmt->closeCursor();
    }

    public static function lastId() {
        return static::$_db->lastInsertId();
    }

    public static function begin() {
        return static::$_db->beginTransaction();
    }

    public static function commit() {
        return static::$_db->commit();
    }

    public static function rollback() {
        return static::$_db->rollback();
    }

    public static function escape($field) {
        return static::$_db->quote($field);
    }

    public static function escapeIdentifier($identifier) {
        return '`'.$identifier.'`';
    }

    public static function getTotal() {
        return self::$_total;
    }

    public static function version() {
        return static::$_db->getAttribute(PDO::ATTR_DRIVER_NAME).' '.static::$_db->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public static function getTables() {
        $stmt = self::query("SHOW TABLES");
        $result = array();
        while ($row=Db::fetch($stmt, true)) {
            $result[]=array_values($row)[0];
        }
        Db::free($stmt);
        return $result;
    }
}