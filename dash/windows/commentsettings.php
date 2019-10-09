<?php
/**
 * Zira project.
 * commentsettings.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Commentsettings extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Comments settings';

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
        if (!array_key_exists('comments_max_nesting', $configs)) $configs['comments_max_nesting'] = 5;
        if (!array_key_exists('comments_limit', $configs)) $configs['comments_limit'] = 10;
        if (!array_key_exists('comment_moderate', $configs)) $configs['comment_moderate'] = 1;
        if (!array_key_exists('comment_anonymous', $configs)) $configs['comment_anonymous'] = 1;
        if (!array_key_exists('comment_notify_email', $configs)) $configs['comment_notify_email'] = '';
        if (!array_key_exists('comments_allowed', $configs)) $configs['comments_allowed'] = 1;
        if (!array_key_exists('comments_captcha', $configs)) $configs['comments_captcha'] = 1;
        if (!array_key_exists('comments_captcha_users', $configs)) $configs['comments_captcha_users'] = 1;
        if (!array_key_exists('comment_min_chars', $configs)) $configs['comment_min_chars'] = 10;

        $form = new \Dash\Forms\Commentsettings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}