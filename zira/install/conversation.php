<?php
/**
 * Zira project.
 * conversation.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Conversation extends Table {
    protected $_table = 'conversations';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'conversation_id' => Field::int(true, true),
            'user_id' => Field::int(true, true),
            'subject' => Field::string(true),
            'highlight' => Field::tinyint(true, true, 1),
            'creation_date' => Field::datetime(true),
            'modified_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'user' => array('user_id', 'modified_date'),
            'conversation' => array('conversation_id')
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