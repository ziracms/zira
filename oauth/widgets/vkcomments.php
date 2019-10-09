<?php
/**
 * Zira project.
 * vkcomments.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Oauth\Widgets;

use Zira;
use Oauth;
use Zira\View;
use Zira\Widget;

class Vkcomments extends Widget {
    protected $_title = 'Vkontakte comments widget';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_CONTENT_BOTTOM);
    }

    protected function _render() {
        $record_url = Zira\Page::getRecordUrl();
        if (!$record_url || $record_url==Zira\Config::get('home_record_name')) return;

        $vk_app_id = Zira\Config::get('oauth_vk_app_id');

        if ($vk_app_id) {
            Oauth\Oauth::addVkontakteOpenApi($vk_app_id);
            Zira\View::renderView(array(),'oauth/vk-comments');
        }
    }
}