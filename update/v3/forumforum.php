<?php

namespace Update\V3;

use Zira\Db\Alter;
use Zira\Db\Field;

class Forumforum extends Alter {
    protected $_table = 'forum_forums';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'language' => Field::string()
        );
    }

    public function getKeysToDrop() {
        return array(
            'forum'
        );
    }
    
    public function getKeysToAdd() {
        return array(
            'forum' => array('language', 'category_id', 'active', 'sort_order')
        );
    }
}
