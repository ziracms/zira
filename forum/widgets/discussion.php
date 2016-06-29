<?php
/**
 * Zira project.
 * discussion.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Widgets;

use Zira;
use Forum;
use Zira\View;
use Zira\Widget;

class Discussion extends Widget {
    protected $_title = 'Discussed on forum';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(true);
        $this->setPlaceholder(View::VAR_SIDEBAR_RIGHT);
    }

    protected function _render() {
        $limit = Zira\Config::get('records_limit', 10);

        $rows = Forum\Models\Message::getCollection()
                            ->select(Forum\Models\Message::getFields())
                            ->join(Forum\Models\Topic::getClass(), array('topic_id'=>'id','topic_title'=>'title'))
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->order_by('id','desc')
                            ->limit($limit)
                            ->get();

        Zira\View::renderView(array(
            'items' => $rows
        ),'forum/widgets/discussion');
    }
}