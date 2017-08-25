<?php
/**
 * Zira project.
 * recordsettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Recordsettings extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-file';
    protected static $_title = 'Records settings';

    protected $_help_url = 'zira/help/record-settings';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();
        if (!array_key_exists('slider_enabled', $configs)) $configs['slider_enabled'] = 1;
        if (!array_key_exists('gallery_enabled', $configs)) $configs['gallery_enabled'] = 1;
        if (!array_key_exists('files_enabled', $configs)) $configs['files_enabled'] = 1;
        if (!array_key_exists('audio_enabled', $configs)) $configs['audio_enabled'] = 1;
        if (!array_key_exists('video_enabled', $configs)) $configs['video_enabled'] = 1;
        if (!array_key_exists('comments_enabled', $configs)) $configs['comments_enabled'] = 1;
        if (!array_key_exists('rating_enabled', $configs)) $configs['rating_enabled'] = 0;
        if (!array_key_exists('display_author', $configs)) $configs['display_author'] = 0;
        if (!array_key_exists('display_date', $configs)) $configs['display_date'] = 0;
        if (!array_key_exists('jpeg_quality', $configs)) $configs['jpeg_quality'] = Zira\Image::QUALITY_JPEG;

        $form = new \Dash\Forms\Recordsettings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}