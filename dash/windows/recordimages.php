<?php
/**
 * Zira project.
 * recordimages.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Recordimages extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-th';
    protected static $_title = 'Gallery';

    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;
    
    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setViewSwitcherEnabled(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordimages_add, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Update thumbnails'), 'glyphicon glyphicon-refresh', 'desk_call(dash_recordimages_update, this);', 'delete', true, array('typo'=>'rethumb'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordimages_add, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Image'), Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordimages_add, this);', 'create')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordimages_desc, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordimages_drop, this, element);'
            )
        );
        
        $this->addDefaultOnLoadScript(
            'desk_call(dash_recordimages_load, this);'
        );

        $this->addStrings(array(
            'Enter description'
        ));

        $this->includeJS('dash/recordimages');
        
        $this->setData(array(
            'page'=>1,
            'pages'=>1,
            'order'=>$this->order
        ));
    }

    public function load() {
        if (empty($this->item) || !is_numeric($this->item)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'limit'=>$this->limit,
                'order'=>$this->order
            ));
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . $record->title);
        
        $this->total = Zira\Models\Image::getCollection()
                            ->count()
                            ->where('record_id','=',$record->id)
                            ->get('co');

        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;

        $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', $this->order)
                            ->limit($this->limit, ($this->page - 1) * $this->limit)
                            ->get();

        $items = array();
        foreach($images as $image) {
            $name = Zira\Helper::basename($image->image);
            $inactive = file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image->image)) ? 0 : 1;
            $items []= $this->createBodyItem(Zira\Helper::html($name), Zira\Helper::html($image->description), Zira\Helper::baseUrl($image->thumb), $image->id, null, false, array('description'=>$image->description,'inactive'=>$inactive));
        }

        $this->setBodyItems($items);

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order
        ));
    }
}