<?php
/**
 * Zira project.
 * fbpage.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Widgets;

use Zira;
use Oauth;
use Zira\View;
use Zira\Widget;

class Fbpage extends Widget {
    protected $_title = 'Facebook page widget';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $fb_app_id = Zira\Config::get('oauth_fb_app_id');
        $fb_page_url = Zira\Config::get('oauth_fb_page_url');

        if ($fb_app_id && $fb_page_url) {
            Oauth\Oauth::addFacebookJsSdk($fb_app_id);
            Zira\View::renderView(array(
                'page_url' => $fb_page_url
            ),'oauth/fb-page');
        }
    }
}