<?php
/**
 * Zira project.
 * access.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Access extends Table {
    protected $_table = 'stat_access';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'anonymous_id' => Field::string(true),
            'url' => Field::string(true),
            'record_id' => Field::int(true, true),
            'category_id' => Field::int(true, true),
            'language' => Field::string(true),
            'ip' => Field::string(true),
            'ua' => Field::string(true),
            'referer' => Field::string(true),
            'access_day' => Field::date(true),
            'access_time' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'stat' => array('access_day')
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