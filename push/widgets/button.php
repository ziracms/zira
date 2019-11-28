<?php
/**
 * Zira project.
 * button.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Button extends Widget {
    protected $_title = 'Push notifications subscribe button';
    
    protected function _init() {
        $this->setDynamic(false);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT);
    }
    
    protected function _render() {
        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        
        Zira\View::renderView(array(
            'is_sidebar' => $is_sidebar
        ), 'push/button');
    }
}