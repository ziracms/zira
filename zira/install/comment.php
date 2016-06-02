<?php
/**
 * Zira project.
 * comment.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Comment extends Table {
    protected $_table = 'comments';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'author_id' => Field::int(true, true),
            'parent_id' => Field::int(true, true, 0),
            'sort_path' => Field::string(true),
            'path_offset' => Field::int(true, true),
            'content' => Field::text(),
            'sender_name' => Field::string(),
            'recipient_name' => Field::string(),
            'likes' => Field::int(true, true, 0),
            'dislikes' => Field::int(true, true, 0),
            'creation_date' => Field::datetime(true),
            'published' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'record' => array('record_id', 'published', 'sort_path'),
            'counter' => array('record_id', 'parent_id', 'path_offset')
        );
    }

    public function getUnique() {
        return array(

        );
    }

    public function getDefaults() {
        return array(

        );
    }
}