<?php
/**
 * Zira project.
 * recordfiles.php
 * (c)2017 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Recordfiles extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-file';
    protected static $_title = 'Files';

    //protected $_help_url = 'zira/help/record-files';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setViewSwitcherEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add file'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordfiles_add, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add file'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordfiles_add, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('File'), Zira\Locale::t('Add file'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordfiles_add, this);', 'create')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordfiles_desc, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordfiles_drop, this, element);'
            )
        );

        $this->addStrings(array(
            'Enter description'
        ));

        $this->includeJS('dash/recordfiles');
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

        $files = Zira\Models\File::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        $items = array();
        foreach($files as $file) {
            $name = basename($file->path);
            if ($file->download_count>0) {
                $name .= '&nbsp;&nbsp;&nbsp;('.Zira\Locale::t('%s downloads', $file->download_count).')';
            }
            $items []= $this->createBodyFileItem($name, $file->description, $file->id, null, false, array('description'=>$file->description));
        }

        $this->setBodyItems($items);

        $this->setData(array(
            'items' => array($this->item)
        ));
    }
}