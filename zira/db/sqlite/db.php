<?php
/**
 * Zira project.
 * db.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Db\Sqlite;

use PDO;

class Db implements \Zira\Db\Implement\Db {
    protected static $_db;
    protected static $_total = 0;

    public static function open() {
        $dsn = 'sqlite:'.DB_FILE;

        static::$_db = new PDO($dsn, null, null);
        static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        return '"'.$identifier.'"';
    }

    public static function getTotal() {
        return self::$_total;
    }

    public static function version() {
        return static::$_db->getAttribute(PDO::ATTR_DRIVER_NAME).' '.static::$_db->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public static function getTables() {
        $stmt = self::query("SELECT name FROM sqlite_master WHERE type='table'");
        $result = array();
        while ($row=Db::fetch($stmt, true)) {
            $result[]=array_values($row)[0];
        }
        Db::free($stmt);
        return $result;
    }
}