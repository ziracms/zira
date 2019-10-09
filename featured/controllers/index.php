<?php
/**
 * Zira project.
 * index.php
 * (c)2016 https://github.com/ziracms/zira
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

        $grid = Zira\Config::get('site_records_grid', 1);

        Zira\Page::setTitle(Zira\Locale::tm('Featured records', 'featured'));

        Zira\Page::setContentView(array(
            'records' => $rows,
            'grid' => $grid
        ),'featured/page', 'featured');

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::tm('Featured records', 'featured'),
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }
}