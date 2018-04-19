<?php
/**
 * Zira project.
 * field.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Field extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-tags';
    protected static $_title = 'Records extra field';

    //protected $_help_url = 'zira/help/extra-field';

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
                'desk_window_form_init(this);'.
                'desk_call(dash_fields_field_form_init, this);'
            )
        );
    }

    public function load() {
        $group_id = Zira\Request::post('group_id');
        if (empty($group_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $group = new \Fields\Models\Group($group_id);
        if (!$group->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        
        $form = new \Fields\Forms\Field();
        if (!empty($this->item)) {
            $field = new \Fields\Models\Field($this->item);
            if (!$field->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($field->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'fields').' - '.$group->title.' - '.$field->title);
        } else {
            $form->setValues(array(
                'field_group_id' => $group->id,
                'active'=>1
            ));
            $this->setTitle(Zira\Locale::tm('New record extra field','fields').' - '.$group->title);
        }

        $this->setBodyContent($form);
    }
}