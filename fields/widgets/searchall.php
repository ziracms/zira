<?php
/**
 * Zira project.
 * searchall.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Searchall extends Widget {
    protected $_title = 'Extra fields extended search';

    protected function _init() {
        $this->setDynamic(false);
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_CONTENT_TOP);
    }

    protected function _render() {
        $form = new \Fields\Forms\Search('fields-search-widget-all-form');
        $fields = $form->getFieldsArray();
        
        if (empty($fields)) return;
        
        $form->setTitle(Zira\Locale::tm('Extended search', 'fields'));
        
        $form->setFields($fields);
        
        $expand = Zira\Config::get('fields_search_expand', 1);
        if (Zira\Router::getModule() == 'fields' && Zira\Router::getController() == 'search') {
            $expand = true;
        }
        
        Zira\View::renderView(array(
            'form'=>$form,
            'expand'=>$expand
        ), 'fields/searchall');
    }
}