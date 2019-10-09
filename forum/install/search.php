<?php
/**
 * Zira project.
 * search.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Search extends Table {
    protected $_table = 'forum_search';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'category_id' => Field::int(true, true),
            'forum_id' => Field::int(true, true),
            'topic_id' => Field::int(true, true),
            'keyword' => Field::string(true)
        );
    }

    public function getKeys() {
        return array(
            'keyword' => array('keyword', 'category_id', 'forum_id'),
            'topic' => array('category_id', 'forum_id', 'topic_id')
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