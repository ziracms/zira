<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;
use Zira\Locale;

class Category extends Table {
    protected $_table = 'forum_categories';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'title' => Field::string(true),
            'description' => Field::text(),
            'layout' => Field::string(),
            'meta_title' => Field::string(),
            'meta_keywords' => Field::string(),
            'meta_description' => Field::string(),
            'access_check' => Field::tinyint(true, true, 0),
            'sort_order' => Field::int(true, false, 0),
            'language' => Field::string(),
            'tpl' => Field::string()
        );
    }

    public function getKeys() {
        return array(
            'sort_order' => array('language', 'sort_order')
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
                'title' => Locale::tm('Default category', 'forum'),
                'description' => null,
                'layout' => null,
                'meta_title' => null,
                'meta_keywords' => null,
                'meta_description' => null,
                'access_check' => 0,
                'sort_order' => 1,
                'tpl' => null
            )
        );
    }
}