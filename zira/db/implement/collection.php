<?php
/**
 * Zira project.
 * collection.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db\Implement;

interface Collection {
    /**
     * SELECT query
     * @param $args
     * @return Collection
     */
    public function select($args);

    /**
     * UPDATE query
     * @param array $fields
     * @return Collection
     */
    public function update(array $fields);

    /**
     * DELETE query
     * @return Collection
     */
    public function delete();

    /**
     * COUNT(*) query
     * @param string $alias
     * @return Collection
     */
    public function count($alias = 'co');

    /**
     * COUNT(field) query
     * @param string $alias
     * @return Collection
     */
    public function countField($field, $alias = 'co');

    /**
     * COUNT(DISTINCT field) query
     * @param string $alias
     * @return Collection
     */
    public function countDistinctField($field, $alias = 'co');

    /**
     * MAX() query
     * @param $field
     * @param string $alias
     * @return Collection
     */
    public function max($field, $alias = 'mx');

    /**
     * MIN() query
     * @param $field
     * @param string $alias
     * @return Collection
     */
    public function min($field, $alias = 'mn');

    /**
     * JOIN query
     * @param $class
     * @param array|null $select
     * @return Collection
     */
    public function join($class, array $select = null);

    /**
     * LEFT JOIN query
     * @param $class
     * @param array|null $select
     * @return Collection
     */
    public function left_join($class, array $select = null);

    /**
     * RIGHT JOIN query
     * @param $class
     * @param array|null $select
     * @return Collection
     */
    public function right_join($class, array $select = null);

    /**
     * LIMIT / OFFSET query
     * @param $limit
     * @param null $offset
     * @return Collection
     */
    public function limit($limit, $offset = null);

    /**
     * WHERE query
     * @param $field
     * @param $sign
     * @param $value
     * @param null $alias
     * @return Collection
     */
    public function where($field, $sign, $value, $alias = null);

    /**
     * AND WHERE query
     * @param null $field
     * @param null $sign
     * @param null $value
     * @param null $alias
     * @return Collection
     */
    public function and_where($field=null, $sign=null, $value=null, $alias = null);

    /**
     * OR WHERE query
     * @param null $field
     * @param null $sign
     * @param null $value
     * @param null $alias
     * @return Collection
     */
    public function or_where($field=null, $sign=null, $value=null, $alias = null);

    /**
     * Open parenthesis for WHERE query
     * @return Collection
     */
    public function open_where();

    /**
     * Close parenthesis after WHERE query
     * @return Collection
     */
    public function close_where();

    /**
     * Open parenthesis for UNION subquery
     * @return Collection
     */
    public function open_query();

    /**
     * Close parenthesis before UNION
     * @return Collection
     */
    public function close_query();

    /**
     * ORDER BY query
     * @param $field
     * @param null $order
     * @return Collection
     */
    public function order_by($field, $order = null);

    /**
     * ORDER BY RAND() query
     * @return Collection
     */
    public function random();

    /**
     * GROUP BY query
     * @param $field
     * @return Collection
     */
    public function group_by($field);

    /**
     * Renders SQL query
     * @return string
     */
    public function toString();

    /**
     * Executes rendered query and returns found rows
     * @param null $get
     * @param bool|false $as_array
     * @return mixed
     */
    public function get($get = null, $as_array=false);

    /**
     * Executes rendered query
     */
    public function execute();

    /**
     * Resets internal data for next query
     * @return Collection
     */
    public function reset();

    /**
     * UNION query
     * @return Collection
     */
    public function union();

    /**
     * SELECT * FROM (subquery)
     * @param string $alias
     * @return Collection
     */
    public function merge($alias = 'sub');

    /**
     * Get query data
     */
    public function getData();
    
    /**
     * Set query data
     * @param array $data
     */
    public function setData(array $data);
    
    /**
     * Magic method for toString()
     * @return string
     */
    public function __toString();

    /**
     * Returns rendered SQL string
     * @return string
     */
    public function debug();
}