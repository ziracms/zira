<?php

namespace Update\V1;

use Zira\Db\Alter;
use Zira\Db\Field;

class Record extends Alter {
    protected $_table = 'records';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'gallery_check' => Field::tinyint(true, true, 0),
            'files_check' => Field::tinyint(true, true, 0),
            'audio_check' => Field::tinyint(true, true, 0),
            'video_check' => Field::tinyint(true, true, 0),
            'slides_count' => Field::tinyint(true, true, 0),
            'images_count' => Field::tinyint(true, true, 0),
            'files_count' => Field::tinyint(true, true, 0),
            'audio_count' => Field::tinyint(true, true, 0),
            'video_count' => Field::tinyint(true, true, 0),
            'comments_enabled' => Field::tinyint(false, true)
        );
    }
    
    public function getKeysToAdd() {
        return array(
            'widget' => array('category_id', 'language', 'published','rating', 'comments')
        );
    }
}