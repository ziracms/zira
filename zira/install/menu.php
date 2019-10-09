<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Menu extends Table {
    protected $_table = 'menu_items';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'menu_id' => Field::int(true, true),
            'parent_id' => Field::int(true, true, 0),
            'url' => Field::string(true),
            'title' => Field::string(true),
            'class' => Field::string(),
            'language' => Field::string(),
            'sort_order' => Field::int(true, false, 0),
            'external' => Field::tinyint(true, true, 0),
            'active' => Field::tinyint(true, true, 1)
        );
    }

    public function getKeys() {
        return array(
            'menu' => array('menu_id', 'parent_id', 'language', 'sort_order')
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