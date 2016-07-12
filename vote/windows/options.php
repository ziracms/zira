<?php
/**
 * Zira project.
 * options.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Options extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-th-list';
    protected static $_title = 'Vote options';

    protected $_help_url = 'zira/help/vote-options';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t($this->_create_action_text), 'glyphicon glyphicon-file', 'desk_call(dash_votes_add_option, this);', 'create', false)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t($this->_create_action_text), 'glyphicon glyphicon-file', 'desk_call(dash_votes_add_option, this);', 'create', false)
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback('desk_call(dash_votes_edit_option, this)')
        );
        $this->setOnDeleteItemsJSCallback(
            $this->createJSCallback('desk_call(dash_votes_delete_options, this)')
        );
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('Add vote option', 'vote'), Zira\Locale::tm('Add option', 'vote'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_votes_add_option, this)', 'create')
        );
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_vote_options_drag, this);'
            )
        );

        $this->addStrings(array(
            'Enter text'
        ));

        $this->addVariables(array(
            'dash_votes_blank_src' => Zira\Helper::imgUrl('blank.png')
        ));

        $this->setData(array(
            'items' => array($this->item)
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        $vote = new \Vote\Models\Vote($this->item);
        if (!$vote->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $options = \Vote\Models\Voteoption::getCollection()
                                            ->where('vote_id','=',$vote->id)
                                            ->order_by('sort_order', 'asc')
                                            ->get();
        $items = array();
        foreach ($options as $option) {
            $items[]=$this->createBodyItem($option->content, $option->content, Zira\Helper::imgUrl('drag.png'), $option->id, null, false, array('sort_order'=>$option->sort_order));
        }

        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$vote->subject);
    }
}