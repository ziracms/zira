<?php
/**
 * Zira project.
 * dash.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Featured\Models;

use Zira;
use Dash\Models\Model;
use Featured;
use Zira\Permission;

class Dash extends Model {
    public function add($id) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if ($record->loaded()) {
            $max_order = Featured\Models\Featured::getCollection()->max('sort_order')->get('mx');

            $featured = new Featured\Models\Featured();
            $featured->record_id = $record->id;
            $featured->sort_order = ++$max_order;
            $featured->date_added = date('Y-m-d H:i:s');
            $featured->save();

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::tm('Successfully added to featured records','featured'),'reload' => $this->getJSClassName());
        } else {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $featured_id) {
            $featured = new Featured\Models\Featured($featured_id);
            if ($featured->loaded()) {
                $featured->delete();
            }
        }

        return array('reload' => $this->getJSClassName());
    }

    public function drag($records, $orders) {
        if (empty($records) || !is_array($records) || count($records)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($records)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_records = array();
        $_orders = array();
        foreach($records as $id) {
            $_record = new Featured\Models\Featured($id);
            if (!$_record->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_records []= $_record;
            $_orders []= $_record->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_records as $index=>$featured) {
            $featured->sort_order = intval($orders[$index]);
            $featured->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}