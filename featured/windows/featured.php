<?php
/**
 * Zira project.
 * featured.php
 * (c)2016 http://dro1d.ru
 */

namespace Featured\Windows;

use Dash\Dash;
use Dash\Windows\Window;
use Zira;
use Zira\Permission;

class Featured extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-star';
    protected static $_title = 'Featured records';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_featured_drag, this);'
            )
        );

        $this->addDefaultOnLoadScript(
            'desk_call(dash_featured_load, this);'
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_featured_drop, this, element);'
            )
        );

        $this->setData(array(
            'language' => ''
        ));

        $this->addVariables(array(
            'dash_featured_blank_src' => Zira\Helper::imgUrl('blank.png')
        ));

        $this->includeJS('featured/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_RECORDS)) {
            $this->setData(array(
                'language' => ''
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $language= (string)Zira\Request::post('language');
        if (!empty($language) && !in_array($language, Zira\Config::get('languages'))) {
            $language = '';
        }

        $query = Zira\Models\Record::getCollection();
        $query->select(Zira\Models\Record::getFields());
        $query->join(\Featured\Models\Featured::getClass(), array('featured_id' => 'id', 'featured_sort_order' => 'sort_order'));
        if (!empty($language)) {
            $query->where('language', '=', $language);
        }
        $query->order_by('featured_sort_order', 'asc');
        $rows = $query->get();

        $items = array();
        foreach($rows as $row) {
            if ($row->thumb) {
                $items[]=$this->createBodyItem($row->name, $row->title, Zira\Helper::baseUrl($row->thumb), $row->featured_id, 'desk_call(dash_featured_preview, this)', false, array('type'=>'html','activated'=>$row->published,'description'=>$row->description,'sort_order'=>$row->featured_sort_order));
            } else {
                $items[]=$this->createBodyFileItem($row->name, $row->title, $row->featured_id, 'desk_call(dash_featured_preview, this)', false, array('type'=>'html','activated'=>$row->published,'description'=>$row->description,'sort_order'=>$row->featured_sort_order));
            }
        }

        $this->setBodyItems($items);

        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown())
        );

        if (count(Zira\Config::get('languages'))>1) {
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_featured_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
        }

        $this->setMenuItems($menu);

        $this->setData(array(
            'language' => $language
        ));
    }
}