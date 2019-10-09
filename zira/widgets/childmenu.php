<?php
/**
 * Zira project.
 * childmenu.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Childmenu extends Zira\Widget {
    protected $_title = 'Secondary menu';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $parent_id = Zira\Menu::getSecondaryParentId();
        if (!$parent_id) return;
        Zira\Menu::initSecondaryMenuItems($parent_id);
        $items = Zira\Menu::getSecondaryMenuItems();
        if (count($items)==0) return;
        $active = Zira\Menu::getSecondaryMenuActiveURL();
        foreach($items as $item) {
            if ($active!==null && $item->url == $active) {
                $item->active = true;
            } else {
                $item->active = false;
            }
            $item->dropdown = Zira\Menu::getSecondaryMenuItemDropdown($item->id);
            if (count($item->dropdown)>0) {
                foreach($item->dropdown as $_item) {
                    if ($active!==null && $_item->url == $active) {
                        $_item->active = true;
                    } else {
                        $_item->active = false;
                    }
                }
            }
        }
        Zira\View::renderView(array(
            'items' => $items
        ), 'zira/widgets/childmenu');
    }
}