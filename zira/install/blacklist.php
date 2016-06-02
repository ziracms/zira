<?php
/**
 * Zira project.
 * blacklist.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Blacklist extends Table {
    protected $_table = 'black_lists';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'user_id' => Field::int(true, true),
            'blocked_user_id' => Field::int(true, true),
            'message' => Field::string(true),
            'creation_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(

        );
    }

    public function getUnique() {
        return array(
            'user' => array('user_id','blocked_user_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}