<?php
/**
 * Zira project.
 * settings.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Windows;

use Zira;
use Dash;
use Zira\Permission;
use Push\Push;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cloud-upload';
    protected static $_title = 'Push notifications';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultOnLoadScript(
            'desk_call(dash_push_settings_load, this);'
        );

        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('Generate keys', 'push'), Zira\Locale::tm('Generate keys', 'push'), 'glyphicon glyphicon-flash', 'desk_call(dash_push_generate_keys, this);', 'generate', true, true)
        );

        $this->includeJS('push/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        try {
            Push::getWebPushInstance();
        } catch(\Exception $e) {
            return array('error' => Zira\Locale::tm($e->getMessage(), 'push'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Push\Forms\Settings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}