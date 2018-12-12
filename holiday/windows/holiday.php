<?php
/**
 * Zira project.
 * holiday.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Holiday extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-gift';
    protected static $_title = 'Holiday';

    //protected $_help_url = 'zira/help/holiday';

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
                'desk_call(dash_holiday_load, this);'
            )
        );
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Holiday\Forms\Holiday();
        if (!empty($this->item)) {
            $holiday = new \Holiday\Models\Holiday($this->item);
            if (!$holiday->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($holiday->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'holiday').' - '.$holiday->title);
        } else {
            $this->setTitle(Zira\Locale::tm('New holiday','holiday'));
        }

        $this->setBodyContent($form);
    }
}