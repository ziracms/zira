<?php
/**
 * Zira project.
 * footermenu.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Footermenu extends Zira\Widget {
    protected $_title = 'Bottom menu';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_FOOTER);
    }

    protected function _render() {
        $items = Zira\Menu::getFooterMenuItems();
        if (count($items)==0) return;
        $active = Zira\Menu::getFooterMenuActiveURL();
        foreach($items as $item) {
            if ($active!==null && $item->url == $active) {
                $item->active = true;
            } else {
                $item->active = false;
            }
            $item->dropdown = Zira\Menu::getFooterMenuItemDropdown($item->id);
        }
        Zira\View::renderView(array(
            'items' => $items
        ), 'zira/widgets/footermenu');
    }
}