<?php
/**
 * Zira project.
 * search.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Search extends Table {
    protected $_table = 'search';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'keyword' => Field::string(true),
            'record_id' => Field::int(true, true),
            'language' => Field::string(true),
        );
    }

    public function getKeys() {
        return array(
            'keyword' => array('language', 'keyword'),
            'record_id' => array('record_id')
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