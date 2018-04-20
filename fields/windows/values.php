<?php
/**
 * Zira project.
 * values.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Values extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-tags';
    protected static $_title = 'Record extra fields';

    //protected $_help_url = 'zira/help/extra-field-values';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Records extra fields','fields'), 'glyphicon glyphicon-tags', 'fieldsGroupsWindow();', 'create', false, true)
        );
        
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'.
                'desk_call(dash_fields_records_load, this);'
            )
        );
        
        $this->addVariables(array(
            'fields_thumbs_url_prefix' => Zira\Helper::url('fields/dash/thumb').'?image=',
            'fields_thumbs_height' => \Fields\Forms\Value::THUMBS_HEIGHT
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_VIEW_RECORDS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Fields\Forms\Value();
        $form->loadFields($record);
        $form->loadFieldValues($record);
        $form->setValues(array(
            'record_id' => $record->id
        ));
        
        $this->setTitle($record->title);
        
        $this->setBodyContent($form);
    }
}