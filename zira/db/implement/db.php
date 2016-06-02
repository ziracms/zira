<?php
/**
 * Zira project.
 * db.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db\Implement;

interface Db {
    /**
     * Opens connection
     */
    public static function open();

    /**
     * Closes connection
     */
    public static function close();

    /**
     * Executes sql query
     * @param $query
     * @param array|null $params
     * @return object
     */
    public static function query($query, array $params = null);

    /**
     * Fetches rows
     * @param $stmt
     * @param bool|false $as_array
     * @return mixed
     */
    public static function fetch($stmt, $as_array = false);

    /**
     * Frees up result
     * @param $stmt
     */
    public static function free($stmt);

    /**
     * Returns last inserted id
     * @return int
     */
    public static function lastId();

    /**
     * Begin transaction
     * @return mixed
     */
    public static function begin();

    /**
     * Commit transaction
     * @return mixed
     */
    public static function commit();

    /**
     * Rollback transaction
     * @return mixed
     */
    public static function rollback();

    /**
     * Escapes string
     * @param $field
     * @return mixed
     */
    public static function escape($field);

    /**
     * Escapes identifier
     * @param $identifier
     * @return mixed
     */
    public static function escapeIdentifier($identifier);

    /**
     * Returns total number of executed queries
     * @return int
     */
    public static function getTotal();

    /**
     * Returns db type and version
     * @return string
     */
    public static function version();

    /**
     * Returns array with all existing tables
     * @return array
     */
    public static function getTables();
}