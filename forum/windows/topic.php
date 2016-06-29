<?php
/**
 * Zira project.
 * topic.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Topic extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Forum thread';

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
        $category_id = (int)Zira\Request::post('category_id');
        $forum_id = (int)Zira\Request::post('forum_id');
        if (!$category_id || !$forum_id) return array('error' => Zira\Locale::t('An error occurred'));
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Forum\Forum::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $forum = new \Forum\Models\Forum($forum_id);
        if (!$forum->loaded() || $forum->category_id!=$category_id) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Forum\Forms\Topic();
        if (!empty($this->item)) {
            $thread = new \Forum\Models\Topic($this->item);
            if (!$thread->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($thread->toArray());
        } else {
            $form->setValues(array(
                'category_id' => $category_id,
                'forum_id' => $forum_id,
                'active' => 1
            ));
        }

        $this->setTitle(Zira\Locale::tm(self::$_title,'forum').' - '.$forum->title);

        $this->setBodyContent($form);
    }
}