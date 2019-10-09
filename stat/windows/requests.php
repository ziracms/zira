<?php
/**
 * Zira project.
 * requests.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Windows;

use Dash;
use Zira;
use Stat;
use Zira\Permission;

class Requests extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-signal';
    protected static $_title = 'Requests';
    
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

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addVariables(array(
            'dash_stat_limit' => $this->limit
        ));
        
        $this->setData(array(
            'page'=>1,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }
        
        $this->total = Stat\Models\Access::getCollection()->count()->get('co');
        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;
        
        $rows = Stat\Models\Access::getCollection()
                            ->order_by('id',$this->order)
                            ->limit($this->limit, ($this->page - 1) * $this->limit)
                            ->get();

        $items = array();
        foreach($rows as $row) {
            $mtime = date(Zira\Config::get('date_format'), strtotime($row->access_time));
            $items[]=$this->createBodyFileItem(Zira\Helper::html($row->url), Zira\Helper::html($row->ip."\n".$row->ua), $row->id, 'desk_call(dash_stat_access_row,this);', false, array('type'=>'txt'), $mtime);
        }
        $this->setBodyItems($items);
        
        $this->setData(array(
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));
    }
}