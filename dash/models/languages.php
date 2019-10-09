<?php
/**
 * Zira project.
 * languages.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Languages extends Model {
    public function activate($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();

        if (!array_key_exists($language, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!in_array($language, $active_languages)) {
            $active_languages[]=$language;

            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','languages')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'languages';
            $optionObj->value = Zira\Models\Option::convertArrayToString($active_languages);
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }

    public function deactivate($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();
        $default_language = Zira\Config::get('language');

        if ($language==$default_language) return array('error' => Zira\Locale::t('An error occurred'));
        if (!array_key_exists($language, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));
        if (in_array($language, $active_languages)) {
            $language_index=array_search($language, $active_languages);
            unset($active_languages[$language_index]);

            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','languages')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'languages';
            $optionObj->value = Zira\Models\Option::convertArrayToString($active_languages);
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }

    public function setDefault($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();
        $default_language = Zira\Config::get('language');

        if (!array_key_exists($language, $available_languages) || !in_array($language, $active_languages)) return array('error' => Zira\Locale::t('An error occurred'));
        if ($language!=$default_language) {
            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','language')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'language';
            $optionObj->value = $language;
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }
    
    public function setPanelDefault($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();

        if (!array_key_exists($language, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));
        $option = Zira\Models\Option::getCollection()
                                            ->select('id')
                                            ->where('name','=','dash_language')
                                            ->get(0);

        if (!$option) {
            $optionObj = new Zira\Models\Option();
        } else {
            $optionObj = new Zira\Models\Option($option->id);
        }

        $optionObj->name = 'dash_language';
        $optionObj->value = $language;
        $optionObj->module = 'zira';
        $optionObj->save();

        Zira\Models\Option::raiseVersion();

        return array('reload'=>$this->getJSClassName());
    }

    public function pickUp($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();

        if (!array_key_exists($language, $available_languages) || !in_array($language, $active_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $languages = array();
        foreach($active_languages as $active_language) {
            if ($active_language == $language && count($languages)>0) {
                $lang_in_stack = array_pop($languages);
                $languages[]=$active_language;
                $languages[]=$lang_in_stack;
                continue;
            }
            $languages[]=$active_language;
        }

        if (count($languages)!=count($active_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $option = Zira\Models\Option::getCollection()
                                            ->select('id')
                                            ->where('name','=','languages')
                                            ->get(0);

        if (!$option) {
            $optionObj = new Zira\Models\Option();
        } else {
            $optionObj = new Zira\Models\Option($option->id);
        }

        $optionObj->name = 'languages';
        $optionObj->value = Zira\Models\Option::convertArrayToString($languages);
        $optionObj->module = 'zira';
        $optionObj->save();

        Zira\Models\Option::raiseVersion();

        return array('reload'=>$this->getJSClassName());
    }

    public function pullDown($language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();

        if (!array_key_exists($language, $available_languages) || !in_array($language, $active_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $languages = array();
        $lang_in_stack = null;
        foreach($active_languages as $active_language) {
            if ($active_language == $language) {
                $lang_in_stack = $active_language;
                continue;
            }
            $languages[]=$active_language;
            if ($lang_in_stack!==null) {
                $languages[]=$lang_in_stack;
                $lang_in_stack = null;
            }
        }
        if ($lang_in_stack!==null) {
            $languages[]=$lang_in_stack;
            $lang_in_stack = null;
        }

        if (count($languages)!=count($active_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $option = Zira\Models\Option::getCollection()
                                            ->select('id')
                                            ->where('name','=','languages')
                                            ->get(0);

        if (!$option) {
            $optionObj = new Zira\Models\Option();
        } else {
            $optionObj = new Zira\Models\Option($option->id);
        }

        $optionObj->name = 'languages';
        $optionObj->value = Zira\Models\Option::convertArrayToString($languages);
        $optionObj->module = 'zira';
        $optionObj->save();

        Zira\Models\Option::raiseVersion();

        return array('reload'=>$this->getJSClassName());
    }
    
    public function drag($languages, $orders) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        if (!is_array($languages) || !is_array($orders) || count($languages)<2 || count($orders)<2 || count($languages)!=count($orders)) return array('error' => Zira\Locale::t('An error occurred'));

        $available_languages = $this->getWindow()->getAvailableLanguages();
        $active_languages = $this->getWindow()->getActiveLanguages();

        foreach($languages as $language) {
            if (!in_array($language, $active_languages)) return array('error' => Zira\Locale::t('An error occurred'));
        }
        
        $_sort_orders = array();
        $sort_order = 0;
        foreach($active_languages as $active_language) {
            $sort_order++;
            $_sort_orders[$active_language] = $sort_order;
        }
        
        foreach($languages as $index=>$language) {
            $_sort_orders[$language] = $orders[$index];
        }
        
        asort($_sort_orders);
        $sorted = array_keys($_sort_orders);

        if (count($sorted)!=count($active_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $option = Zira\Models\Option::getCollection()
                                            ->select('id')
                                            ->where('name','=','languages')
                                            ->get(0);

        if (!$option) {
            $optionObj = new Zira\Models\Option();
        } else {
            $optionObj = new Zira\Models\Option($option->id);
        }

        $optionObj->name = 'languages';
        $optionObj->value = Zira\Models\Option::convertArrayToString($sorted);
        $optionObj->module = 'zira';
        $optionObj->save();

        Zira\Models\Option::raiseVersion();

        return array('reload'=>$this->getJSClassName());
    }
}