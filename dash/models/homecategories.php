<?php
/**
 * Zira project.
 * homecategories.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Homecategories extends Model {
    public function drag($items, $orders) {
        if (empty($items) || !is_array($items) || count($items)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($items)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $home_categories = Zira\Models\Category::getHomeCategories();
        $home_categories_map = array();
        foreach($home_categories as $top_category) {
            $home_categories_map[$top_category->id] = $top_category;
        }

        foreach($items as $index=>$id) {
            $_category = new Zira\Models\Category($id);
            if (!$_category->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            if (!array_key_exists($id, $home_categories_map)) continue;
            $home_categories_map[$id]->sort_order = intval($orders[$index]);
        }

        usort($home_categories, array(Zira\Models\Category::getClass(), 'sortHomeCategories'));

        $option = 'home_categories';
        $value = '';

        for($i=0; $i<count($home_categories); $i++) {
            if (strlen($value)>0) $value .= ',';
            $value .= $home_categories[$i]->id;
        }

        if (strlen($value)>255) return array('error' => Zira\Locale::t('An error occurred'));

        $config_ids = array();
        $user_configs = Zira\Models\Option::getCollection()->get();
        foreach($user_configs as $user_config) {
            $config_ids[$user_config->name] = $user_config->id;
        }

        if (!array_key_exists($option, $config_ids)) {
            $optionObj = new Zira\Models\Option();
        } else {
            $optionObj = new Zira\Models\Option($config_ids[$option]);
        }
        $optionObj->name = $option;
        $optionObj->value = $value;
        $optionObj->module = 'zira';
        $optionObj->save();

        Zira\Models\Option::raiseVersion();

        return array('reload'=>$this->getJSClassName());
    }
}