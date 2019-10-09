<?php
/**
 * Zira project.
 * table.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Db\Mysql;

abstract class Table implements \Zira\Db\Implement\Table {
    protected $_table;
    protected $_engine = 'InnoDB';
    protected $_charset = MYSQL_CHARSET;

    public function __construct($table_name) {
        $this->_table = DB_PREFIX . $table_name;
    }

    public function getName() {
        return $this->_table;
    }

    public function getKeys() {
        return array();
    }

    public function getUnique() {
        return array();
    }

    public function getDefaults() {
        return array();
    }

    public function getEngine() {
        return $this->_engine;
    }

    public function getCharset() {
        return $this->_charset;
    }

    public function setEngine($engine) {
        $this->_engine = $engine;
    }

    public function setCharset($charset) {
        $this->_charset = $charset;
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

    public function dump($delimiter, $limit=1000, $flush = false) {
        $offset = 0;
        $sql = '';
        do {
            $has_rows = false;
            $stmt = DB::query('SELECT * FROM '.DB::escapeIdentifier($this->_table).' LIMIT '.intval($limit).' OFFSET '.intval($offset));
            while ($row=Db::fetch($stmt,true)) {
                $has_rows = true;
                $columns = array_keys($row);
                for($i=0; $i<count($columns); $i++) {
                    $columns[$i] = DB::escapeIdentifier($columns[$i]);
                }
                $values = array_values($row);
                for($i=0; $i<count($values); $i++) {
                    if (is_null($values[$i])) {
                        $values[$i] = 'NULL';
                    } else {
                        $values[$i] = DB::escape($values[$i]);
                    }
                }
                $sql .= 'INSERT INTO '.DB::escapeIdentifier($this->_table).' ('.implode(', ',$columns).') VALUES ('.implode(', ',$values).');'.$delimiter;
            }
            Db::free($stmt);
            $offset += $limit;
            if ($flush) {
                echo $sql;
                flush();
                $sql = '';
            }
        } while($has_rows);
        if (!$flush) return $sql;
    }
}