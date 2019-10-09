<?php
/**
 * Zira project.
 * widgets.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Widgets extends Model {
    public function deactivate($widgets) {
        if (empty($widgets) || !is_array($widgets)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

        $co=0;
        foreach($widgets as $id) {
            if (!is_numeric($id)) continue;
            $widget = new Zira\Models\Widget($id);
            if (!$widget->loaded()) continue;
            if (!array_key_exists($widget->name, $available_widgets)) continue;
            if (!$available_widgets[$widget->name]->isEditable()) continue;
            if ($widget->active == Zira\Models\Widget::STATUS_NOT_ACTIVE) continue;

            $widget->active = Zira\Models\Widget::STATUS_NOT_ACTIVE;
            $widget->save();

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Deactivated %s widgets', $co), 'reload'=>$this->getJSClassName());
    }

    public function activate($widgets) {
        if (empty($widgets) || !is_array($widgets)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $co=0;
        foreach($widgets as $id) {
            if (is_numeric($id)) {
                $widget = new Zira\Models\Widget($id);
                if (!$widget->loaded()) continue;
                if (!array_key_exists($widget->name, $available_widgets)) continue;
                if (!$available_widgets[$widget->name]->isEditable()) continue;
                if ($widget->active == Zira\Models\Widget::STATUS_ACTIVE) continue;

                $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
                $widget->save();
            } else {
                if (!array_key_exists($id, $available_widgets)) continue;
                if (!$available_widgets[$id]->isEditable()) continue;

                $widget = new Zira\Models\Widget();
                $widget->name = $id;
                $widget->module = strtolower(substr($id, 1, strpos($id, '\\', 1)-1));
                $widget->placeholder = $available_widgets[$id]->getPlaceholder();
                $widget->params = null;
                $widget->category_id = null;
                $widget->sort_order = ++$max_order;
                $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
                $widget->save();
            }

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', $co), 'reload'=>$this->getJSClassName());
    }

    public function sort($widgets) {
        if (empty($widgets) || !is_array($widgets) || count($widgets)!=2 || !is_numeric($widgets[0]) || !is_numeric($widgets[1])) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

        $widget1 = new Zira\Models\Widget($widgets[0]);
        $widget2 = new Zira\Models\Widget($widgets[1]);

        if (!$widget1->loaded() || !$widget2->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!array_key_exists($widget1->name, $available_widgets) || !array_key_exists($widget2->name, $available_widgets)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!$available_widgets[$widget1->name]->isEditable() || !$available_widgets[$widget2->name]->isEditable()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if ($widget1->active!=Zira\Models\Widget::STATUS_ACTIVE || $widget2->active!=Zira\Models\Widget::STATUS_ACTIVE) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $sort_order1= $widget1->sort_order;
        $sort_order2= $widget2->sort_order;

        if ($sort_order1 == $sort_order2) $sort_order2++;

        $widget1->sort_order = $sort_order2;
        $widget2->sort_order = $sort_order1;

        $widget1->save();
        $widget2->save();

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function drag($widgets, $orders) {
        if (empty($widgets) || !is_array($widgets) || count($widgets)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($widgets)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

        $_widgets = array();
        $_orders = array();
        foreach($widgets as $id) {
            $_widget = new Zira\Models\Widget($id);
            if (!$_widget->loaded() || !array_key_exists($_widget->name, $available_widgets)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            if (!$available_widgets[$_widget->name]->isEditable() || $_widget->active!=Zira\Models\Widget::STATUS_ACTIVE) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_widgets []= $_widget;
            $_orders []= $_widget->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_widgets as $index=>$widget) {
            $widget->sort_order = intval($orders[$index]);
            $widget->save();
        }

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }
    
    public function copies($ids) {
        if (empty($ids) || !is_array($ids)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $result = array();
        foreach($ids as $id) {
            $_result = $this->copy($id, true);
            if (is_array($_result)) {
                $result = $_result;
                if (array_key_exists('error', $_result)) break;
            }
        }
        return $result;
    }

    public function copy($id, $ignoreError=false) {
        if (empty($id) || !is_numeric($id)) {
            if (!$ignoreError) return array('error' => Zira\Locale::t('An error occurred'));
            else return array('reload'=>$this->getJSClassName());
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

        $widget = new Zira\Models\Widget($id);

        if (!$widget->loaded() || !array_key_exists($widget->name, $available_widgets)) {
            if (!$ignoreError) return array('error' => Zira\Locale::t('An error occurred'));
            else return array('reload'=>$this->getJSClassName());
        }
        if (!$available_widgets[$widget->name]->isEditable()) {
            if (!$ignoreError) return array('error' => Zira\Locale::t('An error occurred'));
            else return array('reload'=>$this->getJSClassName());
        }

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $copy = new Zira\Models\Widget();
        $copy->name = $widget->name;
        $copy->module = $widget->module;
        $copy->placeholder = $widget->placeholder;
        $copy->params = $widget->params;
        $copy->category_id = $widget->category_id;
        $copy->record_id = $widget->record_id;
        $copy->sort_order = ++$max_order;
        $copy->active = Zira\Models\Widget::STATUS_NOT_ACTIVE;
        $copy->save();

        return array('reload'=>$this->getJSClassName());
    }

    public function delete($widgets) {
        if (empty($widgets) || !is_array($widgets)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $co = 0;
        foreach($widgets as $id) {
            if (!is_numeric($id)) continue;

            $widget = new Zira\Models\Widget($id);
            if (!$widget->loaded()) continue;
            $widget->delete();

            $co++;
        }

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function createBlock($path) {
        if (empty($path) || strpos($path,'..')!==false || !file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $path)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Zira\Models\Block::WIDGET_CLASS;
        $widget->module = 'zira';
        $widget->placeholder = Zira\View::VAR_BODY_BOTTOM;
        $widget->params = '[file='.$path.']';
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }
    
    public function autoCompletePage($search) {
        if (empty($search))  return array();
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $records = Zira\Models\Record::getCollection()
                        ->open_query()
                        ->select('id', 'title')
                        ->left_join(Zira\Models\Category::getClass(), array('category_title'=>'title'))
                        ->where('title', 'like', $search.'%')
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->close_query()
                        ->union()
                        ->open_query()
                        ->select('id', 'title')
                        ->left_join(Zira\Models\Category::getClass(), array('category_title'=>'title'))
                        ->where('name', 'like', $search.'%')
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->close_query()
                        ->merge()
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->get();
        
        $results = array();
        foreach($records as $record) {
            $title = $record->title;
            if (!empty($record->category_title)) $title = $record->category_title.' / '.$record->title;
            $results []= array(
                'record_id' => $record->id,
                'record_title' => $record->title,
                'title' => $title
            );
        }
        
        return $results;
    }
}