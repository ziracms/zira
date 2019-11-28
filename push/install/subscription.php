<?php
/**
 * Zira project.
 * subscription.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Subscription extends Table {
    protected $_table = 'push_subscriptions';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'user_id' => Field::int(true, true),
            'anonymous_id' => Field::string(true),
            'endpoint' => Field::string(true),
            'pub_key' => Field::string(true),
            'auth_token' => Field::string(true),
            'encoding' => Field::string(true),
            'language' => Field::string(true),
            'active' => Field::int(true, true, 1),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'active' => array('language', 'active')
        );
    }

    public function getUnique() {
        return array(
            'uid' => array('anonymous_id')
        );
    }

    public function getDefaults() {
        return array(

        );
    }
}