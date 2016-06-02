<?php
/**
 * Zira project.
 * web.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;

class Web extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-globe';
    protected static $_title = 'Web page';

    protected $_help_url = 'zira/help/web-page';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
    }

    public function create() {
        $this->setToolbarItems(array(
            $this->createToolbarButton(null, Zira\Locale::t('Home page'), 'glyphicon glyphicon-home', 'desk_call(dash_web_home_page, this);', 'home'),
            $this->createToolbarButton(null, Zira\Locale::t('Load current page'), 'glyphicon glyphicon-screenshot', 'desk_call(dash_web_current_page, this);', 'current'),
            $this->createToolbarInput(Zira\Locale::t('URL address'), Zira\Locale::t('URL address'), 'glyphicon glyphicon-link', 'desk_call(dash_web_input, this, element);', 'url'),
            $this->createToolbarButton(null, Zira\Locale::t('Reload'), 'glyphicon glyphicon-repeat', 'desk_call(dash_web_reload, this);', 'reload')
        ));

        $this->setBodyFullContent(
            '<iframe id="dashboard-browser-iframe" style="width:100%;height:100%;border:none"></iframe>'.
            '<div class="dashboard-browser-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(234, 240, 245, 0.1);"></div>'
        );

        $this->setOnFocusJSCallback(
            $this->createJSCallback(
                'desk_call(dash_web_focus, this);'
            )
        );

        $this->setOnBlurJSCallback(
            $this->createJSCallback(
                'desk_call(dash_web_blur, this);'
            )
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_web_open, this);'
            )
        );

        $this->addVariables(array(
            'dash_web_home_page_url' => Zira\Helper::url('/',true, true),
            'dash_web_admin_url' => Zira\Helper::url('dash', true, true),
            'dash_web_wnd' => $this->getJSClassName()
        ));

        $this->includeJS('dash/web');
    }
}