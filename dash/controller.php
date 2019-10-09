<?php
/**
 * Zira project.
 * controller.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash;

use Zira;

abstract class Controller extends Zira\Controller {
    public function _before() {
        if (!Zira\User::isAuthorized()) {
            if (!Zira\View::isAjax()) {
                Zira\Response::redirect('user/login?redirect=dash');
            } else {
                Dash::forbidden();
            }
            exit;
        }
        if (!Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD)) {
            if (!Zira\View::isAjax()) {
                Zira\Response::forbidden();
            } else {
                Dash::forbidden();
            }
            exit;
        }
        if (Zira\Request::isPost()) {
            $token = Zira\Request::post('token');
            $cookie = Zira\Cookie::get(Dash::COOKIE_NAME);
            if (!Dash::checkToken($token) || !Dash::checkCookie($cookie)) {
                if (!Zira\View::isAjax()) {
                    Zira\Response::forbidden();
                } else {
                    Dash::forbidden();
                }
                exit;
            }
        }
        parent::_before();
        if (
            !Zira\View::isViewExists('dash/layout') ||
            !Zira\View::isViewExists('dash/page') ||
            !Zira\View::isViewExists('dash/panel')
        ) {
            Zira\View::setTheme(DEFAULT_THEME);
        }
        //Zira\View::setRenderWidgets(false);
        Zira\Page::setLayout('dash/layout');
        Zira\Page::setView('dash/page');
    }
}