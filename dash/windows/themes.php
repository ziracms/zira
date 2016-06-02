<?php
/**
 * Zira project.
 * themes.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Themes extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-eye-open';
    protected static $_title = 'Themes';

    protected $_help_url = 'zira/help/themes';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_themes_activate, this);', 'edit', true, array('typo'=>'activate')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Preview'), 'glyphicon glyphicon-zoom-in', 'desk_call(dash_themes_preview, this);', 'edit', true, array('typo'=>'preview'))
            ))
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_themes_activate, this);', 'edit', true, array('typo'=>'activate')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Preview'), 'glyphicon glyphicon-zoom-in', 'desk_call(dash_themes_preview, this);', 'edit', true, array('typo'=>'preview'))
        ));

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_themes_select, this);'
            )
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_themes_preview, this);'
            )
        );

        $this->addVariables(array(
            'dash_themes_blank_src' => Zira\Helper::imgUrl('blank.png')
        ));

        $this->includeJS('dash/themes');
    }

    public function getAvailableThemes() {
        $themes = array();
        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR;
        $d = opendir($dir);
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_dir($dir. DIRECTORY_SEPARATOR . $f)) continue;
            $metadata = array();
            if (file_exists($dir. DIRECTORY_SEPARATOR . $f . DIRECTORY_SEPARATOR . 'theme.meta')) {
                $meta = @parse_ini_file($dir . DIRECTORY_SEPARATOR . $f . DIRECTORY_SEPARATOR . 'theme.meta', true);
                if (!empty($meta) && is_array($meta) && array_key_exists('meta', $meta)) {
                    $metadata = $meta['meta'];
                }
            }
            if (!array_key_exists('name', $metadata)) $metadata['name'] = ucfirst($f);
            $themes[$f] = $metadata;
        }
        closedir($d);
        return $themes;
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) || !Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_themes = $this->getAvailableThemes();
        $current_theme = Zira\Config::get('theme');

        $items = array();
        foreach ($available_themes as $key=>$meta) {
            if (array_key_exists('preview', $meta)) $preview = Zira\Helper::baseUrl(THEMES_DIR . '/' . $key . '/' . Zira\Helper::html($meta['preview']) . '.jpg');
            else $preview = Zira\Helper::imgUrl('blank.png');

            $author = '';
            if (array_key_exists('author', $meta)) $author = Zira\Locale::t('Author: %s',Zira\Helper::html($meta['author']));

            $active = '';
            if ($key == $current_theme) $active = ' *';
            $items[]=$this->createBodyItem(Zira\Helper::html($meta['name']).$active, $author, $preview, $key, null, false, array('activated'=>($key==$current_theme)));
        }

        $this->setBodyItems($items);
    }
}