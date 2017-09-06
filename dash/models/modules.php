<?php
/**
 * Zira project.
 * modules.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Modules extends Model {
    public function install($module) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_modules = Dash\Windows\Modules::getAvailableModules();
        $active_modules = Zira\Config::get('modules');

        if (!array_key_exists($module, $available_modules)) return array('error' => Zira\Locale::t('An error occurred'));
        if (in_array($module, $active_modules)) return array('error' => Zira\Locale::t('Cannot install activated module'));

        $tables = Dash\Windows\Modules::getAvailableModuleTables($module);
        if (Dash\Windows\Modules::isModuleTablesInstalled($tables)) return array('error' => Zira\Locale::t('Module is already installed'));

        try {
            foreach ($tables as $table) {
                $table->install();
            }
        } catch(\Exception $e) {
            return array('error' => Zira\Locale::t('Failed to install module'));
        }

        Zira\Models\Option::raiseVersion();

        return array('message'=>Zira\Locale::t('Module successfully installed'),'reload'=>$this->getJSClassName());
    }

    public function uninstall($module) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_modules = Dash\Windows\Modules::getAvailableModules();
        $active_modules = Zira\Config::get('modules');

        if (!array_key_exists($module, $available_modules)) return array('error' => Zira\Locale::t('An error occurred'));
        if (in_array($module, $active_modules)) return array('error' => Zira\Locale::t('Cannot uninstall activated module'));

        $tables = Dash\Windows\Modules::getAvailableModuleTables($module);
        if (!Dash\Windows\Modules::isModuleTablesInstalled($tables)) return array('error' => Zira\Locale::t('Module is not installed'));

        try {
            foreach ($tables as $table) {
                $table->uninstall();
            }
            
            Zira\Models\Widget::getCollection()
                                ->delete()
                                ->where('module','=',$module)
                                ->execute();
        } catch(\Exception $e) {
            return array('error' => Zira\Locale::t('Failed to uninstall module'));
        }

        Zira\Models\Option::raiseVersion();

        return array('message'=>Zira\Locale::t('Module successfully uninstalled'),'reload'=>$this->getJSClassName());
    }

    public function activate($module) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_modules = Dash\Windows\Modules::getAvailableModules();
        $active_modules = Zira\Config::get('modules');

        if (!array_key_exists($module, $available_modules)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!in_array($module, $active_modules)) {
            $active_modules []= $module;

            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','modules')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'modules';
            $optionObj->value = Zira\Models\Option::convertArrayToString($active_modules);
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }

    public function deactivate($module) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_modules = Dash\Windows\Modules::getAvailableModules();
        $active_modules = Zira\Config::get('modules');

        if (!array_key_exists($module, $available_modules)) return array('error' => Zira\Locale::t('An error occurred'));
        if (in_array($module, $active_modules)) {
            $index = array_search($module, $active_modules);
            unset($active_modules[$index]);

            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','modules')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'modules';
            $optionObj->value = Zira\Models\Option::convertArrayToString($active_modules);
            $optionObj->module = 'zira';
            $optionObj->save();
            
            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }
}