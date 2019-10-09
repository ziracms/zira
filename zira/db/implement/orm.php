<?php
/**
 * Zira project.
 * orm.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Db\Implement;

interface Orm {
    /**
     * Returns class name
     * @return string
     */
    public static function getClass();

    /**
     * Returns table name without prefix
     * @return string
     */
    static function getTable();

    /**
     * Returns primary key
     * @return string
     */
    static function getPk();

    /**
     * Returns table unique alias
     * @return string
     */
    static function getAlias();

    /**
     * Returns table foreign keys
     * eg: array('reference_class_name' => 'table_foreign_key')
     * @return array
     */
    public static function getReferences();

    /**
     * Loads model from array
     * @param $arr
     */
    public function loadFromArray($arr);

    /**
     * Returns true if model is loaded
     * @return mixed
     */
    public function loaded();

    /**
     * Loads model from db
     * @param $id
     */
    public function load($id);

    /**
     * Updates row in db
     */
    public function save();

    /**
     * Deletes row from db
     */
    public function delete();

    /**
     * Getter method
     * @param $var
     * @return mixed
     */
    public function __get($var);

    /**
     * Setter method
     * @param $var
     * @param $val
     */
    public function __set($var, $val);

    /**
     * Returns Collection object
     * @return Collection
     */
    public static function getCollection();

    /**
     * Finds first rows in collection
     * @param $limit
     * @param null $offset
     * @param array|null $where
     * @return mixed
     */
    public static function getFirst($limit, $offset = null, array $where = null);

    /**
     * Finds last rows in collection
     * @param $limit
     * @param null $offset
     * @param array|null $where
     * @return mixed
     */
    public static function getLast($limit, $offset = null, array $where = null);

    /**
     * Search rows in collection
     * @param $field
     * @param $word
     * @param $limit
     * @param null $offset
     * @param array|null $where
     * @return mixed
     */
    public static function find($field, $word, $limit, $offset = null, array $where = null);

    /**
     * Returns model data
     * @return array
     */
    public function toArray();
}