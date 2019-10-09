<?php
/**
 * Zira project.
 * voteresult.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Vote\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Voteresult extends Table {
    protected $_table = 'vote_results';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'vote_id' => Field::int(true, true),
            'option_id' => Field::int(true, true),
            'user_id' => Field::int(true, true),
            'anonymous_id' => Field::string(true),
            'creation_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'vote' => array('vote_id', 'option_id', 'user_id', 'anonymous_id')
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