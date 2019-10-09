<?php
/**
 * Zira project.
 * button.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Eform\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Button extends Widget {
    protected $_title = 'Email form button';
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
            return Zira\Locale::tm('Email form button', 'eform') . ' - ' . $titles[$id];
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
        $form->setRenderPanel(false);
        if ($eform->description) {
            $form->setDescription(Zira\Locale::t($eform->description));
        }
        
        Zira\View::renderView(array(
            'eform' => $eform,
            'form' => $form
        ),'eform/button');
    }
}