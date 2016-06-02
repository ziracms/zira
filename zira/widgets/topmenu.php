<?php
/**
 * Zira project.
 * topmenu.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Topmenu extends Zira\Widget {
    protected $_title = 'Top menu';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(2);
        $this->setPlaceholder(Zira\View::VAR_HEADER);
    }

    protected function _render() {
        $items = Zira\Menu::getPrimaryMenuItems();
        if (count($items)==0) return;
        $active = Zira\Menu::getPrimaryMenuActiveURL();
        foreach($items as $item) {
            if ($active!==null && $item->url == $active) {
                $item->active = true;
            } else {
                $item->active = false;
            }
            $item->dropdown = Zira\Menu::getPrimaryMenuItemDropdown($item->id);
        }
        Zira\View::renderView(array(
            'items' => $items,
            'search' => new Zira\Forms\Search()
        ), 'zira/widgets/topmenu');
    }
}