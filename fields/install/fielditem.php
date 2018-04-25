<?php
/**
 * Zira project.
 * fielditem.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Fielditem extends Table {
    protected $_table = 'field_items';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'field_group_id' => Field::int(true, true),
            'field_type' => Field::string(true),
            'field_values' => Field::text(),
            'title' => Field::string(true),
            'description' => Field::string(false),
            'sort_order' => Field::int(true, false, 0),
            'active' => Field::int(true, true, 0),
            'search' => Field::int(true, true, 0),
            'preview' => Field::int(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'search' => array('active', 'field_group_id')
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