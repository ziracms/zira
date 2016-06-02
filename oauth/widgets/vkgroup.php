<?php
/**
 * Zira project.
 * vkgroup.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Widgets;

use Zira;
use Oauth;
use Zira\View;
use Zira\Widget;

class Vkgroup extends Widget {
    protected $_title = 'Vkontakte group widget';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $vk_app_id = Zira\Config::get('oauth_vk_app_id');
        $vk_group_id = Zira\Config::get('oauth_vk_group_id');

        if ($vk_app_id && $vk_group_id) {
            Oauth\Oauth::addVkontakteOpenApi($vk_app_id);
            Zira\View::renderView(array(
                'group_id' => $vk_group_id
            ),'oauth/vk-group');
        }
    }
}