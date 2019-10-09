<?php
/**
 * Zira project.
 * online.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Online extends Zira\Widget {
    protected $_title = 'Users online';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $count = Zira\User::getOnlineUsersCount();
        if (!$count) return;
        $users = Zira\User::getOnlineUsers(25);
        $data = array(
            'title' => Zira\Locale::t('Who\'s online').' ('.$count.')',
            'count' => $count,
            'users' => $users
        );

        Zira\View::renderView($data, 'zira/widgets/online');
    }
}