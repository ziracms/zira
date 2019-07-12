<?php
/**
 * Zira project.
 * chats.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Models;

use Zira;
use Dash;
use Chat;
use Zira\Permission;

class Chats extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Chat\Forms\Chat();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            if ($id) {
                $chat = new Chat\Models\Chat($id);
                if (!$chat->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $chat = new Chat\Models\Chat();
            }

            $info = $form->getValue('info');
            $info = str_replace("\r",'',$info);
            $info = str_replace("\n","\r\n",$info);
            $info = Zira\Helper::utf8Entity(html_entity_decode($info));
            
            $chat->title = $form->getValue('title');
            $chat->info = $info;
            $chat->visible_group = (int)$form->getValue('visible_group');
            $refresh_delay = (int)$form->getValue('refresh_delay');
            $chat->refresh_delay = $refresh_delay ? $refresh_delay : Chat\Chat::DEFAULT_DELAY;
            $chat->check_auth = (int)$form->getValue('check_auth') ? 1 : 0;
            $chat->date_created = date('Y-m-d H:i:s');

            $chat->save();
            
            if (empty($id)) {
                $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

                $widget = new Zira\Models\Widget();
                $widget->name = Chat\Chat::WIDGET_CLASS;
                $widget->module = 'chat';
                $widget->placeholder = $form->getValue('placeholder');
                $widget->params = $chat->id;
                $widget->category_id = null;
                $widget->sort_order = ++$max_order;
                $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
                $widget->save();
            }

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::tm('Activated %s widgets', 'dash', 1), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $chat_id) {
            $chat = new Chat\Models\Chat($chat_id);
            if (!$chat->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };

            $chat->delete();
            
            Chat\Models\Message::getCollection()
                                ->delete()
                                ->where('chat_id', '=', $chat_id)
                                ->execute();
            
            Zira\Models\Widget::getCollection()
                                ->where('name','=',\Chat\Chat::WIDGET_CLASS)
                                ->and_where('params','=',$chat_id)
                                ->delete()
                                ->execute();
        }

        Zira\Cache::clear();
        
        return array('reload' => $this->getJSClassName());
    }
    
    public function install($chats) {
        if (empty($chats) || !is_array($chats)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
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

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $co=0;
        foreach($chats as $chat_id) {
            $chat = new Chat\Models\Chat(intval($chat_id));
            if (!$chat->loaded()) continue;
            if (in_array($chat->id,$widgets)) continue;
            $widget = new Zira\Models\Widget();
            $widget->name = Chat\Chat::WIDGET_CLASS;
            $widget->module = 'chat';
            $widget->placeholder = Chat\Chat::WIDGET_PLACEHOLDER;
            $widget->params = $chat->id;
            $widget->category_id = null;
            $widget->sort_order = ++$max_order;
            $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
            $widget->save();

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::tm('Activated %s widgets', 'dash', $co), 'reload'=>$this->getJSClassName());
    }
}