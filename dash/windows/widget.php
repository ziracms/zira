<?php
/**
 * Zira project.
 * widget.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Widget extends Window {
    public $item;
    protected static $_icon_class = 'glyphicon glyphicon-modal-window';
    protected static $_title = 'Widget';

    protected $_help_url = 'zira/help/widget';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widget_load, this);'
            )
        );
        
        $this->addVariables(array(
            'dash_widget_autocomplete_url' => Zira\Helper::url('dash/widgets/autocompletepage'),
        ));
        
        $this->includeJS('dash/widget');
    }

    public function load() {
        if (empty($this->item)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Widgets::getAvailableWidgets();
        $form = new \Dash\Forms\Widget();

        if (is_numeric($this->item)) {
            $widget = new Zira\Models\Widget($this->item);
            if (!$widget->loaded() || !array_key_exists($widget->name, $available_widgets)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            if (!$available_widgets[$widget->name]->isEditable()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $values = $widget->toArray();
            if ($values['category_id']===null) {
                $values['category_id'] = -1;
            }
            if ($values['record_id']>0) {
                $values['category_id'] = -2;
                $record = new Zira\Models\Record($values['record_id']);
                if ($record->loaded()) $values['record'] = $record->title;
            }
            if ($values['language']!==null && !in_array($values['language'],Zira\Config::get('languages'))) {
                $values['language'] = null;
            }

            $this->setTitle(Zira\Locale::t('Widget').' - '.Zira\Locale::tm($available_widgets[$widget->name]->getTitle(), $widget->module));
        } else {
            if (!array_key_exists($this->item, $available_widgets)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            if (!$available_widgets[$this->item]->isEditable()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $values = array(
                'id' => $this->item,
                'placeholder' => $available_widgets[$this->item]->getPlaceholder(),
                'category_id' => -1
            );

            $module = strtolower(substr($this->item, 1, strpos($this->item, '\\', 1)-1));
            $this->setTitle(Zira\Locale::t(self::$_title).' - '.Zira\Locale::tm($available_widgets[$this->item]->getTitle(), $module));
        }
        $form->setValues($values);

        $this->setBodyContent($form);
    }
}