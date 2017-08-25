<?php

namespace Update\V1;

use Zira\Db\Alter;
use Zira\Db\Field;

class Category extends Alter {
    protected $_table = 'categories';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'gallery_check' => Field::tinyint(true, true, 0),
            'files_check' => Field::tinyint(true, true, 0),
            'audio_check' => Field::tinyint(true, true, 0),
            'video_check' => Field::tinyint(true, true, 0),
            'files_enabled' => Field::tinyint(false, true),
            'audio_enabled' => Field::tinyint(false, true),
            'video_enabled' => Field::tinyint(false, true)
        );
    }
}
