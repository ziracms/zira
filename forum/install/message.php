<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Message extends Table {
    protected $_table = 'forum_messages';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'topic_id' => Field::int(true, true),
            'creator_id' => Field::int(true, true),
            'content' => Field::text(true),
            'date_created' => Field::datetime(true),
            'date_modified' => Field::datetime(true),
            'modified_by' => Field::int(),
            'rating' => Field::int(true, false, 0),
            'status' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'topic_id' => array('topic_id')
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