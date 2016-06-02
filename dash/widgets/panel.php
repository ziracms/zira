<?php
/**
 * Zira project.
 * panel.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Widgets;

use Dash\Dash;
use Zira;

class Panel extends Zira\Widget {

    protected function _init() {
        $this->setEditable(false);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_BODY_TOP);
    }

    protected function _render() {
        //if (!Zira\View::isViewExists('dash/panel')) return;
        Dash::getInstance()->addPanelDefaultGroups();
        $panel = Dash::getInstance()->getPanelItems();
        Zira\View::renderView(array(
            'panelItems'=>$panel,
            'userMenu'=>Zira\Router::getModule()=='dash'
        ), 'dash/panel');
    }
}