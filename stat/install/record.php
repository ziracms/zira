<?php
/**
 * Zira project.
 * record.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Record extends Table {
    protected $_table = 'stat_records';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'category_id' => Field::int(true, true),
            'views_count' => Field::int(true, true)
        );
    }

    public function getKeys() {
        return array(
            
        );
    }

    public function getUnique() {
        return array(
            'record_id' => array('record_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}