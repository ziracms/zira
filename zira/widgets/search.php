<?php
/**
 * Zira project.
 * search.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Search extends Zira\Widget {
    protected $_title = 'Search';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        if (Zira\Router::getController() == 'search' || Zira\Router::getModule() == 'forum') return;
        
        $search = new Zira\Forms\Search('search-form-extended', true);
        $search->setExtended(true);
        
        $data = array(
            'title' => Zira\Locale::t('Search'),
            'search' => $search
        );

        Zira\View::renderView($data, 'zira/widgets/search');
    }
}