<?php
/**
 * Zira project.
 * blocks.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Blocks extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-th';
    protected static $_title = 'Blocks';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(Block::getClass());
        $this->setEditActionWindowClass(Block::getClass());
        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultSidebarItem(
            $this->createSidebarSeparator()
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Open code'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_blocks_text,this);', 'edit', true)
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Open text'), 'glyphicon glyphicon-text-size', 'desk_call(dash_blocks_html,this);', 'edit', true)
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_blocks_install, this);', 'delete', true, array('typo'=>'install'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_blocks_text,this);', 'edit', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit text'), 'glyphicon glyphicon-text-size', 'desk_call(dash_blocks_html,this);', 'edit', true)
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_blocks_install, this);', 'delete', true, array('typo'=>'install'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_blocks_text,this);', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit text'), 'glyphicon glyphicon-text-size', 'desk_call(dash_blocks_html,this);', 'edit', true)
        );

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_blocks_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_blocks_load, this);');

        $this->addVariables(array(
            'dash_blocks_blocktext' => Dash::getInstance()->getWindowJSName(Blocktext::getClass()),
            'dash_blocks_blockhtml' => Dash::getInstance()->getWindowJSName(Blockhtml::getClass())
        ));

        $this->includeJS('dash/blocks');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $widgets = array();
        $rows = Zira\Models\Widget::getCollection()
                                ->where('name','=',Zira\Models\Block::WIDGET_CLASS)
                                ->get()
                                ;
        foreach($rows as $row) {
            if (!is_numeric($row->params)) continue;
            $widgets[] = $row->params;
        }
        $blocks = Zira\Models\Block::getCollection()->get();

        $items = array();
        foreach($blocks as $block) {
            $type = 'html';
            if (strpos($block->content, '<') === false ||
                strpos($block->content, '>') === false ||
                (strpos($block->content, '</') === false && strpos($block->content, '/>') === false)
            ) {
                $type = 'txt';
            }
            $items[]=$this->createBodyFileItem($block->name, $block->name, $block->id, 'desk_window_edit_item(this);', false, array('type'=>$type,'inactive'=>in_array($block->id,$widgets) ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}