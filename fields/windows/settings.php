<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Extra fields settings';

    //protected $_help_url = 'zira/help/extra-fields-settings';

    public $item;

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
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Fields\Forms\Settings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}