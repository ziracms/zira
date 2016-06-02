<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Message extends Table {
    protected $_table = 'messages';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'conversation_id' => Field::int(true, true),
            'user_id' => Field::int(true, true),
            'content' => Field::text(true),
            'creation_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
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