<?php
/**
 * Zira project.
 * usermenu.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Usermenu extends Zira\Widget {
    protected $_title = 'User menu';
    protected $_authorizied_class = 'authorized';
    protected $_unauthorizied_class = 'not-authorized';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_HEADER);
    }

    protected function _render() {
        if (Zira\User::isAuthorized()) {
            $class=$this->_authorizied_class;
            $items = array(
                array(
                    'url' => 'user/profile',
                    'icon' => 'glyphicon glyphicon-log-in',
                    'title' => Zira\User::getProfileName(),
                    'dropdown' => array(
                        array(
                            'url' => 'user/profile',
                            'icon' => 'glyphicon glyphicon-user',
                            'title' => Zira\Locale::t('Profile')
                        ),
                        array(
                            'type' => 'separator'
                        ),
                        array(
                            'url' => 'user/messages',
                            'icon' => 'glyphicon glyphicon-envelope',
                            'title' => Zira\Locale::t('Messages') . (Zira\User::getCurrent()->messages>0 ? ' ('.Zira\User::getCurrent()->messages.')' : '')
                        ),
                        array(
                            'type' => 'separator'
                        ),
                        array(
                            'url' => 'user/logout',
                            'icon' => 'glyphicon glyphicon-log-out',
                            'title' => Zira\Locale::t('Logout')
                        )
                    )
                )
            );
            if (Zira\User::getCurrent()->messages>0) {
                $items = array_merge(array(
                    array(
                    'url' => 'user/messages',
                    'icon' => 'glyphicon glyphicon-envelope',
                    'title' => Zira\User::getCurrent()->messages,
                    )
                ), $items);
            }
        } else {
            $redirect_url = Zira\Page::getRedirectUrl();
            if (!$redirect_url) $redirect_url = Zira\Page::getRecordUrl();
            if ($redirect_url && $redirect_url==Zira\Config::get('home_record_name')) $redirect_url = null;
            if (!$redirect_url && Zira\Category::current()) $redirect_url = Zira\Category::current()->name;
            $class=$this->_unauthorizied_class;
            if (Zira\Config::get('user_signup_allow')) {
                $items = array(
                    array(
                        'url' => 'user/login'.($redirect_url ? '?redirect='.$redirect_url : ''),
                        'icon' => '',
                        'title' => Zira\Locale::t('Log In'),
                        'class' => 'user-login-menu'
                    )
                );
                $items []= array(
                    'url' => 'user/signup',
                    'icon' => '',
                    'title' => Zira\Locale::t('Sign Up')
                );
            } else {
                $items = array(
                    array(
                        'url' => 'user/login'.($redirect_url ? '?redirect='.$redirect_url : ''),
                        'icon' => 'glyphicon glyphicon-log-in',
                        'title' => Zira\Locale::t('Authorization'),
                        'class' => 'user-login-menu'
                    )
                );
            }
        }

        $extra_items = Zira\Hook::run(Zira\Menu::USER_MENU_HOOK_NAME);
        if (!empty($extra_items)) {
            $items = array_merge($extra_items, $items);
        }

        Zira\View::renderView(array(
            'class' => $class,
            'items' => $items
        ),'zira/user/menu');
    }
}