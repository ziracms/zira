<?php
/**
 * Zira project.
 * block.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Block extends Table {
    protected $_table = 'blocks';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'name' => Field::string(true),
            'content' => Field::text(true),
            'placeholder' => Field::string(true),
            'tpl' => Field::tinyint(true, true, 0)
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