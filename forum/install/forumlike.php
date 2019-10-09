<?php
/**
 * Zira project.
 * forumlike.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Forumlike extends Table {
    protected $_table = 'forum_likes';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'message_id' => Field::int(true, true),
            'user_id' => Field::int(true, true),
            'anonymous_id' => Field::string(true),
            'rate' => Field::tinyint(true),
            'creation_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'like' => array('message_id', 'user_id', 'anonymous_id')
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