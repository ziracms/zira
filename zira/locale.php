<?php
/**
 * Zira project
 * locale.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

use Zira\Models\Translate;

class Locale {
    const CACHE_KEY_PREFIX = 'translates';
    const COOKIE_NAME = 'zira_lang';
    const COOKIE_TIME = 31536000;
    protected static $language;

    protected static $_strings = array();
    protected static $_loaded = array();

    public static function init() {
         if (Config::get('detect_language') &&
            count(Config::get('languages'))>1 &&
            empty($_SERVER['HTTP_REFERER']) &&
            !Request::isRedirected() &&
            !Router::getRequest() &&
            !Router::getLanguage()
        ) {
            if (self::isRemembered()) {
                $locale = self::getRemembered();
            } else {
                $locale = self::detect();
            }
            if ($locale!=Config::get('language')) {
                Helper::setAddingLanguageToUrl(false);
                Response::redirect($locale);
            }
        }
    }

    public static function detect() {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return false;
        $locale = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $locale = explode(',', $locale[0]);
        $locale = explode('-', $locale[0]);
        $locale = strtolower($locale[0]);
        if (!in_array($locale, Config::get('languages'))) return false;
        return $locale;
    }

    public static function remember() {
        if (Request::isAjax() || Router::getModule()=='dash') return;
        if (count(Config::get('languages'))>1) {
            Cookie::set(self::COOKIE_NAME, self::getLanguage(), self::COOKIE_TIME);
        }
    }

    public static function getRemembered() {
        $language = Cookie::get(self::COOKIE_NAME);
        if (!$language) return false;
        if (!in_array($language, Config::get('languages'))) {
            Cookie::remove(self::COOKIE_NAME);
            return false;
        }
        return $language;
    }

    public static function isRemembered() {
        return (bool)self::getRemembered();
    }

    public static function load($language,$module=null) {
        if (!$language) $language = self::$language;
        if (!$language) return false;
        if (!$module) $module = $language;

        if (self::import($language, $module) || $language == DEFAULT_LANGUAGE) {
            self::$language = $language;
            self::loadJsStrings($language, $module);
            return true;
        }

        return false;
    }

    public static function getLanguageFileAbsPath($language,$prefix,$suffix='') {
        if (!empty($suffix)) $suffix = '.'.$suffix;

        $file = ROOT_DIR . DIRECTORY_SEPARATOR .
            LANGUAGES_DIR . DIRECTORY_SEPARATOR .
            $language . DIRECTORY_SEPARATOR .
            $prefix . $suffix . '.php';

        return $file;
    }

    public static function getStringsFromFile($file) {
        if (!file_exists($file)) return false;

        $strings = include($file);
        if (!is_array($strings)) return false;

        return $strings;
    }

    public static function removeStrings() {
        self::$_strings = array();
    }

    public static function addStrings(array $strings) {
        self::$_strings = array_merge(self::$_strings, $strings);
    }

    public static function import($language, $module) {
        $lang_file = self::getLanguageFileAbsPath($language,$module);
        return self::importTranslates($lang_file);
    }

    protected static function importTranslates($lang_file) {
        $strings = self::getStringsFromFile($lang_file);
        if (!$strings) return false;
        self::addStrings($strings);
        self::$_loaded[] = $lang_file;

        return true;
    }

    public static function loadJsStrings($language,$module) {
        $lang_file = self::getLanguageFileAbsPath($language,$module,'js');

        $strings = self::getStringsFromFile($lang_file);
        if (!$strings) return false;
        View::addJsStrings($strings);

        return true;
    }

    public static function getDBRows($language) {
        $rows = Cache::getArray(self::CACHE_KEY_PREFIX.'.'.$language);
        if ($rows === false) {
            $rows = Translate::getCollection()
                ->where('language', '=', $language)
                ->get();

            Cache::setArray(self::CACHE_KEY_PREFIX.'.'.$language, $rows);
        }
        return $rows;
    }

    public static function getStringsFromDb($language) {
        $rows = self::getDBRows($language);
        $strings = array();
        foreach($rows as $row) {
            $strings[$row->name] = $row->value;
        }
        return $strings;
    }

    public static function loadDBStrings($language=null) {
        if (!$language) $language = self::$language;
        if (!$language) return false;

        $strings = self::getStringsFromDb($language);
        self::addStrings($strings);
    }

    public static function isLoaded($language,$module=null) {
        if (!$language) $language = self::$language;
        if (!$language) return false;
        if (!$module) $module = $language;

        $lang_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            LANGUAGES_DIR . DIRECTORY_SEPARATOR .
            $language . DIRECTORY_SEPARATOR .
            $module . '.php';

        return in_array($lang_file, self::$_loaded);
    }

    public static function t($str, $arg = null) {
        if (array_key_exists($str, self::$_strings)) $str = self::$_strings[$str];
        if ($arg === null) {
            if (is_array($str)) $str = end($str);
            return $str;
        }
        if (!is_array($str) || !is_numeric($arg)) {
            if (is_array($str)) $str = end($str);
            return sprintf($str, $arg);
        } else {
            $index = self::getPluralIndex($arg, self::getLanguage());
            if (!array_key_exists($index, $str)) return sprintf(end($str), $arg);
            return sprintf($str[$index], $arg);
        }
    }

    public static function tm($str, $module, $arg = null, $language = null) {
        if (array_key_exists($str, self::$_strings)) return self::t($str, $arg);
        else if (!self::isLoaded(null,$module)) {
            //self::load($language, $module);
            if (!$language) $language = self::$language;
            self::import($language, $module);
            return self::t($str, $arg);
        } else {
            return self::t($str, $arg);
        }
    }

    public static function getLanguage() {
        return self::$language;
    }
    
    public static function setLanguage($language) {
        self::$language = $language;
    }

    public static function getStrings() {
        return self::$_strings;
    }

    protected static function getPluralIndex($int, $language) {
        if ($language == 'ru') {
            return self::getPluralIndexLanguageRu($int);
        } else {
            return self::getPluralIndexLanguageDefault($int);
        }
    }

    protected static function getPluralIndexLanguageRu($int) {
        $a = $int % 10;
        $b = $int % 100;
        if ($a == 0 || $a >= 5 || ($b >= 10 && $b <= 20)) return 2;
        else if ($a >= 2 && $a <= 4) return 1;
        else if ($a == 1) return 0;
        else return -1;
    }

    protected static function getPluralIndexLanguageDefault($int) {
        if ($int>1) return 1;
        else if ($int == 1) return 0;
        else return -1;
    }

    public static function getLanguagesArray() {
        $active_languages = Config::get('languages');
        $languages = array();
        $d = opendir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR);
        while (($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR . DIRECTORY_SEPARATOR . $f)) continue;
            if (!in_array($f, $active_languages)) continue;
            $lang_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                            LANGUAGES_DIR . DIRECTORY_SEPARATOR .
                            $f . DIRECTORY_SEPARATOR .
                            $f . '.php';
            if (!file_exists($lang_file) || !is_readable(($lang_file))) continue;
            $strings = include($lang_file);
            if (!is_array($strings)) continue;
            $languages[$f]=array_key_exists($f,$strings) ? $strings[$f] : $f;
        }
        return $languages;
    }
}