<?php

namespace Update\V6;

use Zira\Db\Alter;
use Zira\Db\Field;

class Record extends Alter {
    protected $_table = 'records';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'tags' => Field::string()
        );
    }
}
