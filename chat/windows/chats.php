<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Windows;

use Dash;
use Zira;
use Chat;
use Zira\Permission;

class Chats extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-transfer';
    protected static $_title = 'Chats';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionText(Zira\Locale::tm('New chat', 'chat'));

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback('desk_call(dash_chat_chat_create, this);')
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback('desk_call(dash_chat_chat_edit, this);')
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Messages'), 'glyphicon glyphicon-comment', 'desk_call(dash_chat_messages, this);', 'edit', true, array('typo'=>'messages'))
        );

        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Chat settings', 'chat'), 'glyphicon glyphicon-cog', 'desk_call(dash_chat_settings, this);', 'settings', false, true)
        );
        
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Chat messages', 'chat'), 'glyphicon glyphicon-comment', 'desk_call(dash_chat_messages, this);', 'edit', true, array('typo'=>'messages'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_chat_widget_install, this);', 'delete', true, array('typo'=>'install'))
        );
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Chat messages', 'chat'), 'glyphicon glyphicon-comment', 'desk_call(dash_chat_messages, this);', 'edit', true, array('typo'=>'messages'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_chat_widget_install, this);', 'delete', true, array('typo'=>'install'))
        );
        
        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_chat_chat_select, this);'
            )
        );
        
        $this->addDefaultOnLoadScript('desk_call(dash_chat_chat_load, this);');
        
        $this->addVariables(array(
            'dash_chat_chat_wnd' => Dash\Dash::getInstance()->getWindowJSName(Chat\Windows\Chat::getClass()),
            'dash_chat_messages_wnd' => Dash\Dash::getInstance()->getWindowJSName(Chat\Windows\Messages::getClass()),
            'dash_chat_message_wnd' => Dash\Dash::getInstance()->getWindowJSName(Chat\Windows\Message::getClass()),
            'dash_chat_settings_wnd' => Dash\Dash::getInstance()->getWindowJSName(Chat\Windows\Settings::getClass())
        ));
        
        $this->includeJS('chat/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(Chat\Chat::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $widgets = array();
        $rows = Zira\Models\Widget::getCollection()
                                ->where('name','=',Chat\Chat::WIDGET_CLASS)
                                ->get()
                                ;
        foreach($rows as $row) {
            if (!is_numeric($row->params)) continue;
            $widgets[] = $row->params;
        }
        
        $chats = Chat\Models\Chat::getCollection()
                                    ->order_by('id', 'asc')
                                    ->get();

        $items = array();
        foreach($chats as $chat) {
            $title = $chat->title;
            $items[]=$this->createBodyFileItem($title, $title, $chat->id, 'desk_call(dash_chat_messages, this);', false, array('type'=>'txt','inactive'=>in_array($chat->id,$widgets) ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}