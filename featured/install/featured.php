<?php
/**
 * Zira project.
 * featured.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Featured\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Featured extends Table {
    protected $_table = 'featured_records';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'sort_order' => Field::int(true, false, 0),
            'date_added' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'sort_order' => array('sort_order')
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