<?php
/**
 * Zira project.
 * mailsettings.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Mailsettings extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-envelope';
    protected static $_title = 'Mail settings';

    protected $_help_url = 'zira/help/mail-settings';

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

        $form = new \Dash\Forms\Mailsettings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}