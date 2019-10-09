<?php
/**
 * Zira project.
 * block.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Block extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\Block();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if (!empty($id)) {
                $block = new Zira\Models\Block($id);
            } else {
                $block = new Zira\Models\Block();
                $block->placeholder = $form->getValue('placeholder');
            }

            $block->name = $form->getValue('name');
            $block->content = str_replace("\r\n","\n",(string)$form->getValue('content'));
            $block->tpl = boolval($form->getValue('tpl')) ? 1 : 0;

            $block->save();

            if (empty($id)) {
                $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

                $widget = new Zira\Models\Widget();
                $widget->name = Zira\Models\Block::WIDGET_CLASS;
                $widget->module = 'zira';
                $widget->placeholder = $form->getValue('placeholder');
                $widget->params = $block->id;
                $widget->category_id = null;
                $widget->sort_order = ++$max_order;
                $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
                $widget->save();
            }

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}