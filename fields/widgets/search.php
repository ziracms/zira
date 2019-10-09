<?php
/**
 * Zira project.
 * search.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Search extends Widget {
    protected $_title = 'Extra fields search';
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
            return Zira\Locale::tm('Extra fields search', 'fields') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }
    
    protected function _init() {
        $this->setDynamic(true);
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_CONTENT_TOP);
    }

    protected function _render() {
        $id = $this->getData();
        if (!is_numeric($id)) return;
        
        $form = new \Fields\Forms\Search('fields-search-widget-form-'.$id);
        $fields = $form->getFieldsArray();
        
        if (empty($fields) || !array_key_exists($id, $fields)) return;
        
        $form->setTitle(Zira\Locale::tm('Extended search', 'fields'));
        $form->setDescription(Zira\Locale::t($fields[$id]['group']['title']));
        
        $form->setFields(array($id=>$fields[$id]));
        
        $expand = Zira\Config::get('fields_search_expand', 1);
        if (Zira\Router::getModule() == 'fields' && Zira\Router::getController() == 'search') {
            $expand = true;
        }
        
        Zira\View::renderView(array(
            'form'=>$form,
            'expand'=>$expand
        ), 'fields/search');
    }
}