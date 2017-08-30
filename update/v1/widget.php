<?php

namespace Update\V1;

use Zira\Db\Alter;
use Zira\Db\Field;

class Widget extends Alter {
    protected $_table = 'widgets';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'record_id' => Field::int(false, true)
        );
    }
}
