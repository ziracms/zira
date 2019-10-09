<?php
/**
 * Zira project.
 * themes.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Themes extends Model {
    public function activate($theme) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) || !Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_themes = $this->getWindow()->getAvailableThemes();
        $current_theme = Zira\Config::get('theme');

        if (!array_key_exists($theme, $available_themes)) return array('error' => Zira\Locale::t('An error occurred'));
        if ($theme != $current_theme) {
            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','theme')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'theme';
            $optionObj->value = $theme;
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Config::set('theme', $theme);
            Zira\View::setTheme($theme);

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }
    
    public function dashtheme($theme) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) || !Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_themes = $this->getWindow()->getAvailableThemes();
        $current_theme = Zira\Config::get('theme');
        $current_theme = Zira\Config::get('dashtheme', $current_theme);

        if (!array_key_exists($theme, $available_themes)) return array('error' => Zira\Locale::t('An error occurred'));
        if ($theme != $current_theme) {
            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','dashtheme')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'dashtheme';
            $optionObj->value = $theme;
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Config::set('dashtheme', $theme);

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }
}