<?php

namespace Update\V4;

use Zira\Db\Alter;
use Zira\Db\Field;

class Slide extends Alter {
    protected $_table = 'slides';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'link' => Field::string()
        );
    }
}
