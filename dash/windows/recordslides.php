<?php
/**
 * Zira project.
 * recordslides.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Recordslides extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-film';
    protected static $_title = 'Slider';

    protected $_help_url = 'zira/help/slider';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setViewSwitcherEnabled(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordslides_add, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordslides_add, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Image'), Zira\Locale::t('Add image'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordslides_add, this);', 'create')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordslides_desc, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordslides_drop, this, element);'
            )
        );
        
        $this->addDefaultOnLoadScript(
            'desk_call(dash_recordslides_load, this);'
        );

        $this->addStrings(array(
            'Enter description'
        ));

        $this->includeJS('dash/recordslides');
    }

    public function load() {
        if (empty($this->item) || !is_numeric($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . $record->title);

        $images = Zira\Models\Slide::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        $items = array();
        foreach($images as $image) {
            $name = Zira\Helper::basename($image->image);
            $inactive = file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image->image)) ? 0 : 1;
            $items []= $this->createBodyItem(Zira\Helper::html($name), Zira\Helper::html($image->description), Zira\Helper::baseUrl($image->thumb), $image->id, null, false, array('description'=>$image->description,'inactive'=>$inactive));
        }

        $this->setBodyItems($items);

        $this->setData(array(
            'items' => array($this->item)
        ));
    }
}