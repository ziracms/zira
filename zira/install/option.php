<?php
/**
 * Zira project.
 * option.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Option extends Table {
    protected $_table = 'options';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'module' => Field::string(true),
            'name' => Field::string(true),
            'value' => Field::string(true)
        );
    }

    public function getUnique() {
        return array(
            'name' => array('name')
        );
    }
}