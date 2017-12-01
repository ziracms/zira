<?php

namespace Update\V3;

use Zira\Db\Alter;
use Zira\Db\Field;

class Forumcategory extends Alter {
    protected $_table = 'forum_categories';

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
            'sort_order'
        );
    }
    
    public function getKeysToAdd() {
        return array(
            'sort_order' => array('language', 'sort_order')
        );
    }
}
