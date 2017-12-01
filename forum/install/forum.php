<?php
/**
 * Zira project.
 * forum.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;
use Zira\Locale;

class Forum extends Table {
    protected $_table = 'forum_forums';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'category_id' => Field::int(true, true),
            'title' => Field::string(true),
            'description' => Field::text(),
            'meta_title' => Field::string(),
            'meta_keywords' => Field::string(),
            'meta_description' => Field::string(),
            'access_check' => Field::tinyint(true, true, 0),
            'info' => Field::text(),
            'date_created' => Field::datetime(true),
            'date_modified' => Field::datetime(true),
            'topics' => Field::int(true, true, 0),
            'last_user_id' => Field::int(),
            'sort_order' => Field::int(true, false, 0),
            'language' => Field::string(),
            'active' => Field::tinyint(true, true, 1)
        );
    }

    public function getKeys() {
        return array(
            'forum' => array('language', 'category_id', 'active', 'sort_order')
        );
    }

    public function getUnique() {
        return array(

        );
    }

    public function getDefaults() {
        return array(
            array(
                'id' => null,
                'category_id' => 1,
                'title' => Locale::tm('Default forum', 'forum'),
                'description' => null,
                'meta_title' => null,
                'meta_keywords' => null,
                'meta_description' => null,
                'access_check' => 0,
                'info' => null,
                'date_created' => date('Y-m-d H:i:s'),
                'date_modified' => date('Y-m-d H:i:s'),
                'last_user_id' => null,
                'sort_order' => 1,
                'active' => 1
            )
        );
    }
}