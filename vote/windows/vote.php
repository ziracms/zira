<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Vote extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-list-alt';
    protected static $_title = 'Vote subject';

    protected $_help_url = 'zira/help/vote';

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
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Vote\Forms\Vote();
        if (!empty($this->item)) {
            $vote = new \Vote\Models\Vote($this->item);
            if (!$vote->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($vote->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'vote').' - '.$vote->subject);
        } else {
            $form->setValues(array(
                'placeholder' => Zira\View::VAR_CONTENT_BOTTOM
            ));
            $this->setTitle(Zira\Locale::tm('New vote subject','vote'));
        }

        $this->setBodyContent($form);
    }
}