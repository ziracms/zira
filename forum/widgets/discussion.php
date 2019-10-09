<?php
/**
 * Zira project.
 * discussion.php
 * (c)2016 https://github.com/ziracms/zira
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
        $limit = Zira\Config::get('widget_records_limit', 5);

        $rows = Forum\Models\Message::getCollection()
                            ->open_query()
                            ->select(Forum\Models\Message::getFields())
                            ->join(Forum\Models\Topic::getClass(), array('topic_title'=>'title'))
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->where('language','is',null, Forum\Models\Topic::getAlias())
                            ->and_where('published','=',Forum\Models\Message::STATUS_PUBLISHED)
                            ->and_where('published','=',Forum\Models\Topic::STATUS_PUBLISHED, Forum\Models\Topic::getAlias())
                            ->group_by('topic_id')
                            ->order_by('id','desc')
                            ->limit($limit)
                            ->close_query()
                            ->union()
                            ->open_query()
                            ->select(Forum\Models\Message::getFields())
                            ->join(Forum\Models\Topic::getClass(), array('topic_title'=>'title'))
                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
                            ->where('language','=',Zira\Locale::getLanguage(), Forum\Models\Topic::getAlias())
                            ->and_where('published','=',Forum\Models\Message::STATUS_PUBLISHED)
                            ->and_where('published','=',Forum\Models\Topic::STATUS_PUBLISHED, Forum\Models\Topic::getAlias())
                            ->group_by('topic_id')
                            ->order_by('id','desc')
                            ->limit($limit)
                            ->close_query()
                            ->merge()
                            ->order_by('id','desc')
                            ->limit($limit)
                            ->get();

        if (!$rows) return;

        Zira\View::renderView(array(
            'items' => $rows
        ),'forum/widgets/discussion');
    }
}