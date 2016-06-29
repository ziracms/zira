<?php
/**
 * Zira project.
 * file.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class File extends Table {
    protected $_table = 'forum_files';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'message_id' => Field::int(true, true),
            'owner_id' => Field::int(true, true),
            'path1' => Field::string(true),
            'path2' => Field::string(),
            'path3' => Field::string(),
            'path4' => Field::string(),
            'path5' => Field::string(),
            'path6' => Field::string(),
            'path7' => Field::string(),
            'path8' => Field::string(),
            'path9' => Field::string(),
            'path10' => Field::string(),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(

        );
    }

    public function getUnique() {
        return array(
            'message_id' => array('message_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}