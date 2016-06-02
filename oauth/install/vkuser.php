<?php
/**
 * Zira project.
 * vkuser.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Vkuser extends Table {
    protected $_table = 'vk_users';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'user_id' => Field::int(true, true),
            'vk_id' => Field::string(true),
            'email' => Field::string(true),
            'profile_name' => Field::string(true),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(

        );
    }

    public function getUnique() {
        return array(
            'vk_id' => array('vk_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}