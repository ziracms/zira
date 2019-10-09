<?php
/**
 * Zira project.
 * buttons.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Oauth\Widgets;

use Zira;
use Oauth;
use Zira\View;
use Zira\Widget;

class Buttons extends Widget {
    protected $_title = 'Login with social networks';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_HEADER);
    }

    protected function _render() {
        if (Zira\User::isAuthorized()) return;

        $fb_on = false;
        $vk_on = false;

        $fb_enabled = Zira\Config::get('oauth_fb_on');
        $fb_app_id = Zira\Config::get('oauth_fb_app_id');
        $fb_app_secret = Zira\Config::get('oauth_fb_app_secret');

        if ($fb_enabled && $fb_app_id && $fb_app_secret) {
           $fb_on = true;
        }

        $vk_enabled = Zira\Config::get('oauth_vk_on');
        $vk_app_id = Zira\Config::get('oauth_vk_app_id');
        $vk_app_secret = Zira\Config::get('oauth_vk_app_secret');

        if ($vk_enabled && $vk_app_id && $vk_app_secret) {
            $vk_on = true;
        }

        if ($fb_on || $vk_on) {
            echo Zira\Helper::tag_open('div', array('class'=>'header-top-item header-top-buttons'));
            echo Zira\Helper::tag('div', Zira\Locale::tm('Login with: %s', 'oauth', ''), array('class'=>'header-top-button-text'));
        }

        if ($fb_on) {
            Oauth\Oauth::addFacebookJSView();
            echo Zira\Helper::tag_open('div', array('class'=>'header-top-button')).
                Oauth\Oauth::getFacebookLoginBtn().
                Zira\Helper::tag_close('div');
        }

        if ($vk_on) {
            Oauth\Oauth::addVkontakteJSView();
            echo Zira\Helper::tag_open('div', array('class'=>'header-top-button')).
                Oauth\Oauth::getVkontakteLoginBtn().
                Zira\Helper::tag_close('div');
        }

        if ($fb_on || $vk_on) {
            echo Zira\Helper::tag_close('div');
        }
    }
}