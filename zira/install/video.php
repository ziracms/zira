<?php
/**
 * Zira project.
 * video.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Video extends Table {
    protected $_table = 'videos';

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
            'embed' => Field::text()
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