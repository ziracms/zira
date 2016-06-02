<?php
/**
 * Zira project.
 * cron.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Dash;

class Cron extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-tasks';
    protected static $_title = 'Cron';

    protected $_help_url = 'zira/help/cron';

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