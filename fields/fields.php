<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields;

use Zira;
use Dash;

class Fields {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-tags', Zira\Locale::tm('Extra fields', 'fields', null, Dash\Dash::getDashLanguage()), null, 'fieldGroupsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldGroupsWindow', 'Fields\Windows\Groups', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldGroupWindow', 'Fields\Windows\Group', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsItemWindow', 'Fields\Windows\Fields', 'Fields\Models\Fields');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldItemWindow', 'Fields\Windows\Field', 'Fields\Models\Fields');
            Dash\Dash::unloadDashLanguage();
        }
    }
}