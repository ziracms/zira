<?php
/**
 * Zira project.
 * messages.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Messages extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Chat messages';

    protected $_help_url = 'zira/help/chat-messages';

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
                'desk_call(dash_chat_message_create, this);'
            )
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_chat_message_edit, this);'
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
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Chat\Chat::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }
        $chat = new \Chat\Models\Chat($this->item);
        if (!$chat->loaded()) return array('error'=>Zira\Locale::t('An error occurred'));

        $this->total = \Chat\Models\Message::getCollection()
                                    ->count()
                                    ->where('chat_id','=',$chat->id)
                                    ->get('co');

        $this->pages = ceil($this->total / $this->limit);
        if ($this->page > $this->pages) $this->page = $this->pages;
        if ($this->page < 1) $this->page = 1;

        $messages = \Chat\Models\Message::getCollection()
                                    ->select(\Chat\Models\Message::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('user_login'=>'username'))
                                    ->where('chat_id','=',$chat->id)
                                    ->order_by('date_created', $this->order)
                                    ->limit($this->limit, ($this->page - 1) * $this->limit)
                                    ->get();

        $items = array();
        foreach($messages as $message) {
            $content = Zira\Helper::html($message->content);
            $username = $message->user_login ? $message->user_login : Zira\Locale::tm('Guest', 'chat');
            $items[]=$this->createBodyFileItem($content, Zira\Locale::t('User').': '.Zira\Helper::html($username), $message->id, 'desk_call(dash_chat_message_preview, this);', false, array('type'=>'txt'));
        }
        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$chat->title);

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));
    }
}