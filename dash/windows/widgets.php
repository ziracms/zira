<?php
/**
 * Zira project.
 * widgets.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Widgets extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-modal-window';
    protected static $_title = 'Widgets';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $placeholders_menu = array();
        $placeholders = Zira\Models\Widget::getPlaceholders();
        foreach($placeholders as $placeholder_id=>$placeholder_name) {
            $placeholders_menu []= $this->createMenuDropdownItem($placeholder_name, 'glyphicon glyphicon-filter', 'desk_call(dash_widgets_placeholders_filter, this, element);', 'placeholders', false, array('placeholder'=>$placeholder_id));
        }

        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_widgets_deactivate, this);', 'delete', true, array('typo'=>'deactivate')),
                $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_widgets_activate, this);', 'delete', true, array('typo'=>'activate')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_widgets_edit, this);', 'edit', true, array('typo'=>'edit')),
                $this->createMenuDropdownItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_widgets_copy, this);', 'delete', true, array('typo'=>'copy')),
                $this->createMenuDropdownItem($this->_delete_action_text, 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete'),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Up'), 'glyphicon glyphicon-triangle-top', 'desk_call(dash_widgets_up, this);', 'edit', true, array('typo'=>'up')),
                $this->createMenuDropdownItem(Zira\Locale::t('Down'), 'glyphicon glyphicon-triangle-bottom', 'desk_call(dash_widgets_down, this);', 'edit', true, array('typo'=>'down'))
            )),
            $this->createMenuItem(Zira\Locale::t('Placeholders'), $placeholders_menu)
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_widgets_deactivate, this);', 'delete', true, array('typo'=>'deactivate')),
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_widgets_activate, this);', 'delete', true, array('typo'=>'activate')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_widgets_edit, this);', 'edit', true, array('typo'=>'edit')),
            $this->createContextMenuItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_widgets_copy, this);', 'delete', true, array('typo'=>'copy')),
            $this->createContextMenuItem($this->_delete_action_text, 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete'),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Up'), 'glyphicon glyphicon-triangle-top', 'desk_call(dash_widgets_up, this);', 'edit', true, array('typo'=>'up')),
            $this->createContextMenuItem(Zira\Locale::t('Down'), 'glyphicon glyphicon-triangle-bottom', 'desk_call(dash_widgets_down, this);', 'edit', true, array('typo'=>'down'))
        ));

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widgets_select, this);'
            )
        );

        $this->addDefaultOnLoadScript(
                'desk_call(dash_widgets_load, this);'
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widgets_edit, this);'
            )
        );

        $this->setOnDeleteItemsJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widgets_delete, this);'
            )
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widgets_open, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_widgets_drop, this, element);'
            )
        );

        $this->setData(array(
            'placeholder' => null
        ));

        $this->addVariables(array(
            'dash_widget_status_active_id' => Zira\Models\Widget::STATUS_ACTIVE,
            'dash_widget_status_not_active_id' => Zira\Models\Widget::STATUS_NOT_ACTIVE,
            'dash_widgets_blank_src' => Zira\Helper::imgUrl('blank.png'),
            'dash_widgets_widget_wnd' => Dash::getInstance()->getWindowJSName(Widget::getClass())
        ));

        $this->includeJS('dash/widgets');
    }

    public static function getAvailableModuleWidgets($module) {
        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'widgets';
        if (!file_exists($dir) || !is_dir($dir)) return array();
        $widgets = array();
        $d = opendir($dir);
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || is_dir($dir. DIRECTORY_SEPARATOR . $f)) continue;
            if (!preg_match('/^([a-zA-Z0-9]+)\.php$/', $f, $matches)) continue;
            $class = '\\'.ucfirst($module).'\\Widgets\\'.ucfirst($matches[1]);
            try {
                if (class_exists($class)) {
                    $widget = new $class;
                    if ($widget instanceof Zira\Widget) {
                        $widgets [$class]= $widget;
                    } else {
                        unset($widget);
                    }
                }
            } catch(\Exception $e) {
                Zira\Log::exception($e);
            }
        }
        closedir($d);
        return $widgets;
    }

    public static function getAvailableWidgets() {
        $available_widgets = array();
        $active_modules = array_merge(array('zira'),Zira\Config::get('modules'));
        foreach ($active_modules as $module) {
            $widgets = self::getAvailableModuleWidgets($module);
            if (!empty($widgets)) {
                $available_widgets = array_merge($available_widgets, $widgets);
            }
        }
        return $available_widgets;
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $placeholders = Zira\Models\Widget::getPlaceholders();
        $placeholder = Zira\Request::post('placeholder');
        if ($placeholder && !array_key_exists($placeholder, $placeholders)) {
            $placeholder = null;
        }

        $categories = Zira\Models\Category::getArray(false);
        $_categories = array(
            -1 => Zira\Locale::t('All pages'),
            0 => Zira\Locale::t('Home page')
        );
        $categories = $_categories + $categories;

        $available_widgets = self::getAvailableWidgets();

        $active_widgets = array();
        $inactive_widgets = array();
        $user_widgets = array();
        $db_widgets = Zira\Models\Widget::getCollection()->order_by('sort_order', 'asc')->get();
        foreach($db_widgets as $widget) {
            if (!array_key_exists($widget->name, $available_widgets)) continue;
            if ($widget->active == Zira\Models\Widget::STATUS_ACTIVE) {
                $active_widgets []= $widget;
            } else {
                $inactive_widgets []= $widget;
            }
            $user_widgets []= $widget->name;
        }
        $other_widgets = array();
        foreach ($available_widgets as $class=>$widget) {
            if (in_array($class, $user_widgets)) continue;
            $other_widgets [$class]= $widget;
        }
        $widgets = array_merge($active_widgets, $inactive_widgets);

        $items = array();
        foreach ($widgets as $widget) {
            $_widget = $available_widgets[$widget->name];
            if (!$_widget->isEditable()) continue;
            if ($placeholder && $widget->placeholder!=$placeholder) continue;
            $_widget->setData($widget->params);
            $title = Zira\Locale::tm($_widget->getTitle(), $widget->module);
            $suffix = '';
            if (!$placeholder) $suffix .= ' - '.$placeholders[$widget->placeholder];
            $category_id = $widget->category_id;
            if ($category_id!==null) {
                $suffix .= ' - '.$categories[$category_id];
            } else if ($category_id === null && !$widget->record_id && !$widget->url) {
                $suffix .= ' - '.$categories[-1];
            }
            if ($widget->record_id) {
                $record = new Zira\Models\Record($widget->record_id);
                if ($record->loaded()) $suffix .= ' - '.$record->title;
            } else if ($widget->url) {
                $suffix .= ' - '.$widget->url;
            }
            $items[]=$this->createBodyItem(Zira\Helper::html($title), Zira\Helper::html($title.$suffix), Zira\Helper::imgUrl('drag.png'), $widget->id, null, false, array('parent'=>'widgets','activated'=>$widget->active,'installed'=>true,'sort_order'=>$widget->sort_order));
        }
        foreach ($other_widgets as $class=>$widget) {
            if (!$widget->isEditable() || $widget->isDynamic()) continue;
            if ($placeholder && $widget->getPlaceholder()!=$placeholder) continue;
            $module = strtolower(substr($class, 1, strpos($class, '\\', 1)-1));
            $title = Zira\Locale::tm($widget->getTitle(),$module);
            $suffix = '';
            //if (!$placeholder) $suffix .= ' - '.$placeholders[$widget->getPlaceholder()];
            //$suffix .= ' - '.$categories[-1];
            $items[]=$this->createBodyItem($title, $title.$suffix, Zira\Helper::imgUrl('drag.png'), $class, null, false, array('activated'=>Zira\Models\Widget::STATUS_NOT_ACTIVE,'installed'=>false));
        }

        $this->setBodyItems($items);

        if ($placeholder) {
            $this->setTitle(Zira\Locale::t(self::$_title).' - '.$placeholders[$placeholder]);
        } else {
            $this->setTitle(Zira\Locale::t(self::$_title));
        }

        $this->setData(array(
            'placeholder' => $placeholder
        ));
    }
}