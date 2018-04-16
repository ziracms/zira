<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Dash\Dash;
use Dash\Windows\Window;
use Zira;
use Zira\Permission;

class Fields extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-tags';
    protected static $_title = 'Record extra fields';

    //protected $_help_url = 'zira/help/extra-fields';
    
    public $item;
    
    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
        
        $this->setOnCreateItemJSCallback(
            $this->createJSCallback('desk_call(dash_fields_item_create, this)')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback('desk_call(dash_fields_item_edit, this)')
        );
        
        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('Add field', 'fields'), Zira\Locale::tm('Add field', 'fields'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_fields_item_create, this)', 'create')
        );
        
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_fields_fields_drag, this);'
            )
        );

        $this->addDefaultOnLoadScript(
            'desk_call(dash_fields_fields_load, this);'
        );
        
        $this->setData(array(
            'items' => array($this->item)
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error'=>Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $group = new \Fields\Models\Group($this->item);
        if (!$group->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $query = \Fields\Models\Field::getCollection();
        $query->where('field_group_id', '=', $group->id);
        $query->order_by('sort_order', 'asc');
        $rows = $query->get();

        $items = array();
        foreach($rows as $row) {
            $items[]=$this->createBodyItem(Zira\Helper::html($row->title), Zira\Helper::html($row->description), Zira\Helper::imgUrl('drag.png'), $row->id, null, false, array('activated'=>$row->active,'sort_order'=>$row->sort_order));
        }

        $this->setBodyItems($items);
        
        $this->setTitle(Zira\Locale::tm(self::$_title,'fields').' - '.$group->title);
    }
}