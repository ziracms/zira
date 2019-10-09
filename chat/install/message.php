<?php
/**
 * Zira project.
 * message.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Message extends Table {
    protected $_table = 'chat_messages';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'chat_id' => Field::int(true, true),
            'creator_id' => Field::int(true, true),
            'creator_name' => Field::string(),
            'content' => Field::text(true),
            'date_created' => Field::datetime(true),
            'status' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'chat_id' => array('chat_id', 'date_created'),
            'user' => array('creator_id', 'chat_id')
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