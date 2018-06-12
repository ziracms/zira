<?php
/**
 * Zira project.
 * stat.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat;

use Zira;
use Dash;

class Stat {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-signal', Zira\Locale::tm('Statistics', 'stat', null, Dash\Dash::getDashLanguage()), null, 'statsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('statsWindow', 'Stat\Windows\Stats', 'Stat\Models\Stats');
            Dash\Dash::getInstance()->registerModuleWindowClass('statRequestsWindow', 'Stat\Windows\Requests', 'Stat\Models\Requests');
            Dash\Dash::getInstance()->registerModuleWindowClass('statSettingsWindow', 'Stat\Windows\Settings', 'Stat\Models\Settings');
            Dash\Dash::unloadDashLanguage();
        }
        
        Zira\View::registerRenderHook(get_class(), 'log');
    }
    
    public static function log() {
        if (Zira\View::isAjax()) return;
        \Stat\Models\Access::log();
    }
}