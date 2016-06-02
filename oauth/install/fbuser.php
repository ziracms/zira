<?php
/**
 * Zira project.
 * fbuser.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Fbuser extends Table {
    protected $_table = 'fb_users';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'user_id' => Field::int(true, true),
            'fb_id' => Field::string(true),
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
            'fb_id' => array('fb_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}