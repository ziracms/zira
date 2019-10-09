<?php
/**
 * Zira project.
 * homecategories.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Homecategories extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-sort';
    protected static $_title = 'Category sorting';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_home_categories_drag, this);'
            )
        );

        $this->addVariables(array(
            'dash_home_categories_blank_src' => Zira\Helper::imgUrl('blank.png')
        ));

        $this->includeJS('dash/homecategories');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }


        $categories = Zira\Models\Category::getHomeCategories();

        $items = array();
        for ($i=0; $i<count($categories); $i++) {
            $category = $categories[$i];
            $items[]=$this->createBodyItem($category->title, $category->name, Zira\Helper::imgUrl('drag.png'), $category->id, null, false, array('sort_order'=>$i));
        }

        $this->setBodyItems($items);
    }
}