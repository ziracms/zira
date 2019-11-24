<?php
/**
 * Zira project.
 * record.php
 * (c)2016 https://github.com/ziracms/zira
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
            'tags' => Field::string(),
            'language' => Field::string(true),
            'access_check' => Field::tinyint(true, true, 0),
            'gallery_check' => Field::tinyint(true, true, 0),
            'files_check' => Field::tinyint(true, true, 0),
            'audio_check' => Field::tinyint(true, true, 0),
            'video_check' => Field::tinyint(true, true, 0),
            'slides_count' => Field::smallint(true, true, 0),
            'images_count' => Field::smallint(true, true, 0),
            'files_count' => Field::smallint(true, true, 0),
            'audio_count' => Field::smallint(true, true, 0),
            'video_count' => Field::smallint(true, true, 0),
            'comments_enabled' => Field::tinyint(false, true),
            'creation_date' => Field::datetime(true),
            'modified_date' => Field::datetime(true),
            'publish_date' => Field::date(),
            'published' => Field::tinyint(true, true, 0),
            'front_page' => Field::tinyint(true, true, 0),
            'rating' => Field::int(true, false, 0),
            'comments' => Field::int(true, true, 0),
            'tpl' => Field::string()
        );
    }

    public function getKeys() {
        return array(
            'record' => array('category_id', 'language','published','front_page'),
            'widget' => array('category_id', 'language', 'published','rating', 'comments')
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