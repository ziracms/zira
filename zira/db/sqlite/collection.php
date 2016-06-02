<?php
/**
 * Zira project.
 * collection.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Db\Sqlite;

class Collection {
    protected $_class;
    protected $_table;
    protected $_pk;
    protected $_alias;
    protected $_references;
    protected $_fields = array();
    protected $_joins = array();
    protected $_left_joins = array();
    protected $_right_joins = array();
    protected $_ons = array();
    protected $_limit;
    protected $_offset;
    protected $_from = '';
    protected $_where = '';
    protected $_data = array();
    protected $_order_by = array();
    protected $_group_by = array();
    protected $_delete = false;
    protected $_update = array();
    protected $_update_data = array();
    protected $_query_prefix = '';
    protected $_query_suffix = '';
    protected $_union_query_opened = false;

    protected $_allowed_signs = array('=','<','>','<=','>=','<>','LIKE','NOT LIKE','IS','IS NOT','IN');

    public function __construct($class) {
        $this->validClass($class);
        $this->_class = $class;
        $this->_table = DB_PREFIX . $class::getTable();
        $this->_pk = $class::getPk();
        //$this->_alias = $class::getAlias();
        $this->_alias = $this->_table;
        $this->_references = $class::getReferences();
    }

    protected function validClass($class) {
        if (
            !method_exists($class,'getTable')  ||
            !method_exists($class,'getPk') ||
            !method_exists($class,'getAlias') ||
            !method_exists($class,'getReferences')
        ) {
            throw new \Exception('Orm class should be passed');
        }
    }

    public function select($args) {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        if (!empty($args)) {
            foreach($args as $alias=>$arg) {
                if (is_int($alias)) $alias = $arg;
                $this->_fields []= Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($arg).' as '.Db::escapeIdentifier($alias);
            }
        }
        return $this;
    }

    public function update(array $fields) {
        foreach($fields as $field=>$value) {
//            $alias = $this->_alias;
//            if (is_int($field) && is_array($value) && (count($value)==2 || count($value)==3)) {
//                if (count($value)==3) $alias = $value[2];
//                $field = $value[0];
//                $value = $value[1];
//            }
            $this->_update []= Db::escapeIdentifier($field). ' = ?';
            $this->_update_data []= $value;
        }
        return $this;
    }

    public function delete() {
        $this->_delete = true;
        return $this;
    }

    public function count($alias = 'co') {
        $this->_fields []= 'COUNT(*) as '.Db::escapeIdentifier($alias);
        return $this;
    }

    public function max($field, $alias = 'mx') {
        $_alias = $this->_alias;
        if (is_array($field) && count($field)==2) {
            $_alias = $field[0];
            $field = $field[1];
        }
        $this->_fields []= 'MAX('.Db::escapeIdentifier($_alias).'.'.Db::escapeIdentifier($field).') as '.Db::escapeIdentifier($alias);
        return $this;
    }

    public function min($field, $alias = 'mn') {
        $_alias = $this->_alias;
        if (is_array($field) && count($field)==2) {
            $_alias = $field[0];
            $field = $field[1];
        }
        $this->_fields []= 'MIN('.Db::escapeIdentifier($_alias).'.'.Db::escapeIdentifier($field).') as '.Db::escapeIdentifier($alias);
        return $this;
    }

    public function join($class, array $select = null) {
        $this->validClass($class);
        $this->_joins[$class]=array(
            'table' => DB_PREFIX . $class::getTable(),
            'pk' => $class::getPk(),
            'alias' => $class::getAlias(),
            'references' => $class::getReferences()
        );
        if (!empty($select)) {
            foreach($select as $alias=>$arg) {
                if (is_int($alias)) $alias = $arg;
                $this->_fields []= Db::escapeIdentifier($class::getAlias()).'.'.Db::escapeIdentifier($arg).' as '.Db::escapeIdentifier($alias);
            }
        }
        return $this;
    }

    public function left_join($class, array $select = null) {
        $this->validClass($class);
        $this->_left_joins[$class]=array(
            'table' => DB_PREFIX . $class::getTable(),
            'pk' => $class::getPk(),
            'alias' => $class::getAlias(),
            'references' => $class::getReferences()
        );
        if (!empty($select)) {
            foreach($select as $alias=>$arg) {
                if (is_int($alias)) $alias = $arg;
                $this->_fields []= Db::escapeIdentifier($class::getAlias()).'.'.Db::escapeIdentifier($arg).' as '.Db::escapeIdentifier($alias);
            }
        }
        return $this;
    }

    public function right_join($class, array $select = null) {
        $this->validClass($class);
        $this->_right_joins[$class]=array(
            'table' => DB_PREFIX . $class::getTable(),
            'pk' => $class::getPk(),
            'alias' => $class::getAlias(),
            'references' => $class::getReferences()
        );
        if (!empty($select)) {
            foreach($select as $alias=>$arg) {
                if (is_int($alias)) $alias = $arg;
                $this->_fields []= Db::escapeIdentifier($class::getAlias()).'.'.Db::escapeIdentifier($arg).' as '.Db::escapeIdentifier($alias);
            }
        }
        return $this;
    }

    public function limit($limit, $offset = null) {
        if ($this->_union_query_opened) return $this;
        $this->_limit = intval($limit);
        if ($offset !== null) $this->_offset = intval($offset);
        return $this;
    }

    public function where($field, $sign, $value, $alias = null) {
        if ($alias === null) $alias = $this->_alias;
        if ($field === null) $field = $this->_pk;
        $sign = strtoupper($sign);
        if (!in_array($sign, $this->_allowed_signs)) {
            throw new \Exception('Invalid sign passed');
        }
        if ($value !== null) {
            if (!is_array($value)) {
                $this->_where .= Db::escapeIdentifier($alias) . '.' . Db::escapeIdentifier($field) . ' ' . $sign . ' ?';
                $this->_data[] = $value;
            } else {
                $this->_where .= Db::escapeIdentifier($alias) . '.' . Db::escapeIdentifier($field) . ' ' . $sign . ' ';
                $_sign = '';
                $co = 0;
                foreach($value as $_value) {
                    if ($co>0) $_sign .= ',';
                    $_sign .= '? ';
                    $this->_data[] = $_value;
                    $co++;
                }
                $this->_where .= '('.$_sign.')';
            }
        } else {
            $this->_where.=Db::escapeIdentifier($alias).'.'.Db::escapeIdentifier($field).' '.$sign.' NULL';
        }
        return $this;
    }

    public function and_where($field=null, $sign=null, $value=null, $alias = null) {
        $this->_where.=' AND ';
        if ($field!==null && $sign!==null) {
            return $this->where($field, $sign, $value, $alias);
        }
        return $this;
    }

    public function or_where($field=null, $sign=null, $value=null, $alias = null) {
        $this->_where.=' OR ';
        if ($field!==null && $sign!==null) {
            return $this->where($field, $sign, $value, $alias);
        }
        return $this;
    }

    public function open_where() {
        $this->_where.=' ( ';
        return $this;
    }

    public function close_where() {
        $this->_where.=' ) ';
        return $this;
    }

    public function open_query() {
        $this->_union_query_opened = true;
        //$this->_query_prefix.=' ( ';
        return $this;
    }

    public function close_query() {
        //$this->_query_suffix.=' ) ';
        return $this;
    }

    public function order_by($field, $order = null) {
        if ($this->_union_query_opened) return $this;
        if ($field === null) $field = $this->_pk;
        if ($order !== null) $order = strtoupper($order);
        if ($order != 'DESC') $order = 'ASC';
        $this->_order_by[$field]= $order;
        return $this;
    }

    public function random() {
        $this->_order_by['RANDOM()']= 'RANDOM()';
        return $this;
    }

    public function group_by($field) {
        if ($this->_union_query_opened) return $this;
        if ($field === null) $field = $this->_pk;
        $this->_group_by[]=$field;
        return $this;
    }

    public function toString() {
        $query = $this->_query_prefix;
        if (!empty($this->_update)) {
            $query .= 'UPDATE ';
            $query .= Db::escapeIdentifier($this->_table);
            $query .= ' SET ' . implode(', ', $this->_update);
        } else if ($this->_delete) {
            $query .= 'DELETE FROM ';
            if (empty($this->_from)) $query .= Db::escapeIdentifier($this->_table);
            else $query .= $this->_from;
        } else {
            $query .= 'SELECT ';
            if (empty($this->_fields)) $query .= '*';
            else $query .= implode(', ', $this->_fields);
            $query .= ' FROM ';
            if (empty($this->_from)) $query .= Db::escapeIdentifier($this->_table) . ' AS ' . Db::escapeIdentifier($this->_alias);
            else $query .= $this->_from;
        }
        $joined = array();
        if (!empty($this->_joins)) {
            foreach($this->_joins as $class=>$info) {
                $query .= ' LEFT JOIN '.Db::escapeIdentifier($info['table']).' AS '.Db::escapeIdentifier($info['alias']);
                if (is_array($info['references']) && isset($info['references'][$this->_class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_pk).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['references'][$this->_class]);
                    $joined[]=Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['pk']);
                } else if (is_array($this->_references) && isset($this->_references[$class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_references[$class]).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['pk']);
                    $joined[]=Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['pk']);
                } else {
                    throw new \Exception('Failed to join class '.$class);
                }
            }
        }
        if (!empty($this->_left_joins)) {
            foreach($this->_left_joins as $class=>$info) {
                $query .= ' LEFT JOIN '.Db::escapeIdentifier($info['table']).' AS '.Db::escapeIdentifier($info['alias']);
                if (is_array($info['references']) && isset($info['references'][$this->_class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_pk).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['references'][$this->_class]);
                } else if (is_array($this->_references) && isset($this->_references[$class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_references[$class]).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['pk']);
                } else {
                    throw new \Exception('Failed to join class '.$class);
                }
            }
        }
        if (!empty($this->_right_joins)) {
            foreach($this->_right_joins as $class=>$info) {
                $query .= ' RIGHT JOIN '.Db::escapeIdentifier($info['table']).' AS '.Db::escapeIdentifier($info['alias']);
                if (is_array($info['references']) && isset($info['references'][$this->_class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_pk).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['references'][$this->_class]);
                } else if (is_array($this->_references) && isset($this->_references[$class])) {
                    $query .= ' ON '.Db::escapeIdentifier($this->_alias).'.'.Db::escapeIdentifier($this->_references[$class]).' = '.Db::escapeIdentifier($info['alias']).'.'.Db::escapeIdentifier($info['pk']);
                } else {
                    throw new \Exception('Failed to join class '.$class);
                }
            }
        }
        $where = '';
        if (!empty($this->_where)) {
            $where = ' WHERE ('.$this->_where.')';
        }
        if (!empty($joined)) {
            foreach($joined as $field) {
                if (empty($where)) {
                    $where .= ' WHERE '.$field.' IS NOT NULL';
                } else {
                    $where .= ' AND '.$field.' IS NOT NULL';
                }
            }
        }
        $query .= $where;
        if (!empty($this->_group_by)) {
            $_group = '';
            foreach($this->_group_by as $field) {
                if (!empty($_group)) $_group.=', ';
                $_group.=Db::escapeIdentifier($field);
            }
            $query .= ' GROUP BY '.$_group;
        }
        if (!empty($this->_order_by)) {
            $_order = '';
            foreach($this->_order_by as $field=>$order) {
                if (!empty($_order)) $_order.=', ';
                if ($field == 'RANDOM()') {
                    $_order.=$field;
                } else {
                    $_order.=Db::escapeIdentifier($field).' '.$order;
                }
            }
            $query .= ' ORDER BY '.$_order;
        }
        if ($this->_limit !== null) {
            $query .= ' LIMIT '.$this->_limit;
        }
        if ($this->_offset !== null) {
            $query .= ' OFFSET '.$this->_offset;
        }
        $query .= $this->_query_suffix;
        return $query;
    }

    public function get($get = null, $as_array=false) {
        $stmt = DB::query($this->toString(), $this->_data);
        $result = array();
        while ($row=Db::fetch($stmt, $as_array)) {
            $result[]=$row;
        }
        Db::free($stmt);
        if ($get === null) {
            return $result;
        } else if (is_int($get) && isset($result[$get])) {
            return $result[$get];
        } else if (is_string($get) && count($result)>0) {
            return $result[0]->{$get};
        } else {
            return null;
        }
    }

    public function execute() {
        DB::query($this->toString(), array_merge($this->_update_data,$this->_data));
    }

    public function reset() {
        $this->_fields = array();
        $this->_joins = array();
        $this->_ons = array();
        $this->_limit = null;
        $this->_offset = null;
        $this->_where = '';
        $this->_data = array();
        $this->_order_by = array();
        $this->_group_by = array();
        $this->_delete = false;
        $this->_update = array();
        $this->_update_data = array();
        $this->_query_prefix = '';
        $this->_query_suffix = '';
        return $this;
    }

    public function union() {
        $query = $this->toString();
        $data = $this->_data;
        $this->reset();
        $this->_data = $data;
        $this->_query_prefix = $query . ' UNION ';
        $this->_union_query_opened = false;
        return $this;
    }

    public function merge($alias = 'sub') {
        $query = $this->toString();
        $data = $this->_data;
        $this->reset();
        $this->_data = $data;
        $this->_from = '(' . $query . ') AS ' . Db::escapeIdentifier($alias);
        $this->_union_query_opened = false;
        return $this;
    }

    public function __toString() {
        return $this->toString();
    }

    public function debug() {
        return vsprintf(str_replace('?','\'%s\'',$this->toString()), $this->_data);
    }
}