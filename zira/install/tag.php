<?php
/**
* Zira project.
* tag.php
* (c)2019 https://github.com/ziracms/zira
*/

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Tag extends Table {
    protected $_table = 'tags';
    
    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getFields() {
        return array(
            'id' => Field::primary(),
            'tag' => Field::string(true),
            'record_id' => Field::int(true, true),
            'language' => Field::string(true),
        );
    }
    
    public function getKeys() {
        return array(
            'tag' => array('tag', 'language'),
            'record_id' => array('record_id')
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