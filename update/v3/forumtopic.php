<?php

namespace Update\V3;

use Zira\Db\Alter;
use Zira\Db\Field;

class Forumtopic extends Alter {
    protected $_table = 'forum_topics';

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
            'forum' => array('language', 'category_id','forum_id','sticky','published')
        );
    }
}
