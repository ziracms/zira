<?php
/**
 * Zira project.
 * cron.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Dash;

class Cron extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-tasks';
    protected static $_title = 'Cron';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setToolbarEnabled(false);
    }

    public function create() {
        $this->setBodyContent(Zira\Helper::tag('p',Zira\Locale::t('Please wait ...'),array('style'=>'padding:10px')));

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_cron_open, this);'
            )
        );

        $this->includeJS('dash/cron');
    }
}