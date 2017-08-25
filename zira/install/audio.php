<?php
/**
 * Zira project.
 * audio.php
 * (c)2017 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Audio extends Table {
    protected $_table = 'audio';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'description' => Field::string(),
            'path' => Field::string(true)
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