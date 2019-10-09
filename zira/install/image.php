<?php
/**
 * Zira project.
 * image.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Image extends Table {
    protected $_table = 'images';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'description' => Field::string(),
            'thumb' => Field::string(true),
            'image' => Field::string(true)
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