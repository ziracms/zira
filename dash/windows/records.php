<?php
/**
 * Zira project.
 * categories.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Records extends Window {
    const RECORDS_MENU_HOOK = 'dash_records_menu_hook';
    const RECORDS_CONTEXT_MENU_HOOK = 'dash_records_context_menu_hook';
    const RECORDS_SIDEBAR_HOOK = 'dash_records_sidebar_hook';
    const RECORDS_ON_SELECT_CALLBACK_HOOK = 'dash_records_on_select_callback_hook';

    protected static $_icon_class = 'glyphicon glyphicon-book';
    protected static $_title = 'Records';

    protected $_help_url = 'zira/help/records';

    public $search;
    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(false);
        $this->setReloadButtonEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarSeparator()
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Slider'), 'glyphicon glyphicon-film', 'desk_call(dash_records_record_slider, this);', 'edit', true, array('typo'=>'slider'))
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Gallery'), 'glyphicon glyphicon-th', 'desk_call(dash_records_record_gallery, this);', 'edit', true, array('typo'=>'gallery'))
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Audio'), 'glyphicon glyphicon-music', 'desk_call(dash_records_record_audio, this);', 'edit', true, array('typo'=>'audio'))
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Video'), 'glyphicon glyphicon-facetime-video', 'desk_call(dash_records_record_video, this);', 'edit', true, array('typo'=>'video'))
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Files'), 'glyphicon glyphicon-file', 'desk_call(dash_records_record_files, this);', 'edit', true, array('typo'=>'files'))
        );
        // extra sidebar items
        $extra_items = \Zira\Hook::run(self::RECORDS_SIDEBAR_HOOK, $this);
        if (!empty($extra_items)) {
            foreach($extra_items as $extra) {
                $this->addDefaultSidebarItem($extra);
            }
        }

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t($this->_edit_action_text), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t($this->_edit_action_text), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Up'), 'glyphicon glyphicon-level-up', 'desk_call(dash_records_up, this);', 'level', true)
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Reload'), 'glyphicon glyphicon-repeat', 'desk_window_reload(this);', 'reload')
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_records_copy, this);', 'delete', true, array('typo'=>'copy'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Move'), 'glyphicon glyphicon-share', 'desk_call(dash_records_move, this);', 'edit', true, array('typo'=>'move'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Attach picture'), 'glyphicon glyphicon-picture', 'desk_call(dash_records_record_image, this);', 'edit', true, array('typo'=>'editor'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Publish'), 'glyphicon glyphicon-ok', 'desk_call(dash_records_record_publish, this);', 'edit', true, array('typo'=>'publish'))
        );
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_records_record_view, this);', 'edit', true, array('typo'=>'preview'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_records_record_page, this);', 'edit', true, array('typo'=>'record'))
        );

        // extra context menu items
        $extra_items = \Zira\Hook::run(self::RECORDS_CONTEXT_MENU_HOOK, $this);
        if (!empty($extra_items)) {
            $this->addDefaultContextMenuItem(
                $this->createContextMenuSeparator()
            );
            foreach($extra_items as $extra) {
                $this->addDefaultContextMenuItem($extra);
            }
        }

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_edit, this);'
            )
        );

        $this->setOnDeleteItemsJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_delete, this);'
            )
        );
        
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_open, this);'
            )
        );

        $this->addDefaultOnLoadScript(
            'desk_call(dash_records_load, this);'
        );

        $onSelectCallback = 'desk_call(dash_records_select, this);';
        // extra onSelect callbacks
        $extra_items = \Zira\Hook::run(self::RECORDS_ON_SELECT_CALLBACK_HOOK);
        if (!empty($extra_items)) {
            foreach($extra_items as $extra) {
                $onSelectCallback .= $extra;
            }
        }
        $this->setOnSelectJSCallback(
            $this->createJSCallback($onSelectCallback)
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_drop, this, element);'
            )
        );

        $this->setSidebarContent('<div class="record-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setData(array(
            'page'=>1,
            'pages'=>1,
            'order'=>$this->order,
            'root' => '',
            'language' => '',
            'slider_enabled'=>0,
            'gallery_enabled'=>0,
            'files_enabled'=>0,
            'audio_enabled'=>0,
            'video_enabled'=>0
        ));

        $this->addStrings(array(
            'Information',
            'Enter category',
            'Enter description'
        ));

        $this->addVariables(array(
            'record_status_published_id' => Zira\Models\Record::STATUS_PUBLISHED,
            'record_status_not_published_id' => Zira\Models\Record::STATUS_NOT_PUBLISHED,
            'record_status_front_page_id' => Zira\Models\Record::STATUS_FRONT_PAGE,
            'record_status_not_front_page_id' => Zira\Models\Record::STATUS_NOT_FRONT_PAGE,
            'dash_records_wnd' => Dash::getInstance()->getWindowJSName(Records::getClass()),
            'dash_records_category_wnd' => Dash::getInstance()->getWindowJSName(Category::getClass()),
            'dash_records_record_wnd' => Dash::getInstance()->getWindowJSName(Record::getClass()),
            'dash_records_category_settings_wnd' => Dash::getInstance()->getWindowJSName(Categorysettings::getClass()),
            'dash_records_record_text_wnd' => Dash::getInstance()->getWindowJSName(Recordtext::getClass()),
            'dash_records_record_html_wnd' => Dash::getInstance()->getWindowJSName(Recordhtml::getClass()),
            'dash_records_category_meta_wnd' => Dash::getInstance()->getWindowJSName(Categorymeta::getClass()),
            'dash_records_record_meta_wnd' => Dash::getInstance()->getWindowJSName(Recordmeta::getClass()),
            'dash_records_web_wnd' => Dash::getInstance()->getWindowJSName(Web::getClass()),
            'dash_records_record_images_wnd' => Dash::getInstance()->getWindowJSName(Recordimages::getClass()),
            'dash_records_record_slides_wnd' => Dash::getInstance()->getWindowJSName(Recordslides::getClass()),
            'dash_records_record_files_wnd' => Dash::getInstance()->getWindowJSName(Recordfiles::getClass()),
            'dash_records_record_audio_wnd' => Dash::getInstance()->getWindowJSName(Recordaudio::getClass()),
            'dash_records_record_video_wnd' => Dash::getInstance()->getWindowJSName(Recordvideos::getClass())
        ));

        $this->includeJS('dash/records');
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_RECORDS)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'limit'=>$this->limit,
                'order'=>$this->order,
                'root'=>'',
                'language'=>'',
                'slider_enabled'=>0,
                'gallery_enabled'=>0,
                'files_enabled'=>0,
                'audio_enabled'=>0,
                'video_enabled'=>0
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = (string)Zira\Request::post('root');
        $language= (string)Zira\Request::post('language');
        if (!empty($language) && !in_array($language, Zira\Config::get('languages'))) {
            $language = '';
        }
        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }

        // getting category id and titles chain
        $category_id = Zira\Category::ROOT_CATEGORY_ID;

        $slider_enabled = Zira\Config::get('slider_enabled', 1);
        $gallery_enabled = Zira\Config::get('gallery_enabled', 1);
        $files_enabled = Zira\Config::get('files_enabled', 1);
        $audio_enabled = Zira\Config::get('audio_enabled', 1);
        $video_enabled = Zira\Config::get('video_enabled', 1);

        $categories = array();
        if (!empty($root)) {
            $_root = trim($root, '/');
            $p = strpos($_root, '/');
            if ($p!==false) $_root = substr($_root, 0, $p);
            $rows = Zira\Models\Category::getCollection()
                ->where('name', '=', $_root)
                ->or_where('name', 'like', $_root . '/%')
                ->order_by('name', 'asc')
                ->get();
            foreach ($rows as $row) {
                $categories[$row->name] = $row->title;
                if (!empty($root) && $row->name == trim($root, '/')) {
                    $category_id = $row->id;
                    if ($row->slider_enabled !== null) $slider_enabled = $row->slider_enabled;
                    if ($row->gallery_enabled !== null) $gallery_enabled = $row->gallery_enabled;
                    if ($row->files_enabled !== null) $files_enabled = $row->files_enabled;
                    if ($row->audio_enabled !== null) $audio_enabled = $row->audio_enabled;
                    if ($row->video_enabled !== null) $video_enabled = $row->video_enabled;
                }
            }
        }

        // categories count
        $query = Zira\Models\Category::getCollection();
        $query->count();
        $query->where('parent_id','=',$category_id);
        if (!empty($this->search)) {
            $query->and_where();
            $query->open_where();
            $query->where('name','like','%'.$this->search.'%');
            $query->or_where('title','like','%'.$this->search.'%');
            $query->close_where();
        }
        $categories_total = $query->get('co');
        $category_page = $this->page;
        $category_pages = ceil($categories_total / $this->limit);
        if ($category_page > $category_pages) $category_page = $category_pages;
        if ($category_page < 1) $category_page = 1;

        // records count
        $query = Zira\Models\Record::getCollection();
        $query->count();
        $query->where('category_id', '=', $category_id);
        if (!empty($language)) {
            $query->and_where('language', '=', $language);
        }
        if (!empty($this->search)) {
            $query->and_where();
            $query->open_where();
            $query->where('name','like','%'.$this->search.'%');
            $query->or_where('title','like','%'.$this->search.'%');
            $query->close_where();
        }
        $records_total = $query->get('co');
        $records_pages = ceil($records_total / $this->limit);
        $record_page = $this->page;
        if ($record_page > $records_pages) $record_page = $records_pages;
        if ($record_page < 1) $record_page = 1;

        // max count
        $this->total = max($categories_total, $records_total);
        $this->pages = max($category_pages, $records_pages);
        $this->page = max($category_page, $record_page);

        // categories
        $items = array();
        if ($categories_total>0) {
            $query = Zira\Models\Category::getCollection();
            $query->where('parent_id','=',$category_id);
            if (!empty($this->search)) {
                $query->and_where();
                $query->open_where();
                $query->where('name','like','%'.$this->search.'%');
                $query->or_where('title','like','%'.$this->search.'%');
                $query->close_where();
            }
            $query->order_by('id', $this->order);
            $query->limit($this->limit, ($this->page - 1) * $this->limit);
            $rows = $query->get();
        } else {
            $rows = array();
        }

        foreach ($rows as $row) {
            $name = $row->name;
            $_root = trim($root, '/');
            if (!empty($_root)) {
                $name = substr($row->name, strlen($_root) + 1);
            }
            $items[] = $this->createBodyFolderItem($name, Zira\Locale::t($row->title), $row->id, 'desk_call(dash_records_open_category, this);', false, array('parent'=>'category', 'typo' => 'category', 'category' => $name, 'description'=>$row->description));
        }

        // records
        if ($records_total>0) {
            $query = Zira\Models\Record::getCollection();
            $query->where('category_id', '=', $category_id);
            if (!empty($language)) {
                $query->and_where('language', '=', $language);
            }
            if (!empty($this->search)) {
                $query->and_where();
                $query->open_where();
                $query->where('name','like','%'.$this->search.'%');
                $query->or_where('title','like','%'.$this->search.'%');
                $query->close_where();
            }
            $query->order_by('id', $this->order);
            $query->limit($this->limit, ($this->page - 1) * $this->limit);
            $rows = $query->get();
        } else {
            $rows = array();
        }

        foreach($rows as $row) {
            $mtime = date(Zira\Config::get('date_format'), strtotime($row->creation_date));
            if ($row->thumb) {
                $items[]=$this->createBodyItem($row->name, $row->title, Zira\Helper::baseUrl($row->thumb), $row->id, 'desk_call(dash_records_record_html, this);', false, array('type'=>'html','parent'=>'record','typo'=>'record','activated'=>$row->published,'front_page'=>$row->front_page,'page'=>ltrim(trim($root,'/').'/'.$row->name,'/'),'description'=>$row->description,'language'=>count(Zira\Config::get('languages')) > 1 ? $row->language : null), $mtime);
            } else {
                $items[]=$this->createBodyFileItem($row->name, $row->title, $row->id, 'desk_call(dash_records_record_html, this);', false, array('type'=>'html','parent'=>'record','typo'=>'record','activated'=>$row->published,'front_page'=>$row->front_page,'page'=>ltrim(trim($root,'/').'/'.$row->name,'/'),'description'=>$row->description,'language'=>count(Zira\Config::get('languages')) > 1 ? $row->language : null), $mtime);
            }
        }

        $this->setBodyItems($items);

        // window title
        $title_suffix = '';
        if ($this->pages>1) $title_suffix = ' ('.intval($this->page).'/'.intval($this->pages).')';
        if (empty($root)) {
            $this->setTitle(Zira\Locale::t(self::$_title).$title_suffix);
        } else {
            $cats = explode('/',trim($root,'/'));
            $_cat = '';
            $_cats = array();
            foreach($cats as $cat) {
                if (!empty($_cat)) $_cat .= '/';
                $_cat .= $cat;
                if (!array_key_exists($_cat, $categories)) continue;
                $_cats []= Zira\Locale::t($categories[$_cat]);
            }
            $this->setTitle(Zira\Locale::t(self::$_title).': '.implode(' / ',$_cats).$title_suffix);
        }

        // menu
        $categoryMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create'),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description')),
            $this->createMenuDropdownItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Records settings'), 'glyphicon glyphicon-option-vertical', 'desk_call(dash_records_category_settings, this);', 'edit', true, array('typo'=>'settings')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_records_category_widget, this);', 'edit', true, array('typo'=>'widget'))
        );

        $recordMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create'),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Open editor'), 'glyphicon glyphicon-text-size', 'desk_call(dash_records_record_html, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_records_record_text, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description')),
            $this->createMenuDropdownItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo')),
            $this->createMenuDropdownItem(Zira\Locale::t('Attach picture'), 'glyphicon glyphicon-picture', 'desk_call(dash_records_record_image, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownItem(Zira\Locale::t('Update picture'), 'glyphicon glyphicon-refresh', 'desk_call(dash_records_record_image_update, this);', 'delete', true, array('typo'=>'rethumb')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_records_record_view, this);', 'edit', true, array('typo'=>'preview')),
            $this->createMenuDropdownItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_records_record_page, this);', 'edit', true, array('typo'=>'record')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Publish'), 'glyphicon glyphicon-ok', 'desk_call(dash_records_record_publish, this);', 'edit', true, array('typo'=>'publish')),
            $this->createMenuDropdownItem(Zira\Locale::t('Show on front page'), 'glyphicon glyphicon-home', 'desk_call(dash_records_record_front, this);', 'edit', true, array('typo'=>'front_page')),
            //$this->createMenuDropdownSeparator(),
            //$this->createMenuDropdownItem(Zira\Locale::t('Slider'), 'glyphicon glyphicon-film', 'desk_call(dash_records_record_slider, this);', 'edit', true, array('typo'=>'slider')),
            //$this->createMenuDropdownItem(Zira\Locale::t('Gallery'), 'glyphicon glyphicon-th', 'desk_call(dash_records_record_gallery, this);', 'edit', true, array('typo'=>'gallery'))
        );

        // extra menu items
        $extra_items = \Zira\Hook::run(self::RECORDS_MENU_HOOK, $this);
        if (!empty($extra_items)) {
            $recordMenu [] = $this->createMenuDropdownSeparator();
            foreach($extra_items as $extra) {
                $recordMenu = array_merge($recordMenu, $extra);
            }
        }

        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Category'), $categoryMenu),
            $this->createMenuItem(Zira\Locale::t('Record'), $recordMenu)
        );

        if (count(Zira\Config::get('languages'))>1) {
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_records_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
        }

        $this->setMenuItems($menu);

        $this->setData(array(
            'search'=>$this->search,
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order,
            'root' => $root,
            'language' => $language,
            'slider_enabled'=>$slider_enabled,
            'gallery_enabled'=>$gallery_enabled,
            'files_enabled'=>$files_enabled,
            'audio_enabled'=>$audio_enabled,
            'video_enabled'=>$video_enabled
        ));
    }
}