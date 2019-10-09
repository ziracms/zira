<?php
/**
 * Zira project.
 * fieldvalue.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Fieldvalue extends Table {
    protected $_table = 'field_values';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'field_item_id' => Field::int(true, true),
            'field_group_id' => Field::int(true, true),
            'content' => Field::text(),
            'mark' => Field::string(false),
            'date_added' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'search' => array('record_id')
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