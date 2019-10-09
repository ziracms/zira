<?php
/**
 * Zira project.
 * orm.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Db\Mysql;

abstract class Orm implements \Zira\Db\Implement\Orm {
    protected $_table;
    protected $_pk;
    protected $_alias;
    protected $_id;
    protected $_data = array();
    protected $_loaded = false;

    public function __construct($id = null) {
        $id = intval($id);
        $this->_table = DB_PREFIX . static::getTable();
        $this->_pk = static::getPk();
        $this->_alias = static::getAlias();
        $this->_id = $id>0 ? $id : null;
        if ($this->_id !== null) $this->load($this->_id);
    }

    public static function getClass() {
        return get_called_class();
    }

    public function load($id) {
        $stmt = Db::query('SELECT * FROM '.Db::escapeIdentifier($this->_table).' WHERE '.$this->_pk.'=?', array($id));
        $this->_data = Db::fetch($stmt, true);
        if ($this->_data===false) {
            $this->_loaded = false;
            $this->_data = array();
        } else {
            $this->_loaded = true;
        }
    }

    public function loadFromArray($arr) {
        if (array_key_exists($this->_pk, $arr)) {
            $this->_id = $arr[$this->_pk];
            $this->_loaded = true;
        } else {
            $this->_loaded = false;
        }
        $this->_data = $arr;
    }

    public function loaded() {
        return $this->_loaded;
    }

    public function save() {
        if ($this->_id === null) {
            $fields = array();
            $values = array();
            $data = array();
            foreach($this->_data as $field=>$value) {
                if ($field == $this->_pk) continue;
                $fields[]=Db::escapeIdentifier($field);
                $values[]='?';
                $data[]=$value;
            }
            $query = 'INSERT INTO '.Db::escapeIdentifier($this->_table).' ('.implode(', ',$fields).') VALUES ('.implode(', ',$values).')';
            Db::query($query, $data);
            $this->_id = Db::lastId();
            $this->_data[$this->_pk] = $this->_id;
        } else {
            $fields = array();
            $data = array();
            foreach($this->_data as $field=>$value) {
                if ($field == $this->_pk) continue;
                $data[]=$value;
                $fields[]=Db::escapeIdentifier($field).'=?';
            }
            $data[]=$this->_id;
            $query = 'UPDATE '.Db::escapeIdentifier($this->_table).' SET '.implode(', ',$fields).' WHERE '.$this->_pk.'=?';
            Db::query($query, $data);
        }
    }

    public function delete() {
        if ($this->_id === null) throw new \Exception('Cannot delete object');
        $query = 'DELETE FROM '.Db::escapeIdentifier($this->_table).' WHERE '.$this->_pk.'=?';
        Db::query($query, array($this->_id));
    }

    public function __get($var) {
        if (!isset($this->_data[$var])) return null;
        return $this->_data[$var];
    }

    public function __set($var, $val) {
        $this->_data[$var] = $val;
    }

    public static function getCollection() {
        return new Collection(get_called_class());
    }

    public static function getFirst($limit, $offset = null, array $where = null) {
        $collection = static::getCollection()->limit($limit, $offset);
        if ($where !== null) {
            $i=0;
            foreach($where as $field=>$value) {
                if ($i==0) {
                    $collection->where($field,'=',$value);
                } else {
                    $collection->and_where($field,'=',$value);
                }
                $i++;
            }
        }
        return $collection->order_by(null, 'ASC')->get();
    }

    public static function getLast($limit, $offset = null, array $where = null) {
        $collection = static::getCollection()->limit($limit, $offset);
        if ($where !== null) {
            $i=0;
            foreach($where as $field=>$value) {
                if ($i==0) {
                    $collection->where($field,'=',$value);
                } else {
                    $collection->and_where($field,'=',$value);
                }
                $i++;
            }
        }
        return $collection->order_by(null, 'DESC')->get();
    }

    public static function find($field, $word, $limit, $offset = null, array $where = null) {
        $collection = static::getCollection()->where(static::getAlias().'.'.$field, 'LIKE', '%'.$word.'%');
        if ($where !== null) {
            foreach($where as $field=>$value) {
                $collection->and_where($field,'=',$value);
            }
        }
        return $collection->limit($limit, $offset)->order_by(null, 'DESC')->get();
    }

    public function toArray() {
        return $this->_data;
    }
}