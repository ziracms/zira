<?php
/**
 * Zira project.
 * cache.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Cache extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-flash';
    protected static $_title = 'Cache';

    protected $_help_url = 'zira/help/cache';

    public $page = 0;
    public $pages = 0;
    public $order = 'asc';
    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_cache_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_cache_load, this);');

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Clear all'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_cache_clear, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Clear all'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_cache_clear, this);', 'create')
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Clear all'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_cache_clear, this);', 'create')
        );
        $this->setSidebarContent('<div class="cache-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->addStrings(array(
            'Information'
        ));
        $this->includeJS('dash/cache');
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_FILES) || !Permission::check(Permission::TO_EXECUTE_TASKS)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'order'=>$this->order
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root_dir = ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR;
        $items = scandir($root_dir, $this->order=='asc' ? SCANDIR_SORT_ASCENDING : SCANDIR_SORT_DESCENDING);
        $folders = array();
        $files = array();
        foreach ($items as $item) {
            if ($item=='.' || $item=='..') continue;
            if (is_dir($root_dir . DIRECTORY_SEPARATOR . $item)) $folders[]=$item;
            else $files[]=$item;
        }
        $files = array_merge($folders,$files);
        $this->total = count($files);
        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;
        $files = array_slice($files,$this->limit*($this->page-1), $this->limit);
        $bodyItems = array();
        foreach($files as $file) {
            if (is_dir($root_dir . DIRECTORY_SEPARATOR . $file)) {
                $bodyItems[]=$this->createBodyFolderItem($file, $file, $file, null, false, array('type'=>'folder', 'parent'=>'files'));
            } else {
                $bodyItems[]=$this->createBodyFileItem($file, $file, $file, null, false, array('type'=>'txt', 'parent'=>'files'));
            }
        }
        $this->setBodyItems($bodyItems);
        $this->setData(array(
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order
        ));
    }
}