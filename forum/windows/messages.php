<?php
/**
 * Zira project.
 * messages.php
 * (c)2016 https://github.com/ziracms/zira
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
    public $search;
    
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
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Activate', 'forum'), 'glyphicon glyphicon-ok', 'desk_call(dash_forum_message_activate, this);', 'edit', true, array('typo'=>'activate'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Activate', 'forum'), 'glyphicon glyphicon-ok', 'desk_call(dash_forum_message_activate, this);', 'edit', true, array('typo'=>'activate'))
        );
        
        $this->addVariables(array(
            'dash_forum_messages_limit' => $this->limit
        ));

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));

        $this->setOnSelectJSCallback(
            $this->createJSCallback('desk_call(dash_forum_messages_select, this);')
        );

        $this->addDefaultOnLoadScript('desk_call(dash_forum_messages_load, this);');
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error'=>Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Forum\Forum::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }
        $topic = new \Forum\Models\Topic($this->item);
        if (!$topic->loaded()) return array('error'=>Zira\Locale::t('An error occurred'));

        $total_q= \Forum\Models\Message::getCollection()
                                    ->count()
                                    ->where('topic_id','=',$topic->id)
                                    ;
        
        if (!empty($this->search)) {
            $total_q->and_where('content','like','%'.$this->search.'%');
        }
                                    
        $this->total = $total_q->get('co');

        $this->pages = ceil($this->total / $this->limit);
        if ($this->page > $this->pages) $this->page = $this->pages;
        if ($this->page < 1) $this->page = 1;

        $file_fields = \Forum\Models\File::getFields();
        $_file_fields = array();
        foreach($file_fields as $field) {
            $_file_fields['file_'.$field] = $field;
        }

        $messages_q = \Forum\Models\Message::getCollection()
                                    ->select(\Forum\Models\Message::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('user_login'=>'username'))
                                    ->left_join(\Forum\Models\File::getClass(), $_file_fields)
                                    ->where('topic_id','=',$topic->id)
                                    ;
        
        if (!empty($this->search)) {
            $messages_q->and_where('content','like','%'.$this->search.'%');
        }
        
        $messages = $messages_q->order_by('id', $this->order)
                            ->limit($this->limit, ($this->page - 1) * $this->limit)
                            ->get();

        $items = array();
        foreach($messages as $message) {
            $files = array();
            for ($i=1; $i<=\Forum\Models\File::MAX_FILES_COUNT; $i++) {
                $field = 'file_path'.$i;
                if ($message->{$field}) {
                    $filename = Zira\Helper::basename($message->{$field});
                    $files[$message->{$field}] = $filename;
                }
            }

            $files_str = '';
            if (!empty($files)) $files_str = "\r\n".Zira\Locale::t('Attached files').': '.implode(', ',$files);

            $content = Zira\Helper::html($message->content);
            $username = $message->user_login ? $message->user_login : Zira\Locale::tm('User deleted', 'forum');
            $items[]=$this->createBodyFileItem($content, Zira\Locale::t('User').': '.Zira\Helper::html($username).Zira\Helper::html($files_str), $message->id, 'desk_call(dash_forum_message_preview, this);', false, array('type'=>'txt','published'=>$message->published ? 1 : 0));
        }
        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::tm(self::$_title, 'forum').' - '.$topic->title);

        $this->setData(array(
            'search'=>$this->search,
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));
    }
}