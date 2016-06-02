<?php
/**
 * Zira project.
 * modules.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Modules extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-certificate';
    protected static $_title = 'Modules';

    protected $_help_url = 'zira/help/modules';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Install'), 'glyphicon glyphicon-ok-sign', 'desk_call(dash_modules_install, this);', 'edit', true, array('typo'=>'install')),
                $this->createMenuDropdownItem(Zira\Locale::t('Uninstall'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_modules_uninstall, this);', 'edit', true, array('typo'=>'uninstall')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_modules_activate, this);', 'edit', true, array('typo'=>'activate')),
                $this->createMenuDropdownItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_modules_deactivate, this);', 'edit', true, array('typo'=>'deactivate'))
            ))
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Install'), 'glyphicon glyphicon-ok-sign', 'desk_call(dash_modules_install, this);', 'edit', true, array('typo'=>'install')),
            $this->createContextMenuItem(Zira\Locale::t('Uninstall'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_modules_uninstall, this);', 'edit', true, array('typo'=>'uninstall')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_modules_activate, this);', 'edit', true, array('typo'=>'activate')),
            $this->createContextMenuItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_modules_deactivate, this);', 'edit', true, array('typo'=>'deactivate'))
        ));

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_modules_select, this);'
            )
        );

        $this->addDefaultOnLoadScript(
                'desk_call(dash_modules_load, this);'
        );

        $this->setOnCloseJSCallback(
            $this->createJSCallback(
                'desk_call(dash_modules_close, this);'
            )
        );

        $this->addStrings(array(
            'Remove module from database ?'
        ));

        $this->addVariables(array(
            'dash_modules_widgets_wnd' => Dash::getInstance()->getWindowJSName(Widgets::getClass())
        ));

        $this->includeJS('dash/modules');
    }

    public static function getAvailableModules($excludeCore=true) {
        $modules = array();
        $dir = ROOT_DIR;
        $d = opendir($dir);
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_dir($dir. DIRECTORY_SEPARATOR . $f)) continue;
            if (strpos($f,'..')!==false) continue;
            if ($excludeCore && ($f=='zira' || $f=='dash')) continue;
            if (!file_exists($dir. DIRECTORY_SEPARATOR . $f . DIRECTORY_SEPARATOR . $f . '.php')) continue;
            $class = '\\'.ucfirst($f).'\\'.ucfirst($f);
            try {
                if (class_exists($class)) {
                    $modules[$f]=ucfirst($f);
                }
            } catch(\Exception $e) {
                Zira\Log::exception($e);
            }
        }
        closedir($d);
        return $modules;
    }

    public static function getAvailableModuleTables($module) {
        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'install';
        if (!file_exists($dir) || !is_dir($dir)) return array();
        $tables = array();
        $d = opendir($dir);
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || is_dir($dir. DIRECTORY_SEPARATOR . $f)) continue;
            if (!preg_match('/^([a-zA-Z0-9]+)\.php$/', $f, $matches)) continue;
            $class = '\\'.ucfirst($module).'\\Install\\'.ucfirst($matches[1]);
            try {
                if (class_exists($class)) {
                    $table = new $class;
                    if ($table instanceof Zira\Db\Table) {
                        $tables []= $table;
                    } else {
                        unset($table);
                    }
                }
            } catch(\Exception $e) {
                Zira\Log::exception($e);
            }
        }
        closedir($d);
        return $tables;
    }

    public static function isModuleTablesInstalled($tables, $installed_tables=null) {
        if ($installed_tables===null) {
            $installed_tables = Zira\Db\Db::getTables();
        }
        $installed = true;
        foreach ($tables as $table) {
            if (!in_array($table->getName(), $installed_tables)) {
                $installed = false;
                break;
            }
        }
        return $installed;
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_modules = self::getAvailableModules();
        $active_modules = Zira\Config::get('modules');

        $items = array();
        foreach ($available_modules as $key=>$name) {
            $tables = self::getAvailableModuleTables($key);
            $installed = self::isModuleTablesInstalled($tables);
            $description = '';
            $author = '';
            $version = '';

            if (file_exists(ROOT_DIR. DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'module.meta')) {
                $meta = @parse_ini_file(ROOT_DIR. DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'module.meta', true);
                if (!empty($meta) && is_array($meta) && array_key_exists('meta', $meta)) {
                    $module = strtolower($name);
                    if (array_key_exists('name', $meta['meta'])) $name = Zira\Locale::tm($meta['meta']['name'], $module);
                    if (array_key_exists('description', $meta['meta'])) $description = Zira\Locale::tm($meta['meta']['description'], $module);
                    if (array_key_exists('author', $meta['meta'])) $author = $meta['meta']['author'];
                    if (array_key_exists('version', $meta['meta'])) $version = $meta['meta']['version'];
                }
            }

            $title = Zira\Helper::html($name);
            if (empty($description)) $description = Zira\Helper::html($name);
            if (!empty($author)) $description .= "\r\n".Zira\Locale::t('Author: %s', Zira\Helper::html($author));
            if (!empty($version)) $description .= "\r\n".Zira\Locale::t('Version: %s', Zira\Helper::html($version));
            $items[]=$this->createBodyFileItem($title, $description, $key, null, false, array('activated'=>in_array($key, $active_modules),'installable'=>count($tables)>0,'installed'=>$installed));
        }

        $this->setBodyItems($items);
    }
}