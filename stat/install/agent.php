<?php
/**
 * Zira project.
 * agent.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Agent extends Table {
    protected $_table = 'stat_agents';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'ua' => Field::string(true),
            'mobile' => Field::tinyint(true, true, 0),
            'anonymous_id' => Field::string(true),
            'access_day' => Field::date(true)
        );
    }

    public function getKeys() {
        return array(
            'ua' => array('ua', 'anonymous_id'),
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