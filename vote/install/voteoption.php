<?php
/**
 * Zira project.
 * voteoption.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Voteoption extends Table {
    protected $_table = 'vote_options';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'vote_id' => Field::int(true, true),
            'content' => Field::string(true),
            'sort_order' => Field::int(true, false, 0)
        );
    }

    public function getKeys() {
        return array(
            'vote' => array('vote_id', 'sort_order')
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