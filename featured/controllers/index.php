<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

namespace Featured\Controllers;

use Zira;
use Featured;

class Index extends Zira\Controller {
    public function index() {
        $rows = Featured\Models\Featured::getRecords();
        if (!$rows) return;

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        //$is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS;
        $is_grid = Zira\Config::get('site_records_grid', 1);

        Zira\Page::setTitle(Zira\Locale::tm('Featured records', 'featured'));

        Zira\Page::setContentView(array(
            'records' => $rows,
            'grid' => $is_grid
        ),'featured/page');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::tm('Featured records', 'featured'),
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }
}