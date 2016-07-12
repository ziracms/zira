<?php
/**
 * Zira project.
 * eformfields.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Eformfields extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-list';
    protected static $_title = 'Form fields';

    protected $_help_url = 'zira/help/eform-fields';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback('desk_call(dash_eform_field_create, this)')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback('desk_call(dash_eform_field_edit, this)')
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('Add form field', 'eform'), Zira\Locale::tm('Add form field', 'eform'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_eform_field_create, this)', 'create')
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_eform_fields_drag, this);'
            )
        );

        $this->addVariables(array(
            'dash_eform_blank_src' => Zira\Helper::imgUrl('blank.png'),
            'dash_eform_field_wnd' => Dash\Dash::getInstance()->getWindowJSName(Eformfield::getClass())
        ));

        $this->setData(array(
            'items' => array($this->item)
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        $eform = new \Eform\Models\Eform($this->item);
        if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $fields = \Eform\Models\Eformfield::getCollection()
                                            ->where('eform_id','=',$eform->id)
                                            ->order_by('sort_order', 'asc')
                                            ->get();
        $items = array();
        foreach ($fields as $field) {
            $items[]=$this->createBodyItem($field->label, $field->field_type, Zira\Helper::imgUrl('drag.png'), $field->id, null, false, array('sort_order'=>$field->sort_order));
        }

        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$eform->title);
    }
}