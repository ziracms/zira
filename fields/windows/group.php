<?php
/**
 * Zira project.
 * group.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Group extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-tags';
    protected static $_title = 'Extra field group';

    //protected $_help_url = 'zira/help/extra-field-group';

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
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Fields\Forms\Group();
        if (!empty($this->item)) {
            $group = new \Fields\Models\Group($this->item);
            if (!$group->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($group->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'fields').' - '.$group->title);
        } else {
            $form->setValues(array(
                'placeholder' => Zira\View::VAR_CONTENT,
                'active'=>1
                ));
            $this->setTitle(Zira\Locale::tm('New extra field group','fields'));
        }

        $this->setBodyContent($form);
    }
}