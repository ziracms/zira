<?php
/**
 * Zira project.
 * eformfield.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Eform\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Eformfield extends Table {
    protected $_table = 'eform_fields';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'eform_id' => Field::int(true, true),
            'field_type' => Field::string(true),
            'field_values' => Field::text(),
            'label' => Field::string(true),
            'description' => Field::string(),
            'required' => Field::tinyint(true, true, 0),
            'sort_order' => Field::int(true, false, 0)
        );
    }

    public function getKeys() {
        return array(
            'eform' => array('eform_id', 'sort_order')
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