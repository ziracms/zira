<?php
/**
 * Zira project.
 * fieldsearch.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Fieldsearch extends Table {
    protected $_table = 'field_search';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'field_item_id' => Field::int(true, true),
            'keyword' => Field::string(true),
            'record_id' => Field::int(true, true),
            'language' => Field::string(true),
            'published' => Field::int(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'search' => array('language', 'published', 'field_item_id', 'keyword', 'record_id')
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