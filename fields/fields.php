<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields;

use Zira;
use Dash;

class Fields {
    private static $_instance;
    
    protected static $_fields = array();
    protected static $_preview_fields = array();
    protected static $_preview_fields_ids = null;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public static function getFields() {
        return self::$_fields;
    }
    
    public static function getPreviewFields() {
        return self::$_preview_fields;
    }

    public function beforeDispatch() {
        Zira\Assets::registerCSSAsset('fields/fields.css');
    }
    
    public function onActivate() {
        Zira\Assets::registerCSSAsset('fields/fields.css');
    }

    public function onDeactivate() {
        Zira\Assets::unregisterCSSAsset('fields/fields.css');
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-tags', Zira\Locale::tm('Extra fields', 'fields', null, Dash\Dash::getDashLanguage()), null, 'fieldsGroupsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsGroupsWindow', 'Fields\Windows\Groups', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsGroupWindow', 'Fields\Windows\Group', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsItemsWindow', 'Fields\Windows\Fields', 'Fields\Models\Fields');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsItemWindow', 'Fields\Windows\Field', 'Fields\Models\Fields');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsValuesWindow', 'Fields\Windows\Values', 'Fields\Models\Values');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsSettingsWindow', 'Fields\Windows\Settings', 'Fields\Models\Settings');
            Dash\Dash::unloadDashLanguage();
            Zira\Hook::register(Dash\Windows\Records::RECORDS_MENU_HOOK, array(get_class(), 'dashRecordsMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_CONTEXT_MENU_HOOK, array(get_class(), 'dashRecordsContextMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_SIDEBAR_HOOK, array(get_class(), 'dashRecordsSidebarHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_ON_SELECT_CALLBACK_HOOK, array(get_class(), 'dashRecordsOnSelectCallbackHook'));
        }
  
        Zira\View::addStyle('fields/fields.css');
        Zira\View::registerRenderHook($this, 'renderCallback');
        if (Zira\Config::get('fields_enable_previews')) {
            Zira\Page::registerRecordsPreviewHook($this, 'previewCallback');
        }
    }
    
    public static function dashRecordsMenuHook($window) {
        return array(
            $window->createMenuDropdownItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'))
        );
    }
    
    public static function dashRecordsContextMenuHook($window) {
        return $window->createContextMenuItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'));
    }
    
    public static function dashRecordsSidebarHook($window) {
        return $window->createSidebarItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'));
    }
    
    public static function dashRecordsOnSelectCallbackHook() {
        return 'desk_call(dash_fields_records_on_select, this);';
    }
    
    public static function renderCallback() {
        $record_id = Zira\Page::getRecordId();
        if (!$record_id && Zira\Category::current()) {
            $record_id = Zira\Page::getCategoryPageRecordId();
        }
        if (!$record_id) return;
        $category_ids = array(Zira\Category::ROOT_CATEGORY_ID);
        if (Zira\Category::current()) {
            $chain = Zira\Category::chain();
            foreach($chain as $row) {
                $category_ids[]=$row->id;
            }
        }
        $fields = \Fields\Models\Field::loadRecordFields($category_ids, Zira\Locale::getLanguage());
        if (empty($fields)) return;
        $field_values = \Fields\Models\Value::loadRecordValues($record_id);
        
        if (empty($field_values)) return;
        $placeholders = array();
        $_fields = array();
        $_group_with_vals = array();
        foreach ($fields as $group_id=>$fields_group) {
            if (!array_key_exists('group', $fields_group) || !array_key_exists('fields', $fields_group)) continue;
            $group = $fields_group['group'];
            if (!array_key_exists($group['placeholder'], $placeholders)) $placeholders[$group['placeholder']] = array();
            $placeholders[$group['placeholder']][]=$group_id;
            if (!array_key_exists($group_id, $_fields)) {
                $_fields[$group_id] = array(
                    'group' => $group,
                    'fields' => array()
                );
            }
            foreach($fields_group['fields'] as $field) {
                $value = null;
                $date_added = '';
                if (array_key_exists($field->field_id, $field_values)) {
                    $value = $field_values[$field->field_id]->content;
                    $date_added = $field_values[$field->field_id]->date_added;
                    if (!in_array($group_id, $_group_with_vals)) $_group_with_vals []= $group_id;
                }
                $_fields[$group_id]['fields'][]=array(
                    'id' => $field->field_id,
                    'type' => $field->field_type,
                    'title' => $field->field_title,
                    'description' => $field->field_description,
                    'values' => $field->field_values,
                    'value' => $value,
                    'date_added' => $date_added
                );
            }
        }
        foreach($placeholders as $placeholder=>$group_ids) {
            $data = array();
            foreach ($group_ids as $group_id) {
                if (!array_key_exists($group_id, $_fields)) continue;
                if (!in_array($group_id, $_group_with_vals)) {
                    unset($_fields[$group_id]);
                    continue;
                }
                $data[]=$_fields[$group_id];
            }
            if ($placeholder == Zira\View::VAR_CONTENT) {
                //Zira\View::addPlaceholderView($placeholder, array('fields_groups'=>$data), 'fields/record');
                Zira\Page::setContentView(array('fields_groups'=>$data), 'fields/record');
            }
        }
        
        Zira\View::addLightbox();
        
        self::$_fields = $_fields;
    }
    
    public static function previewCallback($records, $is_widget = false) {
        if ($is_widget && !Zira\Config::get('fields_display_widgets_previews')) return;
        if (empty($records)) return;
        $record_ids = array();
        foreach($records as $record) {
            if (Zira\Page::isRecordPreviewDataExists($record->id)) continue;
            $record_ids []= $record->id;
        }

        if (empty($record_ids)) return;
        
        if (self::$_preview_fields_ids === null) {
            $preview_cache_key = 'fields.preview.'.Zira\Locale::getLanguage();
            $cached_preview_fields = Zira\Cache::getArray($preview_cache_key);
            if ($cached_preview_fields!==false) {
                self::$_preview_fields = $cached_preview_fields;
            } else {
                self::$_preview_fields = \Fields\Models\Field::loadRecordFields(array(), Zira\Locale::getLanguage(), true);
                Zira\Cache::setArray($preview_cache_key, self::$_preview_fields);
            }
            self::$_preview_fields_ids = array();
            foreach(self::$_preview_fields as $fields_group) {
                foreach($fields_group['fields'] as $field) {
                    self::$_preview_fields_ids []= $field->field_id;
                }
            }
        }
        if (empty(self::$_preview_fields_ids)) return;
        
        $values = \Fields\Models\Value::loadRecordsValues($record_ids, self::$_preview_fields_ids);
        if (empty($values)) return;
        
        foreach($records as $record) {
            if (!array_key_exists($record->id, $values)) continue;
            $field_values = $values[$record->id];

            $_fields = array();
            $_group_with_vals = array();
            foreach (self::$_preview_fields as $group_id=>$fields_group) {
                if (!array_key_exists('group', $fields_group) || !array_key_exists('fields', $fields_group)) continue;
                $group = $fields_group['group'];
                if (!array_key_exists($group_id, $_fields)) {
                    $_fields[$group_id] = array(
                        'group' => $group,
                        'fields' => array()
                    );
                }
                foreach($fields_group['fields'] as $field) {
                    $value = null;
                    $date_added = '';
                    if (array_key_exists($field->field_id, $field_values)) {
                        $value = $field_values[$field->field_id]->content;
                        $date_added = $field_values[$field->field_id]->date_added;
                        if (!in_array($group_id, $_group_with_vals)) $_group_with_vals []= $group_id;
                    }
                    $_fields[$group_id]['fields'][]=array(
                        'id' => $field->field_id,
                        'type' => $field->field_type,
                        'title' => $field->field_title,
                        'description' => $field->field_description,
                        'values' => $field->field_values,
                        'value' => $value,
                        'date_added' => $date_added
                    );
                }
            }
            $data = array();
            foreach (self::$_preview_fields as $group_id=>$fields_group) {
                if (!in_array($group_id, $_group_with_vals)) {
                    unset($_fields[$group_id]);
                    continue;
                }
                $data[]=$_fields[$group_id];
            }
            
            if (!empty($data)) {
                Zira\Page::addRecordPreviewData($record->id, array('fields_groups'=>$data), 'fields/preview', Zira\Config::get('fields_display_widgets_previews'));
            }
        }
    }
}