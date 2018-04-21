<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Fields extends Widget {
    protected $_title = 'Extra fields';
    protected static $_titles;

    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = \Fields\Models\Group::getCollection()->get();
            foreach($rows as $row) {
                self::$_titles[$row->id] = $row->title;
            }
        }
        return self::$_titles;
    }

    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            $titles = $this->getTitles();
            if (empty($titles) || !array_key_exists($id, $titles)) return parent::getTitle();
            return Zira\Locale::tm('Extra fields', 'fields') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }
    
    protected function _init() {
        $this->setDynamic(true);
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $id = $this->getData();
        if (!is_numeric($id)) return;
        
        $fields = \Fields\Fields::getFields();
        
        $data = array();
        if (!array_key_exists($id, $fields)) return;
        $data[]=$fields[$id];
        
        Zira\View::renderView(array('fields_groups'=>$data), 'fields/record');
    }
}