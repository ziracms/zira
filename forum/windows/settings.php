<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Forum settings';

    protected $_help_url = 'zira/help/forum-settings';

    public $item;

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
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Forum\Forum::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Forum\Forms\Settings();
        if (!array_key_exists('forum_limit', $configs)) $configs['forum_limit'] = 10;
        if (!array_key_exists('forum_min_chars', $configs)) $configs['forum_min_chars'] = 10;
        if (!array_key_exists('forum_file_max_size', $configs)) $configs['forum_file_max_size'] = \Forum\Models\File::DEFAULT_MAX_SIZE;
        if (!array_key_exists('forum_file_ext', $configs)) $configs['forum_file_ext'] = \Forum\Models\File::DEFAULT_ALLOWED_EXTENSIONS;
        if (!array_key_exists('forum_threads_sorting', $configs)) $configs['forum_threads_sorting'] = 'id';
        if (!array_key_exists('forum_captcha', $configs)) $configs['forum_captcha'] = 1;
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}