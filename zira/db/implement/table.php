<?php
/**
 * Zira project
 * table.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db\Implement;

interface Table {
    /**
     * Returns table name
     * @return string
     */
    public function getName();

    /**
     * Returns table fields
     * @return array
     */
    public function getFields();

    /**
     * Returns table indexes
     * @return array
     */
    public function getKeys();

    /**
     * Returns table unique indexes
     * @return array
     */
    public function getUnique();

    /**
     * Returns table default rows
     * @return array
     */
    public function getDefaults();

    /**
     * Returns table SQL
     * @return string
     */
    public function __toString();

    /**
     * Creates table in db
     */
    public function install();

    /**
     * Drops table from db
     */
    public function uninstall();

    /**
     * Returns table dump
     * @param $delimiter
     * @return string
     */
    public function dump($delimiter);
}