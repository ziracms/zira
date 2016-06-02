<?php
/**
 * Zira project.
 * fbcomments.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Widgets;

use Zira;
use Oauth;
use Zira\View;
use Zira\Widget;

class Fbcomments extends Widget {
    protected $_title = 'Facebook comments widget';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_CONTENT_BOTTOM);
    }

    protected function _render() {
        $record_url = Zira\Page::getRecordUrl();
        if (!$record_url || $record_url==Zira\Config::get('home_record_name')) return;

        $fb_app_id = Zira\Config::get('oauth_fb_app_id');

        if ($fb_app_id) {
            Oauth\Oauth::addFacebookJsSdk($fb_app_id);
            Zira\View::renderView(array(
                'page_url' => Zira\Helper::url($record_url, true, true)
            ),'oauth/fb-comments');
        }
    }
}