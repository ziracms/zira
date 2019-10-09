<?php
/**
 * Zira project.
 * like.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Like extends Table {
    protected $_table = 'likes';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'user_id' => Field::int(true, true),
            'anonymous_id' => Field::string(true),
            'creation_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'like' => array('record_id', 'user_id', 'anonymous_id')
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