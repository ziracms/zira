<?php
/**
 * Zira project.
 * test.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class User extends Table {
    protected $_table = 'users';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'email' => Field::string(true),
            'username' => Field::string(true),
            'password' =>  Field::string(true),
            'group_id' => Field::tinyint(true, true, 0),
            'image' =>  Field::string(false),
            'firstname' => Field::string(false),
            'secondname' => Field::string(false),
            'dob' => Field::date(false),
            'phone' => Field::string(false),
            'country' => Field::string(false),
            'city' => Field::string(false),
            'address' => Field::string(false),
            'language' => Field::string(false),
            'date_created' => Field::datetime(true),
            'date_logged' => Field::datetime(true),
            'last_access' => Field::datetime(false),
            'verified' => Field::tinyint(true, true, 0),
            'active' => Field::tinyint(true, true, 0),
            'messages' => Field::int(true, true, 0),
            'comments' => Field::int(true, true, 0),
            'posts' => Field::int(true, true, 0),
            'subscribed' => Field::tinyint(true, true, 1),
            'vcode' => Field::string(false),
            'code' => Field::string(true),
            'token' => Field::string(false)
        );
    }

    public function getKeys() {
        return array(
            'group_id' => array('group_id'),
            'enabled' => array('verified', 'active'),
            'language' => array('language'),
            'last_access' => array('last_access')
        );
    }

    public function getUnique() {
        return array(
            'email' => array('email'),
            'username' => array('username'),
            'code' => array('code')
        );
    }
}