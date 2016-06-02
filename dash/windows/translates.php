<?php
/**
 * Zira project.
 * translate.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Translates extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-text-color';
    protected static $_title = 'Custom translates';

    protected $_help_url = 'zira/help/translates';

    public $item;
    public $search;
    public $page = 0;
    public $pages = 0;
    public $order = 'asc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setBodyViewListVertical(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Create'), 'glyphicon glyphicon-file', 'desk_call(dash_translates_create, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Create'), 'glyphicon glyphicon-file', 'desk_call(dash_translates_create, this);', 'create')
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Create'), 'glyphicon glyphicon-file', 'desk_call(dash_translates_create, this);', 'create')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_translates_edit, this);'
            )
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->setData(array(
            'items'=>array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'search'=>$this->search,
            'order'=>$this->order
        ));

        $this->addStrings(array(
            'Enter string to translate',
            'Enter translate'
        ));

        $this->includeJS('dash/translates');
    }

    public function getAvailableLanguages() {
        $available_languages = array();
        $d = opendir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR);
        while (($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR . DIRECTORY_SEPARATOR . $f)) continue;
            $lang_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                            LANGUAGES_DIR . DIRECTORY_SEPARATOR .
                            $f . DIRECTORY_SEPARATOR .
                            $f . '.php';
            if (!file_exists($lang_file) || !is_readable(($lang_file))) continue;
            $strings = include($lang_file);
            if (!is_array($strings)) continue;
            $available_languages[$f]=array_key_exists($f,$strings) ? $strings[$f] : $f;
        }
        return $available_languages;
    }

    public function getActiveLanguages() {
        return Zira\Config::get('languages');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_languages = $this->getAvailableLanguages();

        if (empty($this->item) || !array_key_exists($this->item, $available_languages)) return array('error' => Zira\Locale::t('An error occurred'));

        $query = Zira\Models\Translate::getCollection()
                                    ->count()
                                    ->where('language','=',$this->item)
                                    ;
        if (!empty($this->search)) {
            $query->and_where()
                    ->open_where()
                    ->where('name','like','%'.$this->search.'%')
                    ->or_where('value','like','%'.$this->search.'%')
                    ->close_where()
                    ;
        }
        $this->total = $query->get('co');

        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;

        $this->setData(array(
            'items'=>array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'search'=>$this->search,
            'order'=>$this->order
        ));

        $query = Zira\Models\Translate::getCollection()
                                    ->where('language','=',$this->item)
                                    ->order_by(Zira\Models\Translate::getPk(), $this->order)
                                    ->limit($this->limit,($this->page - 1) * $this->limit)
                                    ;
        if (!empty($this->search)) {
            $query->and_where()
                    ->open_where()
                    ->where('name','like','%'.$this->search.'%')
                    ->or_where('value','like','%'.$this->search.'%')
                    ->close_where()
                    ;
        }

        $rows = $query->get();

        $items = array();
        foreach ($rows as $row) {
            $items[]=$this->createBodyFileItem($row->name, $row->value, $row->id, null, false, array('type'=>'txt'));
        }

        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t('Custom translates').' ['.$this->item.']');
    }
}