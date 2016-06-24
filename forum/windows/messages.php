<?php
/**
 * Zira project.
 * messages.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Messages extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Topic messages';

    public $item;

    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_forum_message_create, this);'
            )
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_forum_message_edit, this);'
            )
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error'=>Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $topic = new \Forum\Models\Topic($this->item);
        if (!$topic->loaded()) return array('error'=>Zira\Locale::t('An error occurred'));

        $this->total = \Forum\Models\Message::getCollection()
                                    ->count()
                                    ->where('topic_id','=',$topic->id)
                                    ->get('co');

        $this->pages = ceil($this->total / $this->limit);
        if ($this->page > $this->pages) $this->page = $this->pages;
        if ($this->page < 1) $this->page = 1;

        $messages = \Forum\Models\Message::getCollection()
                                    ->select(\Forum\Models\Message::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('user_login'=>'username'))
                                    ->where('topic_id','=',$topic->id)
                                    ->order_by('id', $this->order)
                                    ->limit($this->limit, ($this->page - 1) * $this->limit)
                                    ->get();

        $items = array();
        foreach($messages as $message) {
            $content = Zira\Helper::html($message->content);
            $username = $message->user_login ? $message->user_login : Zira\Locale::tm('User deleted', 'forum');
            $items[]=$this->createBodyFileItem($content, $username, $message->id, null, false, array('type'=>'txt'));
        }
        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$topic->title);

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order
        ));
    }
}