<?php
/**
 * Zira project.
 * comments.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Comments extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Comments';

    protected $_help_url = 'zira/help/comments';

    public $search;
    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 20;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok', 'desk_call(dash_comments_activate, this);', 'delete', true, array('typo'=>'activate'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_comments_edit, this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok', 'desk_call(dash_comments_activate, this);', 'delete', true, array('typo'=>'activate'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_comments_edit, this);', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_comments_view, this);', 'edit', true, array('typo'=>'preview'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_comments_open, this);', 'edit', true, array('typo'=>'record'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_comments_view, this);', 'edit', true, array('typo'=>'preview'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_comments_open, this);', 'edit', true, array('typo'=>'record'))
        );

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_comments_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_comments_load, this);');

        $this->setSidebarContent('<div class="comment-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setData(array(
            'page'=>1,
            'pages'=>1,
            'order'=>$this->order
        ));

        $this->addStrings(array(
            'Information',
            'Comment'
        ));

        $this->addVariables(array(
            'dash_comments_web' => Dash::getInstance()->getWindowJSName(Web::getClass())
        ));

        $this->includeJS('dash/comments');
    }

    public function load() {
        if (!Permission::check(Permission::TO_MODERATE_COMMENTS)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'limit'=>$this->limit,
                'order'=>$this->order,
                'search'=>''
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }
        
        if (empty($this->search)) {
            $this->total = Zira\Models\Comment::getCollection()->count()->get('co');
        } else {
            $this->total = Zira\Models\Comment::getCollection()
                                                ->count()
                                                ->where('content','like','%'.$this->search.'%')
                                                ->get('co');
        }
        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;

        $query = Zira\Models\Comment::getCollection()
                    ->select(Zira\Models\Comment::getFields())
                    ->left_join(Zira\Models\User::getClass(), array('author_username'=>'username'))
            ;

        if (!empty($this->search)) {
            $query->where('content','like','%'.$this->search.'%');
        }

        $query->order_by('id',$this->order);
        $query->limit($this->limit, ($this->page - 1) * $this->limit);
        $rows = $query->get();

        $records = array();
        foreach($rows as $row) {
            if (!in_array($row->record_id, $records)) $records[]=$row->record_id;
        }
        $urls = array();
        if (!empty($records)) {
            $_rows = Zira\Models\Record::getCollection()
                ->select(Zira\Models\Record::getFields())
                ->left_join(Zira\Models\Category::getClass(), array('category_name' => 'name'))
                ->where('id', 'in', $records)
                ->get();

            foreach($_rows as $_row) {
                $language = count(Zira\Config::get('languages'))>1 ? $_row->language . '/' : '';
                $urls[$_row->id] = $language . Zira\Page::generateRecordUrl($_row->category_name, $_row->name);
            }
        }
        $items = array();
        foreach($rows as $row) {
            $content = Zira\Helper::html($row->content);
            if ($row->author_id > 0) {
                $username = $row->author_username;
            } else {
                $username = ($row->sender_name ? $row->sender_name : Zira\Locale::t('Guest'));
            }
            $items[]=$this->createBodyFileItem($content, Zira\Helper::html($username), $row->id, 'desk_call(dash_comments_preview, this);', false, array('type'=>'txt','inactive'=>$row->published==Zira\Models\Comment::STATUS_PUBLISHED ? 0 : 1,'page'=>isset($urls[$row->record_id]) ? $urls[$row->record_id] : ''));
        }
        $this->setBodyItems($items);

        $this->setData(array(
                'page'=>$this->page,
                'pages'=>$this->pages,
                'limit'=>$this->limit,
                'order'=>$this->order,
                'search'=>$this->search
            ));
    }
}