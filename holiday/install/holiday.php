<?php
/**
 * Zira project.
 * holiday.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Holiday extends Table {
    protected $_table = 'holidays';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'cdate' => Field::string(true),
            'title' => Field::string(true),
            'description' => Field::string(false),
            'image' => Field::string(false),
            'audio' => Field::string(false),
            'cls' => Field::string(false),
            'active' => Field::int(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'active' => array('active')
        );
    }

    public function getUnique() {
        return array(

        );
    }

    public function getDefaults() {
        return array(

        );
    }
}