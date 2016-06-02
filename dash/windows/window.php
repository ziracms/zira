<?php
/**
 * Zira project.
 * window.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash;
use Zira\Helper;
use Zira\Locale;

abstract class Window {
    protected $_js_name;

    protected static $_callback_string_mode = false;

    protected $_create_action_text;
    protected $_edit_action_text;
    protected $_delete_action_text;
    protected $_save_action_text;
    protected $_default_menu_title;
    protected $_default_menu = array();
    protected $_default_menu_dropdown = array();
    protected $_default_context_menu = array();
    protected $_default_toolbar = array();
    protected $_default_sidebar = array();
    protected $_create_action_window_class;
    protected $_edit_action_window_class;
    protected $_default_onload_scripts = array();
    protected $_selection_links = false;
    protected $_reload_button = true;
    protected $_extra_js = '';
    protected $_save_action_enabled = false;
    protected $_delete_action_enabled = false;
    protected $_help_url = null;

    protected $_options = array(
            'top' => null,
            'left' => null,
            'right' => null,
            'bottom' => null,
            'width' => null,
            'height' => null,
            'auto' => false,
            'resize' => true,
            'animate' => true,
            'maximized' => false,
            'sidebar' => true,
            'sidebar_width' => null,
            'toolbar' => true,
            'viewSwitcher' => false,
            'bodyViewList' => false,
            'icon_class' => 'glyphicon glyphicon-th-large',
            'title' => null,
            'menuItems' => [],
            'toolbarItems' => [],
            'toolbarContent' => '',
            'sidebarItems' => [],
            'sidebarContent' => '',
            'bodyItems' => [],
            'bodyContent' => '',
            'bodyFullContent' => '',
            'footerContent' => '',
            'contextMenuItems' => [],
            'onOpen' => null,
            'onLoad' => null,
            'onFocus' => null,
            'onSelect' => null,
            'onClose' => null,
            'onBlur' => null,
            'onDrop' => null,
            'onCreateItem' => null,
            'onEditItem' => null,
            'onDeleteItems' => null,
            'onUpdateContent' => null,
            'onSave' => null,
            'onResize' => null,
            'load' => null,
            'data' => null,
            'nocache' => false,
            'singleInstance' => false,
            'help_url' => null
    );

    abstract public function init();
    abstract public function create();

    public function __construct() {
        $this->_js_name = Dash\Dash::getInstance()->getWindowJSName(self::getClass());
        if (empty($this->_js_name)) throw new \Exception('Failed to construct window: ' . self::getClass());
    }

    public function build() {
        $this->setDefaultMenuTitle(Locale::t('Actions'));
        $this->setCreateActionText(Locale::t('Create'));
        $this->setEditActionText(Locale::t('Edit'));
        $this->setDeleteActionText(Locale::t('Delete'));
        $this->setSaveActionText(Locale::t('Save'));
        $this->setAutoSizing(true);
        $this->init();
        if (method_exists($this, 'load')) {
            $this->setLoadURL(Helper::url('dash/index/load'));
        }
        if ($this->_save_action_enabled || method_exists($this, 'save')) {
            $this->setOnSaveJSCallback(
                $this->createJSCallback(
                    'var data = desk_window_content(this);'.
                    'desk_window_request(this, \''.Helper::url('dash/index/save').'\', data);'
                )
            );
        }
        if (!empty($this->_options['onSave'])) {
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem($this->_save_action_text, 'glyphicon glyphicon-floppy-disk', 'desk_window_save(this);', 'save')
            );
            $this->addDefaultToolbarItem(
                $this->createToolbarButton($this->_save_action_text, $this->_save_action_text, 'glyphicon glyphicon-floppy-disk', 'desk_window_save(this);', 'save')
            );
        }
        if (!empty($this->_create_action_window_class)) {
            $this->setOnCreateItemJSCallback(
                $this->createJSCallback(
                    'var data = {\'onClose\':function(){desk_window_reload_all(\''.$this->getJSClassName().'\');}};'.
                    Dash\Dash::getInstance()->getWindowJSName($this->_create_action_window_class).'(data);'
                )
            );
        }
        if (!empty($this->_options['onCreateItem'])) {
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem($this->_create_action_text, 'glyphicon glyphicon-file', 'desk_window_create_item(this);', 'create')
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem($this->_create_action_text, 'glyphicon glyphicon-file', 'desk_window_create_item(this);', 'create')
            );
            $this->addDefaultSidebarItem(
                $this->createSidebarItem($this->_create_action_text, 'glyphicon glyphicon-file', 'desk_window_create_item(this);', 'create')
            );
        }
        if (!empty($this->_edit_action_window_class)) {
            $this->setOnEditItemJSCallback(
                $this->createJSCallback(
                    'var data = {\'data\':desk_window_selected(this,1),\'onClose\':function(){desk_window_reload_all(\''.$this->getJSClassName().'\');}};'.
                    Dash\Dash::getInstance()->getWindowJSName($this->_edit_action_window_class).'(data);'
                )
            );
        }
        if (!empty($this->_options['onEditItem'])) {
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem($this->_edit_action_text, 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit')
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem($this->_edit_action_text, 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit')
            );
        }
        if ($this->_delete_action_enabled || method_exists($this, 'delete')) {
            $this->setOnDeleteItemsJSCallback(
                $this->createJSCallback(
                    'var data = desk_window_selected(this);'.
                    'desk_window_request(this, \''.Helper::url('dash/index/delete').'\', data);'
                )
            );
        }
        if (!empty($this->_options['onDeleteItems'])) {
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem($this->_delete_action_text, 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete')
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem($this->_delete_action_text, 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete')
            );
        }
        if (property_exists($this, 'search')) {
            $this->addDefaultToolbarItem(
                $this->createToolbarInput(Locale::t('Search'), Locale::t('Search'), 'glyphicon glyphicon-search', 'var text=$(element).val();desk_window_search(this, text);', 'search')
            );
            $this->addDefaultOnLoadScript('desk_window_search_init(this);');
        }
        if (method_exists($this, 'load') && $this->_reload_button) {
            $this->addDefaultToolbarItem(
                $this->createToolbarButton(null, Locale::t('Reload'), 'glyphicon glyphicon-repeat', 'desk_window_reload(this);', 'reload')
            );
        }
        if (property_exists($this, 'page')) {
            $this->addDefaultToolbarItem(
                $this->createToolbarButtonGroup(array(
                    $this->createToolbarButton(null, Locale::t('Previous'), 'glyphicon glyphicon-arrow-left', 'desk_window_pagination_prev(this);', 'pagination-prev', true),
                    $this->createToolbarButton(null, Locale::t('Next'), 'glyphicon glyphicon-arrow-right', 'desk_window_pagination_next(this);', 'pagination-next', true)
                ), true)
            );
            $this->addDefaultOnLoadScript('desk_window_pagination_init(this);');
        }
        if (property_exists($this, 'order')) {
            $this->addDefaultToolbarItem(
                $this->createToolbarButtonGroup(array(
                    $this->createToolbarButton(null, Locale::t('Sort descending'), 'glyphicon glyphicon-sort-by-attributes-alt', 'desk_window_sort_desc(this);', 'order-desc', true),
                    $this->createToolbarButton(null, Locale::t('Sort ascending'), 'glyphicon glyphicon-sort-by-attributes', 'desk_window_sort_asc(this);', 'order-asc', true)
                ), true)
            );
            $this->addDefaultOnLoadScript('desk_window_sorter_init(this);');
        }

        $this->create();

        if (!empty($this->_default_onload_scripts)) {
            $this->setOnLoadJSCallback(
                $this->createJSCallback(
                    implode(' ', $this->_default_onload_scripts)
                ),
                true
            );
        }

        if ($this->_options['data']==null) $this->setData(array());
    }

    public static function getParentClass() {
        return get_class();
    }

    public static function getClass() {
        return get_called_class();
    }

    public function getJSClassName() {
        return $this->_js_name;
    }

    public function getOptions() {
        return $this->_options;
    }

    public function resetOptions() {
        $this->_options = array();
    }

    public function setDefaultMenuTitle($title) {
        $this->_default_menu_title = (string)$title;
    }

    public function setDefaultMenu(array $menu) {
        $this->_default_menu = $menu;
    }

    public function getDefaultMenu() {
        return $this->_default_menu;
    }

    public function addDefaultMenuItem(array $menuItem) {
        $this->_default_menu []= $menuItem;
    }

    public function setDefaultMenuDropdown(array $menu) {
        $this->_default_menu_dropdown = $menu;
    }

    public function getDefaultMenuTitle() {
        return $this->_default_menu_title;
    }

    public function getDefaultMenuDropdown() {
        return $this->_default_menu_dropdown;
    }

    public function addDefaultMenuDropdownItem(array $menuItem) {
        $this->_default_menu_dropdown []= $menuItem;
    }

    public function setDefaultContextMenu(array $menu) {
        $this->_default_context_menu = $menu;
    }

    public function getDefaultContextMenu() {
        return $this->_default_context_menu;
    }

    public function addDefaultContextMenuItem(array $menuItem) {
        $this->_default_context_menu []= $menuItem;
    }

    public function setDefaultToolbar(array $toolbar) {
        $this->_default_toolbar = $toolbar;
    }

    public function getDefaultToolbar() {
        return $this->_default_toolbar;
    }

    public function addDefaultToolbarItem(array $toolbarItem) {
        $this->_default_toolbar []= $toolbarItem;
    }

    public function setDefaultSidebar(array $sidebar) {
        $this->_default_sidebar = $sidebar;
    }

    public function getDefaultSidebar() {
        return $this->_default_sidebar;
    }

    public function addDefaultSidebarItem(array $sidebarItem) {
        $this->_default_sidebar []= $sidebarItem;
    }

    public function setCreateActionText($text) {
        $this->_create_action_text = (string)$text;
    }

    public function setEditActionText($text) {
        $this->_edit_action_text = (string)$text;
    }

    public function setDeleteActionText($text) {
        $this->_delete_action_text = (string)$text;
    }

    public function setSaveActionText($text) {
        $this->_save_action_text = (string)$text;
    }

    public function setCreateActionWindowClass($class) {
        if (!method_exists($class,'getParentClass') || $class::getParentClass()!='Dash\Windows\Window') throw new \Exception('Invalid window class passed');
        $this->_create_action_window_class = $class;
    }

    public function setEditActionWindowClass($class) {
        if (!method_exists($class,'getParentClass') || $class::getParentClass()!='Dash\Windows\Window') throw new \Exception('Invalid window class passed');
        $this->_edit_action_window_class = $class;
    }

    public function setSaveActionEnabled($enabled) {
        $this->_save_action_enabled = (bool)$enabled;
    }

    public function setDeleteActionEnabled($enabled) {
        $this->_delete_action_enabled = (bool)$enabled;
    }

    public function addDefaultOnLoadScript($js) {
        if (substr($js, -1)!=';') $js.=';';
        $this->_default_onload_scripts[]=$js;
    }

    public function getDefaultOnLoadScripts() {
        return implode(' ', $this->_default_onload_scripts);
    }

    public function setSelectionLinksEnabled($enabled) {
        $this->_selection_links = (bool)$enabled;
    }

    public static function setCallbackStringMode($mode) {
        self::$_callback_string_mode = (bool)$mode;
    }

    protected function setOption($name, $value) {
        $this->_options[$name] = $value;
    }

    public function setTop($value) {
        $this->setOption('top', intval($value));
    }

    public function setLeft($value) {
        $this->setOption('left', intval($value));
    }

    public function setRight($value) {
        $this->setOption('right', intval($value));
    }

    public function setBottom($value) {
        $this->setOption('bottom', intval($value));
    }

    public function setWidth($value) {
        $this->setOption('width', intval($value));
    }

    public function setHeight($value) {
        $this->setOption('height', intval($value));
    }

    public function setAutoSizing($value) {
        $this->setOption('auto', boolval($value));
    }

    public function setResizing($value) {
        $this->setOption('resize', boolval($value));
    }

    public function setAnimating($value) {
        $this->setOption('animate', boolval($value));
    }

    public function setMaximized($value) {
        $this->setOption('maximized', boolval($value));
    }

    public function setSidebarEnabled($value) {
        $this->setOption('sidebar', boolval($value));
    }

    public function setSidebarWidth($value) {
        $this->setOption('sidebar_width', intval($value));
    }

    public function setToolbarEnabled($value) {
        $this->setOption('toolbar', boolval($value));
    }

    public function setViewSwitcherEnabled($value) {
        $this->setOption('viewSwitcher', boolval($value));
    }

    public function setBodyViewListVertical($value) {
        $this->setOption('bodyViewList', boolval($value));
    }

    public function setIconClass($value) {
        $this->setOption('icon_class', (string)$value);
    }

    public function setTitle($value) {
        $this->setOption('title', (string)$value);
    }

    public function setMenuItems(array $value) {
        $this->setOption('menuItems', $value);
    }

    public function setToolbarItems(array $value) {
        $this->setOption('toolbarItems', $value);
    }

    public function setSidebarItems(array $value) {
        $this->setOption('sidebarItems', $value);
    }

    public function setBodyItems(array $value) {
        $this->setOption('bodyItems', $value);
    }

    public function setContextMenuItems(array $value) {
        $this->setOption('contextMenuItems', $value);
    }

    public function setToolbarContent($value) {
        $this->setOption('toolbarContent', (string)$value);
    }

    public function setSidebarContent($value) {
        $this->setOption('sidebarContent', (string)$value);
    }

    public function setBodyContent($value) {
        $this->setOption('bodyContent', (string)$value);
    }

    public function setBodyFullContent($value) {
        $this->setOption('bodyFullContent', (string)$value);
    }

    public function setFooterContent($value) {
        $this->setOption('footerContent', (string)$value);
    }

    public function setOnOpenJSCallback($value) {
        $this->setOption('onOpen', (string)$value);
    }

    public function setOnLoadJSCallback($value, $force=false) {
        if (!$force && (property_exists($this, 'page') || property_exists($this, 'order'))) throw new \Exception('Cannot setOnLoadJSCallback. Use addDefaultOnLoadScript instead.');
        $this->setOption('onLoad', (string)$value);
    }

    public function setOnFocusJSCallback($value) {
        $this->setOption('onFocus', (string)$value);
    }

    public function setOnBlurJSCallback($value) {
        $this->setOption('onBlur', (string)$value);
    }

    public function setOnSelectJSCallback($value) {
        $this->setOption('onSelect', (string)$value);
    }

    public function setOnDropJSCallback($value) {
        $this->setOption('onDrop', (string)$value);
    }

    public function setOnCloseJSCallback($value) {
        $this->setOption('onClose', (string)$value);
    }

    public function setOnCreateItemJSCallback($value) {
        $this->setOption('onCreateItem', (string)$value);
    }

    public function setOnEditItemJSCallback($value) {
        $this->setOption('onEditItem', (string)$value);
    }

    public function setOnDeleteItemsJSCallback($value) {
        $this->setOption('onDeleteItems', (string)$value);
    }

    public function setOnSaveJSCallback($value) {
        $this->setOption('onSave', (string)$value);
    }

    public function setOnUpdateContentJSCallback($value) {
        $this->setOption('onUpdateContent', (string)$value);
    }

    public function setOnResizeJSCallback($value) {
        $this->setOption('onResize', (string)$value);
    }

    public function setLoadURL($value) {
        $this->setOption('load', (string)$value);
    }

    public function setData(array $value) {
        $value['token'] = Dash\Dash::getToken();
        $this->setOption('data', $value);
    }

    public function setNoCache($value) {
        $this->setOption('nocache', (bool)$value);
    }


    public function setSingleInstance($value) {
        $this->setOption('singleInstance', (bool)$value);
    }

    public function setReloadButtonEnabled($reload_button) {
        $this->_reload_button = (bool)$reload_button;
    }

    public function createJSCallback($js) {
        if (empty($js)) return null;
        if (self::$_callback_string_mode) {
            return $this->createJSCallbackString($js);
        } else {
            return $this->createJSCallbackFunction($js);
        }
    }

    public function createJSCallbackFunction($js) {
        $js = trim($js);
        if (substr($js,-1)!=';') $js .= ';';
        return 'function(element){'.$js.'}';
    }

    public function createJSCallbackString($js) {
        $js = trim($js);
        if (substr($js,-1)!=';') $js .= ';';
        //return 'new Function(\'element\', \''.$js.'\')';
        return '(function(element){'.$js.'})';
    }

    public function createMenuItem($title, array $dropdownItems) {
        return array(
            'title' => (string)$title,
            'items' => $dropdownItems
        );
    }

    public function createMenuDropdownItem($title, $icon_class, $js, $action_name=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'title' => (string)$title,
            'icon_class' => (string)$icon_class,
            'callback' => $this->createJSCallback($js),
            'action' => (string)$action_name,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createMenuDropdownSeparator() {
        return array(
            'type' => 'separator'
        );
    }

    public function createContextMenuItem($title, $icon_class, $js, $action_name=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'title' => (string)$title,
            'icon_class' => (string)$icon_class,
            'callback' => $this->createJSCallback($js),
            'action' => (string)$action_name,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createContextMenuSeparator() {
        return array(
            'type' => 'separator'
        );
    }

    public function createToolbarButton($title, $tooltip, $icon_class, $js, $action_name=null, $disabled=false, $align_right=false, array $extra = array()) {
        return array_merge(array(
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'icon_class' => (string)$icon_class,
            'align' => $align_right ? 'right' : 'left',
            'callback' => $this->createJSCallback($js),
            'action' => (string)$action_name,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createToolbarInput($title, $tooltip, $icon_class, $js, $action_name=null, $disabled=false, $align_right=false, array $extra = array()) {
        return array_merge(array(
            'type' => 'input',
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'icon_class' => (string)$icon_class,
            'align' => $align_right ? 'right' : 'left',
            'callback' => $this->createJSCallback($js),
            'action' => (string)$action_name,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createToolbarButtonGroup(array $buttons, $align_right=false, array $extra = array()) {
        return array_merge(array(
            'type' => 'button_group',
            'align' => $align_right ? 'right' : 'left',
            'items' => $buttons
        ), $extra);
    }

    public function createSidebarItem($title, $icon_class, $js, $action_name=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'title' => (string)$title,
            'icon_class' => (string)$icon_class,
            'callback' => $this->createJSCallback($js),
            'action' => (string)$action_name,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createSidebarSeparator() {
        return array(
            'type' => 'separator'
        );
    }

    public function createBodyItem($title, $tooltip, $src, $data=null, $js=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'src' => (string)$src,
            'callback' => $this->createJSCallback($js),
            'data' => $data,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createBodyFolderItem($title, $tooltip, $data=null, $js=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'type' => 'folder',
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'callback' => $this->createJSCallback($js),
            'data' => $data,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createBodyFileItem($title, $tooltip, $data=null, $js=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'type' => 'file',
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'callback' => $this->createJSCallback($js),
            'data' => $data,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function createBodyArchiveItem($title, $tooltip, $data=null, $js=null, $disabled=false, array $extra = array()) {
        return array_merge(array(
            'type' => 'archive',
            'title' => (string)$title,
            'tooltip' => (string)$tooltip,
            'callback' => $this->createJSCallback($js),
            'data' => $data,
            'disabled' => (bool)$disabled
        ), $extra);
    }

    public function generateOptions() {
        $js = "\t".'{';
        $stack = array();
        $stack_indexes = array();
        $stack_objects = array();
        $stack[]=$this->_options;
        $stack_indexes[]=0;
        $stack_objects[]=false;
        $is_object = false;
        while(count($stack)>0) {
            $index = count($stack)-1;
            $prefix = "\r\n".str_repeat("\t", $index+2);
            $total = count($stack[$index]);
            $co = 0;
            if ($stack_indexes[$index]>0 && $is_object) {
                $js .= $prefix.'}';
            } else if ($stack_indexes[$index]>0) {
                $js .= $prefix.']';
            }
            if ($stack_indexes[$index]>0 && $stack_indexes[$index]<$total) {
                $js .= ',';
            }
            if ($stack_indexes[$index]<$total) {
                foreach($stack[$index] as $option_name=>$option_value) {
                    $co++;
                    if ($co<=$stack_indexes[$index]) continue;
                    $js .= $prefix;
                    if (!is_int($option_name)) {
                        $js .= "'".$option_name."': ";
                        if (is_array($option_value)) $js .= $prefix;
                    }
                    if (is_array($option_value) && (is_int(key($option_value)) || empty($option_value))) {
                        $js .= '[';
                        $is_object = false;
                    } else if (is_array($option_value)) {
                        $js .= '{';
                        $is_object = true;
                    }
                    if (is_array($option_value)) {
                        $stack_indexes[$index] = $co;
                        $stack[]=$option_value;
                        $stack_indexes[]=0;
                        $stack_objects[]=$is_object;
                        $co=0;
                        break;
                    } else if (is_int($option_value)) {
                        $js .= $option_value;
                    } else if (is_bool($option_value)) {
                        $js .= $option_value ? 'true': 'false';
                    } else if (is_null($option_value)) {
                        $js .= 'null';
                    } else {
                        if ($option_name=='callback' || strpos($option_name,'on')===0) {
                            $js .= $option_value;
                        } else {
                            $js .= "'".$option_value."'";
                        }
                    }
                    if ($co<$total) $js .= ',';
                }
            } else {
                $co = $total;
            }
            if ($co==$total) {
                array_pop($stack);
                array_pop($stack_indexes);
                $is_object = array_pop($stack_objects);
            }
        }
        $js .= "\r\n\t".'}';
        $stack = null;
        $stack_indexes = null;
        $stack_objects = null;
        return $js;
    }

    public function setExtraJS($js) {
        if (substr(trim($js),-1)!=';') $js=trim($js).';';
        $this->_extra_js = $js;
    }

    public function addStrings(array $strings) {
        foreach($strings as $str) {
            Dash\Dash::getInstance()->registerWindowString($str);
        }
    }

    public function addVariables(array $vars, $once = false) {
        foreach($vars as $name=>$val) {
            if ($once) {
                Dash\Dash::getInstance()->registerWindowVariableOnce($name, $val);
            } else {
                Dash\Dash::getInstance()->registerWindowVariable($name, $val);
            }
        }
    }

    public function includeJS($path) {
        Dash\Dash::getInstance()->registerWindowScript($path);
    }

    public function render() {
        if (empty($this->_options['menuItems']) && (!empty($this->_default_menu) || !empty($this->_default_menu_dropdown))) {
            if (!empty($this->_default_menu_dropdown)) {
                $this->addDefaultMenuItem(
                    $this->createMenuItem($this->_default_menu_title, $this->_default_menu_dropdown)
                );
            }
            $this->setMenuItems($this->_default_menu);
        }
        if ($this->_selection_links) {
            if (!empty($this->_default_context_menu)) {
                $this->addDefaultContextMenuItem(
                    $this->createContextMenuSeparator()
                );
            }
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem(Locale::t('Select all'), 'glyphicon glyphicon-ok-sign', 'desk_window_select_items(this);', 'select')
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem(Locale::t('Unselect all'), 'glyphicon glyphicon-ok-circle', 'desk_window_unselect_items(this);', 'select')
            );
        }
        if (empty($this->_options['contextMenuItems']) && !empty($this->_default_context_menu)) {
            $this->setContextMenuItems($this->_default_context_menu);
        }
        if (empty($this->_options['toolbarItems']) && !empty($this->_default_toolbar)) {
            $this->setToolbarItems($this->_default_toolbar);
        }
        if (empty($this->_options['sidebarItems']) && !empty($this->_default_sidebar)) {
            $this->setSidebarItems($this->_default_sidebar);
        }
        if ($this->_help_url) {
            $this->setOption('help_url', $this->_help_url);
        }
        $js = 'var '.$this->getJSClassName().' = function(data) {'."\r\n\t";
        $js .= 'desk_window('.'\''.$this->getJSClassName().'\','."\r\n".$this->generateOptions().', data);'."\r\n";
        $js .= '};'."\r\n";
        $js .= $this->_extra_js;

        return $js;
    }

    public function __toString() {
        return $this->render();
    }
}