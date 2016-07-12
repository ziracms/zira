<?php
/**
 * Zira project.
 * eformfield.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Eformfield extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-list';
    protected static $_title = 'Form field';

    protected $_help_url = 'zira/help/eform-field';

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
                'desk_call(dash_eform_field_form_init, this);'
            )
        );

        $this->setData(array(
                'eform_id' => 0
        ));
    }

    public function load() {
        $eform_id = Zira\Request::post('eform_id');
        if (empty($eform_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $eform = new \Eform\Models\Eform($eform_id);
        if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Eform\Forms\Eformfield();
        if (!empty($this->item)) {
            $eformfield = new \Eform\Models\Eformfield($this->item);
            if (!$eformfield->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($eformfield->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'eform').' - '.$eform->title.' - '.$eformfield->label);
        } else {
            $form->setValues(array(
                'eform_id' => $eform_id
            ));
            $this->setTitle(Zira\Locale::tm('New form field','eform').' - '.$eform->title);
        }

        $this->setBodyContent($form);
    }
}