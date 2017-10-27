<?php
/**
 * Zira project.
 * widget.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Widget extends Table {
    protected $_table = 'widgets';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'name' => Field::string(true),
            'module' => Field::string(true),
            'placeholder' => Field::string(true),
            'params' => Field::string(),
            'language' => Field::string(),
            'category_id' => Field::int(false, true),
            'record_id' => Field::int(false, true),
            'url' => Field::string(),
            'sort_order' => Field::int(true, false, 0),
            'active' => Field::tinyint(true, true, 0),
            'filter' => Field::string()
        );
    }

    public function getKeys() {
        return array(
            'search' => array('language','category_id','active'),
            'sort_order' => array('sort_order')
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
                'name' => '\Zira\Widgets\Languages',
                'module' => 'zira',
                'placeholder' => 'header',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 1,
                'active' => 0,
                'filter' => null
            ),
            array(
                'id' => null,
                'name' => '\Zira\Widgets\Usermenu',
                'module' => 'zira',
                'placeholder' => 'header',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 2,
                'active' => 1,
                'filter' => null
            ),
            array(
                'id' => null,
                'name' => '\Zira\Widgets\Logo',
                'module' => 'zira',
                'placeholder' => 'header',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 3,
                'active' => 1,
                'filter' => null
            ),
            array(
                'id' => null,
                'name' => '\Zira\Widgets\Topmenu',
                'module' => 'zira',
                'placeholder' => 'header',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 4,
                'active' => 1,
                'filter' => null
            ),
            array(
                'id' => null,
                'name' => '\Zira\Widgets\Childmenu',
                'module' => 'zira',
                'placeholder' => 'sidebar_right',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 5,
                'active' => 1,
                'filter' => null
            ),
            array(
                'id' => null,
                'name' => '\Zira\Widgets\Footermenu',
                'module' => 'zira',
                'placeholder' => 'footer',
                'params' => null,
                'language' => null,
                'category_id' => null,
                'sort_order' => 6,
                'active' => 1,
                'filter' => null
            )
        );
    }
}