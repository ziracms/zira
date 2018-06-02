<?php
/**
 * Zira project.
 * custommenu.php
 * (c)2018 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Custommenu extends Zira\Widget {
    protected $_title = 'Menu';
    protected static $_titles;

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }
    
    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            return Zira\Locale::t('Menu') . ' #' . $id;
        } else {
            return parent::getTitle();
        }
    }

    protected function _render() {
        $id = $this->getData();
        $items = Zira\Menu::getCustomMenuItems($id);
        if (count($items)==0) return;
        
        if ($this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT) {
            $view = 'zira/widgets/childmenu';
        } else if ($this->getPlaceholder() == Zira\View::VAR_FOOTER) {
            $view = 'zira/widgets/footermenu';
        } else {
            $view = 'zira/widgets/topmenu';
        }
        
        Zira\View::renderView(array(
            'custom_id' => $id,
            'items' => $items
        ), $view);
    }
}