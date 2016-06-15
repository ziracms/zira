<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Vote extends Table {
    protected $_table = 'votes';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'creator_id' => Field::int(true, true),
            'subject' => Field::string(true),
            'placeholder' => Field::string(true),
            'multiple' => Field::tinyint(true, true, 0),
            'votes' => Field::int(true, true, 0),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(

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