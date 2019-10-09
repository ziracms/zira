<?php
/**
 * Zira project.
 * console.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;

class Console extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-console';
    protected static $_title = 'Terminal';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setToolbarEnabled(false);
    }

    public function create() {
        $this->setBodyFullContent(
            '<div id="dashboard-console" tabindex="1" style="position:absolute;width:100%;height:100%;font-family:monospace;padding:10px;overflow-x:hidden;overflow-y:scroll;outline:none"></div>'
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_console_create, this);'
            )
        );

        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_console_load, this);'
            )
        );

        $this->setOnFocusJSCallback(
            $this->createJSCallback(
                'desk_call(dash_console_focus, this);'
            )
        );

        $this->setOnBlurJSCallback(
            $this->createJSCallback(
                'desk_call(dash_console_blur, this);'
            )
        );

        $this->setData(array(
            'exec' => 'uname -a',
            'mode' => 'sh'
        ));

        $this->addStrings(array(
            'Initialization',
            'Password'
        ));

        $this->includeJS('dash/console');
    }

    public function load() {
        $model = new \Dash\Models\Console($this);
        $data = $model->run();
        $this->setData($data);
    }
}