<?php
/**
 * Zira project.
 * table.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Db\Sqlite;

abstract class Table implements \Zira\Db\Implement\Table {
    protected $_table;
    protected $_charset = SQLITE_CHARSET;

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

    public function getCharset() {
        return $this->_charset;
    }

    public function setCharset($charset) {
        $this->_charset = $charset;
    }

    protected function _getCreateTableSQL() {
        $fields = array();
        foreach((array)$this->getFields() as $name=>$type) {
            $fields[]= Db::escapeIdentifier($name).' '.$type;
        }

        $sql = 'CREATE TABLE '.DB::escapeIdentifier($this->_table).' ( ';
        $sql .= implode(', ', $fields);
        $sql .= ' )';

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

    public function createIndexes() {
        foreach((array)$this->getKeys() as $name=>$keys) {
            $sql = $this->_getCreateIndexSQL($name, $keys);
            Db::query($sql);
        }
        foreach((array)$this->getUnique() as $name=>$keys) {
            $sql = $this->_getCreateIndexSQL($name, $keys, true);
            Db::query($sql);
        }
    }

    public function __toString() {
        $sql = $this->_getCreateTableSQL().';';
        foreach((array)$this->getKeys() as $name=>$keys) {
            $sql .= "\r\n". $this->_getCreateIndexSQL($name, $keys).';';
        }
        foreach((array)$this->getUnique() as $name=>$keys) {
            $sql .= "\r\n". $this->_getCreateIndexSQL($name, $keys, true).';';
        }
        return $sql;
    }

    public function install() {
        // setting encoding
        Db::query('PRAGMA encoding = "'.$this->getCharset().'"');
        // creating table
        $query = $this->_getCreateTableSQL();
        Db::query($query);
        // creating indexes
        $this->createIndexes();
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