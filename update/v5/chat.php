<?php

namespace Update\V5;

use Zira\Db\Alter;
use Zira\Db\Field;

class Chat extends Alter {
    protected $_table = 'chats';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'info' => Field::text()
        );
    }
}
