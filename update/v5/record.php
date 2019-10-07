<?php

namespace Update\V5;

use Zira\Db\Alter;
use Zira\Db\Field;

class Record extends Alter {
    protected $_table = 'records';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFieldsToAdd() {
        return array(
            'publish_date' => Field::date()
        );
    }

    public function getFieldsToChange() {
        return array(
            'slides_count' => Field::smallint(true, true, 0),
            'images_count' => Field::smallint(true, true, 0),
            'files_count' => Field::smallint(true, true, 0),
            'audio_count' => Field::smallint(true, true, 0),
            'video_count' => Field::smallint(true, true, 0)
        );
    }
}
