<?php
/**
 * Zira project.
 * forum.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Forum extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Forum';

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
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $category_id = Zira\Request::post('category_id');
        if (empty($category_id)) return array('error' => Zira\Locale::t('An error occurred'));
        $category = new \Forum\Models\Category($category_id);
        if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Forum\Forms\Forum();
        if (!empty($this->item)) {
            $forum = new \Forum\Models\Forum($this->item);
            if (!$forum->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($forum->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'forum').' - '.$forum->title.' - '.$category->title);
        } else {
            $form->setValues(array(
                'category_id'=>$category_id,
                'active'=>1
            ));
            $this->setTitle(Zira\Locale::tm('New forum','forum').' - '.$category->title);
        }

        $this->setBodyContent($form);
    }
}