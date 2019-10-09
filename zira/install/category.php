<?php
/**
 * Zira project.
 * category.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Category extends Table {
    protected $_table = 'categories';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'name' => Field::string(true),
            'title' => Field::string(true),
            'description' => Field::string(),
            'layout' => Field::string(true),
            'meta_title' => Field::string(),
            'meta_keywords' => Field::string(),
            'meta_description' => Field::string(),
            'parent_id' => Field::int(true, true, 0),
            'access_check' => Field::tinyint(true, true, 0),
            'gallery_check' => Field::tinyint(true, true, 0),
            'files_check' => Field::tinyint(true, true, 0),
            'audio_check' => Field::tinyint(true, true, 0),
            'video_check' => Field::tinyint(true, true, 0),
            'slider_enabled' => Field::tinyint(false, true),
            'gallery_enabled' => Field::tinyint(false, true),
            'files_enabled' => Field::tinyint(false, true),
            'audio_enabled' => Field::tinyint(false, true),
            'video_enabled' => Field::tinyint(false, true),
            'comments_enabled' => Field::tinyint(false, true),
            'rating_enabled' => Field::tinyint(false, true),
            'display_author' => Field::tinyint(false, true),
            'display_date' => Field::tinyint(false, true),
            'records_list' => Field::tinyint(false, true),
            'tpl' => Field::string()
        );
    }

    public function getKeys() {
        return array(
            'parent_id' => array('parent_id')
        );
    }

    public function getUnique() {
        return array(
            'name' => array('name')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}