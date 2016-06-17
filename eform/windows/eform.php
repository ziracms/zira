<?php
/**
 * Zira project.
 * eform.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Eform extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-th-list';
    protected static $_title = 'Email form';

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

        $form = new \Eform\Forms\Eform();
        if (!empty($this->item)) {
            $eform = new \Eform\Models\Eform($this->item);
            if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($eform->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'eform').' - '.$eform->name);
        } else {
            $form->setValues(array('active'=>1));
            $this->setTitle(Zira\Locale::tm('New email form','eform'));
        }

        $this->setBodyContent($form);
    }
}