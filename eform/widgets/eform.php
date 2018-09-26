<?php
/**
 * Zira project.
 * eform.php
 * (c)2018 http://dro1d.ru
 */

namespace Eform\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Eform extends Widget {
    protected $_title = 'Email form';
    protected static $_titles;

    protected function _init() {
        $this->setEditable(true);
        $this->setDynamic(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_CONTENT);
    }
    
    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = \Eform\Models\Eform::getCollection()->get();
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
            return Zira\Locale::tm('Email form', 'eform') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }

    protected function _render() {
        if (Zira\Router::getModule()=='eform') return;
        $id = $this->getData();
        if (!is_numeric($id)) return;
        
        $eform = new \Eform\Models\Eform($id);
        if (!$eform->loaded()) return;
        if (!$eform->active) return;
        
        $fields = \Eform\Models\Eformfield::getCollection()
                                            ->where('eform_id','=',$eform->id)
                                            ->order_by('sort_order', 'asc')
                                            ->get();

        $labels = array();
        $has_required = false;
        $has_file = false;
        foreach($fields as $field) {
            $labels []= Zira\Locale::t($field->label);
            if ($field->required) $has_required = true;
            if ($field->field_type == 'file') $has_file = true;
        }

        $form = new \Eform\Forms\Submit($eform, $fields, $has_required, $has_file, true);
        $form->setUrl(\Eform\Eform::ROUTE.'/'.$eform->name);
        if ($eform->description) {
            $form->setDescription(Zira\Locale::t($eform->description));
        }
        
        Zira\View::renderView(array(
            'form' => $form
        ),'eform/widget');
    }
}