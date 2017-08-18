<?php
/**
 * Zira project.
 * eform.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform;

use Zira;
use Dash;

class Eform {
    const ROUTE = 'submit';

    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        Zira\Router::addRoute(self::ROUTE,'eform/index/index');
        Zira\Router::addRoute(self::ROUTE.'/*','eform/index/index');
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-send', Zira\Locale::tm('Email forms', 'eform', null, Dash\Dash::getDashLanguage()), null, 'eformsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('eformsWindow', 'Eform\Windows\Eforms', 'Eform\Models\Eforms');
            Dash\Dash::getInstance()->registerModuleWindowClass('eformWindow', 'Eform\Windows\Eform', 'Eform\Models\Eforms');
            Dash\Dash::getInstance()->registerModuleWindowClass('eformFieldsWindow', 'Eform\Windows\Eformfields', 'Eform\Models\Eformfields');
            Dash\Dash::getInstance()->registerModuleWindowClass('eformFieldWindow', 'Eform\Windows\Eformfield', 'Eform\Models\Eformfields');
            Dash\Dash::unloadDashLanguage();
        }
    }
}