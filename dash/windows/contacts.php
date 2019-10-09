<?php
/**
 * Zira project.
 * contacts.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Contacts extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-map-marker';
    protected static $_title = 'Contacts';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_contacts_load, this);'
            )
        );

        $this->includeJS('dash/contacts');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Dash\Forms\Contacts();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}