<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Category extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-folder-close';
    protected static $_title = 'Forum category';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Forum\Forum::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Forum\Forms\Category();
        if (!empty($this->item)) {
            $category = new \Forum\Models\Category($this->item);
            if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($category->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'forum').' - '.$category->title);
        } else {
            $this->setTitle(Zira\Locale::tm('New forum category','forum'));
        }

        $this->setBodyContent($form);
    }
}