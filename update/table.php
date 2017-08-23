<?php
/**
 * Zira project.
 * table.php
 * (c)2017 http://dro1d.ru
 */

namespace Update;

abstract class Table {
    protected $_table;

    public function __construct($table_name) {
        $this->_table = DB_PREFIX . $table_name;
    }
    
    public function getFields() {
        return array();
    }

    public function getKeys() {
        return array();
    }

    public function getUnique() {
        return array();
    }

    public function getValues() {
        return array();
    }

    public function __toString() {
        $primary = null;
        $fields = array();
        foreach((array)$this->getFields() as $name=>$type) {
            if ($type == Field::primary()) $primary = $name;
            $fields[]= Db::escapeIdentifier($name).' '.$type;
        }
        if ($primary!==null) {
            $fields[]='PRIMARY KEY ('.Db::escapeIdentifier($primary).')';
        }
        foreach((array)$this->getKeys() as $name=>$keys) {
            $index='KEY '.Db::escapeIdentifier($name).' ( ';
            if (is_string($keys)) $index .= Db::escapeIdentifier($keys);
            else if (is_array($keys)) {
                for($i=0; $i<count($keys); $i++) {
                    $keys[$i]= Db::escapeIdentifier($keys[$i]);
                }
                $index.=implode(', ',$keys);
            }
            $index .= ' )';
            $fields[]=$index;
        }
        foreach((array)$this->getUnique() as $name=>$keys) {
            $index='UNIQUE KEY '.Db::escapeIdentifier($name).' ( ';
            if (is_string($keys)) $index .= Db::escapeIdentifier($keys);
            else if (is_array($keys)) {
                for($i=0; $i<count($keys); $i++) {
                    $keys[$i]= Db::escapeIdentifier($keys[$i]);
                }
                $index.=implode(', ',$keys);
            }
            $index .= ' )';
            $fields[]=$index;
        }

        $sql = 'CREATE TABLE '.DB::escapeIdentifier($this->_table).' ( ';
        $sql .= implode(', ', $fields);
        $sql .= ' ) ENGINE='.$this->getEngine().' DEFAULT CHARSET='.$this->getCharset();

        return $sql;
    }

    public function install() {
        // creating table
        $query = (string)$this;
        Db::query($query);
        // inserting default values
        foreach((array)$this->getDefaults() as $insert) {
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
    }

    public function uninstall() {
        $query = 'DROP TABLE IF EXISTS '.DB::escapeIdentifier($this->_table);
        Db::query($query);
    }
}