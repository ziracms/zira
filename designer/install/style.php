<?php
/**
 * Zira project.
 * style.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Style extends Table {
    protected $_table = 'styles';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'creator_id' => Field::int(true, true),
            'theme' => Field::string(true),
            'title' => Field::string(true),
            'content' => Field::text(),
            'language' => Field::string(),
            'category_id' => Field::int(false, true),
            'record_id' => Field::int(false, true),
            'url' => Field::string(),
            'filter' => Field::string(),
            'date_created' => Field::datetime(true),
            'active' => Field::tinyint(true, true, 1)
        );
    }

    public function getKeys() {
        return array(
            'search' => array('theme', 'language', 'active', 'date_created')
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