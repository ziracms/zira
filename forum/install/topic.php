<?php
/**
 * Zira project.
 * topic.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Topic extends Table {
    protected $_table = 'forum_topics';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'category_id' => Field::int(true, true),
            'forum_id' => Field::int(true, true),
            'creator_id' => Field::int(true, true),
            'title' => Field::string(true),
            'description' => Field::text(),
            'meta_title' => Field::string(),
            'meta_keywords' => Field::string(),
            'meta_description' => Field::string(),
            'info' => Field::text(),
            'date_created' => Field::datetime(true),
            'date_modified' => Field::datetime(true),
            'messages' => Field::int(true, true, 0),
            'last_user_id' => Field::int(),
            'language' => Field::string(),
            'active' => Field::tinyint(true, true, 1),
            'status' => Field::tinyint(true, true, 0),
            'sticky' => Field::tinyint(true, true, 0),
            'published' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'forum' => array('language', 'category_id','forum_id','sticky','published')
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