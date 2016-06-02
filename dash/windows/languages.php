<?php
/**
 * Zira project.
 * languages.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Languages extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Localisation';

    protected $_help_url = 'zira/help/languages';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_languages_deactivate, this);', 'edit', true, array('typo'=>'deactivate')),
                $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_languages_activate, this);', 'edit', true, array('typo'=>'activate')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Make default'), 'glyphicon glyphicon-flag', 'desk_call(dash_languages_default, this);', 'edit', true, array('typo'=>'default')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Up'), 'glyphicon glyphicon-triangle-top', 'desk_call(dash_languages_up, this);', 'edit', true, array('typo'=>'up')),
                $this->createMenuDropdownItem(Zira\Locale::t('Down'), 'glyphicon glyphicon-triangle-bottom', 'desk_call(dash_languages_down, this);', 'edit', true, array('typo'=>'down')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Custom translates'), 'glyphicon glyphicon-text-color', 'desk_call(dash_languages_translates, this);', 'edit', true, array('typo'=>'translates'))
            ))
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_languages_deactivate, this);', 'edit', true, array('typo'=>'deactivate')),
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_languages_activate, this);', 'edit', true, array('typo'=>'activate')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Make default'), 'glyphicon glyphicon-flag', 'desk_call(dash_languages_default, this);', 'edit', true, array('typo'=>'default')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Up'), 'glyphicon glyphicon-triangle-top', 'desk_call(dash_languages_up, this);', 'edit', true, array('typo'=>'up')),
            $this->createContextMenuItem(Zira\Locale::t('Down'), 'glyphicon glyphicon-triangle-bottom', 'desk_call(dash_languages_down, this);', 'edit', true, array('typo'=>'down')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Custom translates'), 'glyphicon glyphicon-text-color', 'desk_call(dash_languages_translates, this);', 'edit', true, array('typo'=>'translates')),
        ));

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_languages_translates, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_languages_load, this);');

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_languages_select, this);'
            )
        );

        $this->setData(array(
            'db_translated_enabled' => boolval(Zira\Config::get('db_translates'))
        ));

        $this->addStrings(array(
            'DB translates are not enabled'
        ));

        $this->addVariables(array(
            'dash_languages_translates_wnd' => Dash::getInstance()->getWindowJSName(Translates::getClass())
        ));

        $this->includeJS('dash/languages');
    }

    public function getAvailableLanguages() {
        $available_languages = array();
        $d = opendir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR);
        while (($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR . DIRECTORY_SEPARATOR . $f)) continue;
            $lang_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                            LANGUAGES_DIR . DIRECTORY_SEPARATOR .
                            $f . DIRECTORY_SEPARATOR .
                            $f . '.php';
            if (!file_exists($lang_file) || !is_readable(($lang_file))) continue;
            $strings = include($lang_file);
            if (!is_array($strings)) continue;
            $available_languages[$f]=array_key_exists($f,$strings) ? $strings[$f] : $f;
        }
        return $available_languages;
    }

    public function getActiveLanguages() {
        return Zira\Config::get('languages');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getAvailableLanguages();
        $active_languages = $this->getActiveLanguages();
        $default_language = Zira\Config::get('language');

        $items = array();
        foreach ($active_languages as $language_key) {
            if (!array_key_exists($language_key, $available_languages)) continue;
            $language_name = $available_languages[$language_key];
            if ($language_key==$default_language) $language_name.=' *';
            $items[]=$this->createBodyArchiveItem($language_name, $language_name, $language_key, null, false, array('activated'=>in_array($language_key, $active_languages),'is_default'=>($language_key==$default_language)));
        }
        foreach ($available_languages as $language_key=>$language_name) {
            if (in_array($language_key, $active_languages)) continue;
            $items[]=$this->createBodyArchiveItem($language_name, $language_name, $language_key, null, false, array('activated'=>in_array($language_key, $active_languages),'is_default'=>($language_key==$default_language)));
        }

        $this->setBodyItems($items);

        $this->setData(array(
            'db_translated_enabled' => boolval(Zira\Config::get('db_translates'))
        ));
    }
}