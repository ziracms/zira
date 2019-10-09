<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Extra fields settings';

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
        if (!array_key_exists('fields_search_expand', $configs)) $configs['fields_search_expand'] = 1;
        if (!array_key_exists('fields_search_type', $configs)) $configs['fields_search_type'] = \Fields\Models\Search::TYPE_SEARCH_OR;
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}