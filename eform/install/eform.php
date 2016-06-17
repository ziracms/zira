<?php
/**
 * Zira project.
 * eform.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Eform extends Table {
    protected $_table = 'eforms';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'name' => Field::string(true),
            'creator_id' => Field::int(true, true),
            'title' => Field::string(true),
            'description' => Field::text(),
            'email' => Field::string(true),
            'date_created' => Field::datetime(true),
            'active' => Field::tinyint(true, true, 1)
        );
    }

    public function getKeys() {
        return array(

        );
    }

    public function getUnique() {
        return array(
            'name' => array('name', 'active')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}