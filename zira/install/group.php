<?php
/**
 * Zira project.
 * group.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;
use Zira\Models\Group as Model;

class Group extends Table {
    protected $_table = 'groups';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'name' => Field::string(true),
            'active' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(
            'active' => array('active')
        );
    }

    public function getUnique() {
        return array(
            'name' => array('name')
        );
    }

    public function getDefaults() {
        return array(
            array(
                'id' => \Zira\User::GROUP_SUPERADMIN,
                'name' => 'Super-Administrators',
                'active' => Model::STATUS_ACTIVE
            ),
            array(
                'id' => \Zira\User::GROUP_ADMIN,
                'name' => 'Administrators',
                'active' => Model::STATUS_ACTIVE
            ),
            array(
                'id' => \Zira\User::GROUP_USER,
                'name' => 'Users',
                'active' => Model::STATUS_ACTIVE
            )
        );
    }
}