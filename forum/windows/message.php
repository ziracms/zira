<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Message extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Topic message';

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
        $topic_id = (int)Zira\Request::post('topic_id');
        if (!$topic_id) return array('error' => Zira\Locale::t('An error occurred'));
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $topic = new \Forum\Models\Topic($topic_id);
        if (!$topic->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Forum\Forms\Message();
        if ($this->item) {
            $message = new \Forum\Models\Message($this->item);
            if (!$message->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($message->toArray());
        } else {
            $form->setValues(array('topic_id'=>$topic_id));
        }

        $this->setTitle(Zira\Locale::t(self::$_title) . ' - '. $topic->title);

        $this->setBodyContent($form);
    }
}