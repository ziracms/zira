<?php
/**
 * Zira project.
 * recordaudio.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;
use Dash\Windows\Files;

class Recordaudio extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-music';
    protected static $_title = 'Audio';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setViewSwitcherEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add audio'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordaudio_add, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Add URL'), 'glyphicon glyphicon-link', 'desk_call(dash_recordaudio_addurl, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Embed code'), 'glyphicon glyphicon-paperclip', 'desk_call(dash_recordaudio_embed, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_recordaudio_edit, this);', 'edit', true, array('typo'=>'edit'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add audio'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordaudio_add, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Add URL'), 'glyphicon glyphicon-link', 'desk_call(dash_recordaudio_addurl, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Embed code'), 'glyphicon glyphicon-paperclip', 'desk_call(dash_recordaudio_embed, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_recordaudio_edit, this);', 'edit', true, array('typo'=>'edit'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Audio'), Zira\Locale::t('Add audio'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_recordaudio_add, this);', 'create')
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('URL'), Zira\Locale::t('Add URL'), 'glyphicon glyphicon-link', 'desk_call(dash_recordaudio_addurl, this);', 'create')
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Code'), Zira\Locale::t('Embed code'), 'glyphicon glyphicon-paperclip', 'desk_call(dash_recordaudio_embed, this);', 'create')
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordaudio_desc, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordaudio_drop, this, element);'
            )
        );
        
        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_recordaudio_select, this);'
            )
        );
        
        $this->addDefaultOnLoadScript(
            'desk_call(dash_recordaudio_load, this);'
        );

        $this->addStrings(array(
            'Enter description',
            'Enter URL',
            'Enter code'
        ));

        $this->includeJS('dash/recordaudio');
    }

    public function load() {
        if (empty($this->item) || !is_numeric($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || 
            !Permission::check(Permission::TO_EDIT_RECORDS)
        ) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . $record->title);

        $files = Zira\Models\Audio::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        $items = array();
        foreach($files as $file) {
            if (!empty($file->path)) {
                $real_path = str_replace('/', DIRECTORY_SEPARATOR, $file->path);
                $name = Zira\Helper::basename($file->path);
                $typo = 'file';
                $edit_value = '';
            } else if (!empty($file->url)) {
                $name = $file->url;
                $real_path = null;
                $typo = 'url';
                $edit_value = $file->url;
            } else if (!empty($file->embed)) {
                $name = Zira\Locale::t('Embedded code').' ('.Zira\Locale::t('ID: %s', $file->id).')';
                $real_path = null;
                $typo = 'embed';
                $edit_value = $file->embed;
            } else {
                $name = '';
                $real_path = null;
                $typo = '';
                $edit_value = '';
            }
            $inactive = isset($real_path) && !file_exists($real_path) ? 1 : 0;
            if (Files::is_audio($name)) {
                $items[]=$this->createBodyAudioItem(Zira\Helper::html($name), Zira\Helper::html($file->description), $file->id, null, false, array('type'=>'audio', 'description'=>$file->description, 'typo'=>$typo, 'editval'=>$edit_value, 'inactive'=>$inactive));
            } else {
                $items[]=$this->createBodyFileItem(Zira\Helper::html($name), Zira\Helper::html($file->description), $file->id, null, false, array('type'=>'file', 'description'=>$file->description, 'typo'=>$typo, 'editval'=>$edit_value, 'inactive'=>$inactive));
            }
        }

        $this->setBodyItems($items);

        $this->setData(array(
            'items' => array($this->item)
        ));
    }
}