<?php
/**
 * Zira project.
 * translates.php
 * (c)2016 http://dro1d.ru
 */
namespace Dash\Models;

use Zira;
use Zira\Permission;

class Translates extends Model {
    public function add($string, $language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        if (empty($string) || empty($language)) return array('error' => Zira\Locale::t('An error occurred'));

        $available_languages = $this->getWindow()->getAvailableLanguages();
        if (!array_key_exists($language, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $exists = Zira\Models\Translate::getCollection()
                                    ->count()
                                    ->where('name','=',$string)
                                    ->and_where('language','=',$language)
                                    ->get('co')
                                    ;
        if ($exists) return array('error' => Zira\Locale::t('String already exists'));

        $string = str_replace("\r\n","\n", $string);

        $translate = new Zira\Models\Translate();
        $translate->module = 'custom';
        $translate->name = $string;
        $translate->value = $string;
        $translate->language = $language;
        $translate->save();

        return array('reload'=>$this->getJSClassName());
    }

    public function translate($id, $translate, $language) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        if (empty($id) || empty($language)) return array('error' => Zira\Locale::t('An error occurred'));

        $available_languages = $this->getWindow()->getAvailableLanguages();
        if (!array_key_exists($language, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $translateObj = new Zira\Models\Translate($id);
        if (!$translateObj->loaded() || $translateObj->language!=$language) return array('error' => Zira\Locale::t('An error occurred'));

        $translateObj->value = str_replace("\r\n","\n",$translate);
        $translateObj->save();

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $id) {
            $translateObj = new Zira\Models\Translate($id);
            if (!$translateObj->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $translateObj->delete();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}