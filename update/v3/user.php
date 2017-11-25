<?php

namespace Update\V3;

use Zira\Db\Alter;
use Zira\Db\Field;

class User extends Alter {
    protected $_table = 'users';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'language' => Field::string(false),
            'last_access' => Field::datetime(false)
        );
    }
    
    public function getKeysToAdd() {
        return array(
            'language' => array('language'),
            'last_access' => array('last_access')
        );
    }
}
