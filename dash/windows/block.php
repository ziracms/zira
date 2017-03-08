<?php
/**
 * Zira project.
 * block.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Block extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-th';
    protected static $_title = 'Block';

    protected $_help_url = 'zira/help/block';
    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_block_load, this);'
            )
        );
        $this->setOnUpdateContentJSCallback(
            $this->createJSCallback(
               'desk_call(dash_block_update, this);'
            )
        );
        $this->setOnResizeJSCallback(
            $this->createJSCallback(
                'desk_call(dash_block_resize, this);'
            )
        );
        
        $this->includeJS('dash/block');
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        if (!empty($this->item)) $this->setData(array('items'=>array($this->item)));

        $form = new \Dash\Forms\Block();

        if (!empty($this->item)) {
            $block = new Zira\Models\Block($this->item);
            if (!$block->loaded()) {
                return array('error'=>Zira\Locale::t('An error occurred'));
            }
            $this->setTitle(Zira\Locale::t(self::$_title).' - '.$block->name);
            $form->setValues($block->toArray());
        } else {
            $this->setTitle(Zira\Locale::t('New block'));
            $form->setValue('placeholder', Zira\View::VAR_SIDEBAR_LEFT);
        }

        $this->setBodyContent($form);
    }
}