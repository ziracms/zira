<?php

namespace Update\V4;

use Zira\Db\Alter;
use Zira\Db\Field;

class Record extends Alter {
    protected $_table = 'records';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getKeysToDrop() {
        return array('record');
    }
    
    public function getKeysToAdd() {
        return array(
            'record' => array('category_id', 'language','published','front_page')
        );
    }
}
