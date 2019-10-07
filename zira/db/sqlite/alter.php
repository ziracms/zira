<?php
/**
 * Zira project.
 * alter.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Db\Sqlite;

abstract class Alter implements \Zira\Db\Implement\Alter {
    protected $_table;

    public function __construct($table_name) {
        $this->_table = DB_PREFIX . $table_name;
    }
    
    public function getName() {
        return $this->_table;
    }

    public function getFieldsToAdd() {
        return array();
    }

    // not supported by sqlite
    public function getFieldsToChange() {
        return array();
    }

    public function getKeysToAdd() {
        return array();
    }
    
    public function getKeysToDrop() {
        return array();
    }

    public function getUniqueToAdd() {
        return array();
    }

    public function getValues() {
        return array();
    }
    
    protected function _getAddFieldSQL($name, $type) {
        $sql = 'ALTER TABLE '.DB::escapeIdentifier($this->_table).' ADD '.Db::escapeIdentifier($name).' '.$type;

        if ($type == Field::FIELD_TYPE_INT_NOT_NULL) $sql .= ' DEFAULT 0';
        else if ($type == Field::FIELD_TYPE_TEXT_NOT_NULL) $sql .= ' DEFAULT \'\'';
        
        return $sql;
    }

    protected function _getCreateIndexSQL($name, $keys, $unique = false) {
        $index=' ( ';
        if (is_string($keys)) $index .= Db::escapeIdentifier($keys);
        else if (is_array($keys)) {
            for($i=0; $i<count($keys); $i++) {
                $keys[$i]= Db::escapeIdentifier($keys[$i]);
            }
            $index.=implode(', ',$keys);
        }
        $index .= ' )';
        $sql = 'CREATE ';
        if ($unique) $sql .= 'UNIQUE ';
        $sql .='INDEX '.Db::escapeIdentifier($this->_table.'_'.$name).' ON '.DB::escapeIdentifier($this->_table).$index;

        return $sql;
    }
    
    protected function _getDropIndexSQL($name) {
        $sql ='DROP INDEX '.Db::escapeIdentifier($this->_table.'_'.$name);

        return $sql;
    }

    public function createIndexes() {
        foreach((array)$this->getKeysToAdd() as $name=>$keys) {
            $sql = $this->_getCreateIndexSQL($name, $keys);
            Db::query($sql);
        }
        foreach((array)$this->getUniqueToAdd() as $name=>$keys) {
            $sql = $this->_getCreateIndexSQL($name, $keys, true);
            Db::query($sql);
        }
    }
    
    public function dropIndexes() {
        foreach((array)$this->getKeysToDrop() as $name) {
            $sql = $this->_getDropIndexSQL($name);
            Db::query($sql);
        }
    }

    public function __toString() {
        $sql = '';
        foreach((array)$this->getKeysToDrop() as $name) {
            $sql .= $this->_getDropIndexSQL($name).';'."\r\n";
        }
        foreach((array)$this->getFieldsToAdd() as $name=>$type) {
            $sql .= $this->_getAddFieldSQL($name, $type).';'."\r\n";
        }
        foreach((array)$this->getKeysToAdd() as $name=>$keys) {
            $sql .= $this->_getCreateIndexSQL($name, $keys).';'."\r\n";
        }
        foreach((array)$this->getUniqueToAdd() as $name=>$keys) {
            $sql .= $this->_getCreateIndexSQL($name, $keys, true).';'."\r\n";
        }
        return $sql;
    }

    public function execute() {
        // checking if table is exists
        $tables = Db::getTables();
        if (!in_array($this->_table, $tables)) return false;
        // dropping indexes
        $this->dropIndexes();
        // adding fields
        foreach((array)$this->getFieldsToAdd() as $name=>$type) {
            $query = $this->_getAddFieldSQL($name, $type);
            Db::query($query);
        }
        // creating indexes
        $this->createIndexes();
        // inserting default values
        foreach((array)$this->getValues() as $insert) {
            $fields = array();
            $values = array();
            $data = array();
            foreach($insert as $field=>$value) {
                $fields[]=Db::escapeIdentifier($field);
                $values[]='?';
                $data[]=$value;
            }
            $query = 'INSERT INTO '.Db::escapeIdentifier($this->_table).' ('.implode(', ',$fields).') VALUES ('.implode(', ',$values).')';
            Db::query($query, $data);
        }
        return true;
    }
}