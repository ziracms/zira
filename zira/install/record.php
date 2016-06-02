<?php
/**
 * Zira project.
 * record.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Record extends Table {
    protected $_table = 'records';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'category_id' => Field::int(true, true),
            'author_id' => Field::int(true, true),
            'name' => Field::string(true),
            'title' => Field::string(true),
            'description' => Field::text(),
            'content' => Field::longtext(),
            'thumb' => Field::string(),
            'image' => Field::string(),
            'meta_title' => Field::string(),
            'meta_keywords' => Field::string(),
            'meta_description' => Field::text(),
            'language' => Field::string(true),
            'access_check' => Field::tinyint(true, true, 0),
            'creation_date' => Field::datetime(true),
            'modified_date' => Field::datetime(true),
            'published' => Field::tinyint(true, true, 0),
            'front_page' => Field::tinyint(true, true, 0),
            'rating' => Field::int(true, false, 0),
            'comments' => Field::int(true, true, 0),
            'tpl' => Field::string()
        );
    }

    public function getKeys() {
        return array(
            'record' => array('category_id', 'language', 'name','published','front_page')
        );
    }

    public function getUnique() {
        return array(
            'url' => array('category_id', 'language', 'name')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}