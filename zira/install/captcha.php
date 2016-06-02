<?php
/**
 * Zira project.
 * captcha.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Captcha extends Table {
    protected $_table = 'captcha';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'form_id' => Field::string(true),
            'date_created' => Field::datetime(true)
        );
    }

    public function getKeys() {
        return array(
            'recent' => array('form_id', 'date_created')
        );
    }

    public function getUnique() {
        return array();
    }
}