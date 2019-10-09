<?php
/**
 * Zira project.
 * translates.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Translate extends Table {
    protected $_table = 'translates';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'module' => Field::string(true),
            'name' => Field::string(true),
            'value' => Field::string(true),
            'language' => Field::string(true)
        );
    }

    public function getUnique() {
        return array(
            'name' => array('name','language')
        );
    }
}