<?php
/**
 * Zira project.
 * selector.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;

class Selector extends Files {
    public function init() {
        $this->setSingleInstance(true);
        parent::init();
    }

    public function create() {
        parent::create();
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Select'), Zira\Locale::t('Select'), 'glyphicon glyphicon-ok', 'desk_call(dash_selector_choose, this);', 'select', true)
        );

        $this->addVariables(array(
            'dash_selector_wnd' => $this->getJSClassName()
        ));

        $this->includeJS('dash/selector');
    }

    protected function get_body_item_callback_js() {
        return 'desk_call(dash_selector_body_item_callback, this);';
    }

    protected function get_on_select_js() {
        $js = parent::get_on_select_js();
        $js .= 'desk_call(dash_selector_select, this);';
        return $js;
    }
}