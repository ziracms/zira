<?php
/**
 * Zira project.
 * visitor.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Visitor extends Table {
    protected $_table = 'stat_visitors';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'category_id' => Field::int(true, true),
            'anonymous_id' => Field::string(true),
            'access_day' => Field::date(true)
        );
    }

    public function getKeys() {
        return array(
            'record' => array('record_id', 'anonymous_id'),
            'day' => array('access_day')
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