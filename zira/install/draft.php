<?php
/**
 * Zira project.
 * draft.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Draft extends Table {
    protected $_table = 'drafts';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'record_id' => Field::int(true, true),
            'author_id' => Field::int(true, true),
            'content' => Field::longtext(),
            'published' => Field::tinyint(true, true, 0),
            'creation_date' => Field::datetime(true),
            'modified_date' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'published' => array('published')
        );
    }

    public function getUnique() {
        return array(
            'record' => array('record_id', 'author_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}