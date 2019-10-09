<?php
/**
 * Zira project.
 * file.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class File extends Table {
    protected $_table = 'files';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'description' => Field::string(),
            'path' => Field::string(),
            'url' => Field::string(),
            'download_count' => Field::int(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
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