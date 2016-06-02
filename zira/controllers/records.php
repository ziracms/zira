<?php
/**
 * Zira project.
 * records.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Records extends Zira\Controller {
    /**
     * AJAX action
     * Loads category records
     */
    public function index() {
        if (Zira\Request::isPost()) {
            $category_id = (int)Zira\Request::post('category_id');
            $last_id = (int)Zira\Request::post('last_id');

            if (!$category_id || $last_id<=0) return;

            $category = new Zira\Models\Category($category_id);
            if (!$category->loaded()) return;

            Zira\Category::setCurrent($category);
            Zira\Category::setChilds(null);
            Zira\Content\Category::content($last_id, true);
        }
    }
}