<?php
/**
 * Zira project.
 * alter.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Db\Mysql;

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

    public function __toString() {
        $fields = array();
        
        foreach((array)$this->getKeysToDrop() as $name) {
            $fields[]='DROP KEY '.Db::escapeIdentifier($name);
        }

        foreach((array)$this->getFieldsToChange() as $name=>$type) {
            $fields[]= 'CHANGE '.Db::escapeIdentifier($name).' '.Db::escapeIdentifier($name).' '.$type;
        }
        
        foreach((array)$this->getFieldsToAdd() as $name=>$type) {
            $fields[]= 'ADD '.Db::escapeIdentifier($name).' '.$type;
        }
        
        foreach((array)$this->getKeysToAdd() as $name=>$keys) {
            $index='KEY '.Db::escapeIdentifier($name).' ( ';
            if (is_string($keys)) $index .= Db::escapeIdentifier($keys);
            else if (is_array($keys)) {
                for($i=0; $i<count($keys); $i++) {
                    $keys[$i]= Db::escapeIdentifier($keys[$i]);
                }
                $index.=implode(', ',$keys);
            }
            $index .= ' )';
            $fields[]='ADD '.$index;
        }
        
        foreach((array)$this->getUniqueToAdd() as $name=>$keys) {
            $index='UNIQUE KEY '.Db::escapeIdentifier($name).' ( ';
            if (is_string($keys)) $index .= Db::escapeIdentifier($keys);
            else if (is_array($keys)) {
                for($i=0; $i<count($keys); $i++) {
                    $keys[$i]= Db::escapeIdentifier($keys[$i]);
                }
                $index.=implode(', ',$keys);
            }
            $index .= ' )';
            $fields[]='ADD '.$index;
        }
        
        $sql = 'ALTER TABLE '.DB::escapeIdentifier($this->_table);
        $sql .= implode(', ', $fields);

        return $sql;
    }

    public function execute() {
        // checking if table is exists
        $tables = Db::getTables();
        if (!in_array($this->_table, $tables)) return false;
        // creating query
        $query = (string)$this;
        Db::query($query);
        // inserting new values
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