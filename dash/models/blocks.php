<?php
/**
 * Zira project.
 * blocks.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Blocks extends Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $block_id) {
            $block = new Zira\Models\Block($block_id);
            if (!$block->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $block->delete();

            Zira\Models\Widget::getCollection()
                                ->where('name','=',Zira\Models\Block::WIDGET_CLASS)
                                ->and_where('params','=',$block_id)
                                ->delete()
                                ->execute();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function install($blocks) {
        if (empty($blocks) || !is_array($blocks)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
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

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $co=0;
        foreach($blocks as $block_id) {
            $block = new Zira\Models\Block(intval($block_id));
            if (!$block->loaded()) continue;
            if (in_array($block->id,$widgets)) continue;
            $widget = new Zira\Models\Widget();
            $widget->name = Zira\Models\Block::WIDGET_CLASS;
            $widget->module = 'zira';
            $widget->placeholder = $block->placeholder;
            $widget->params = $block->id;
            $widget->category_id = null;
            $widget->sort_order = ++$max_order;
            $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
            $widget->save();

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', $co), 'reload'=>$this->getJSClassName());
    }
}