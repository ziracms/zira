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
        $users = Zira\User::getOnlineUsers(25);
        $guests_co = 0;
        if (in_array('stat', Zira\Config::get('modules')) && 
            class_exists('Stat\Models\Access', false) && 
            Zira\Config::get('stat_log_access')
        ) {
            $guests_co = \Stat\Models\Access::getCollection()
                                            ->countDistinctField('anonymous_id')
                                            ->where('access_time','>=',date('Y-m-d H:i:s', time() - Zira\User::ONLINE_INTERVAL))
                                            ->get('co');

            if ($guests_co <= $count) $guests_co = 0;
            else $guests_co -= $count;
        }
        $data = array(
            'title' => Zira\Locale::t('Who\'s online').' ('.($count + $guests_co).')',
            'count' => $count,
            'users' => $users,
            'guests_count' => $guests_co
        );

        Zira\View::renderView($data, 'zira/widgets/online');
    }
}