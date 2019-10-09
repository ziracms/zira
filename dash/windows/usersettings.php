<?php
/**
 * Zira project.
 * usersettings.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Usersettings extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-user';
    protected static $_title = 'User settings';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();
        if (!array_key_exists('user_signup_allow', $configs)) $configs['user_signup_allow'] = 0;
        if (!array_key_exists('user_profile_view_allow', $configs)) $configs['user_profile_view_allow'] = 1;
        if (!array_key_exists('user_login_change_allow', $configs)) $configs['user_login_change_allow'] = 1;
        if (!array_key_exists('user_email_verify', $configs)) $configs['user_email_verify'] = 1;
        if (!array_key_exists('user_check_ua', $configs)) $configs['user_check_ua'] = 1;

        $form = new \Dash\Forms\Usersettings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}