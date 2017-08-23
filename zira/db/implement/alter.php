<?php
/**
 * Zira project.
 * alter.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Db\Implement;

interface Alter {
    /**
     * Returns table name
     * @return string
     */
    public function getName();

    /**
     * Returns table fields to be added
     * @return array
     */
    public function getFieldsToAdd();

    /**
     * Returns table indexes to be added
     * @return array
     */
    public function getKeysToAdd();
    
    /**
     * Returns table indexes to be dropped
     * @return array
     */
    public function getKeysToDrop();

    /**
     * Returns table unique indexes to be added
     * @return array
     */
    public function getUniqueToAdd();

    /**
     * Returns table rows to be added
     * @return array
     */
    public function getValues();

    /**
     * Returns alter SQL
     * @return string
     */
    public function __toString();

    /**
     * Apply modifications
     */
    public function execute();
}