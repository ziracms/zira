<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Chat extends Table {
    protected $_table = 'chats';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'title' => Field::string(true),
            'info' => Field::text(),
            'check_auth' => Field::int(true, true, 0),
            'visible_group' => Field::int(true, true, 0),
            'refresh_delay' => Field::int(true, true, 0),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            
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