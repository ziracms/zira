<?php
/**
 * Zira project.
 * votes.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Windows;

use Dash;
use Zira;
use Vote;
use Zira\Permission;

class Votes extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-stats';
    protected static $_title = 'Votes';

    protected $_help_url = 'zira/help/votes';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(Vote\Windows\Vote::getClass());
        $this->setEditActionWindowClass(Vote\Windows\Vote::getClass());

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::tm('Results','vote'), 'glyphicon glyphicon-stats', 'desk_call(dash_votes_results, this);', 'edit', true, array('typo'=>'results'))
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Vote options','vote'), 'glyphicon glyphicon-th-list', 'desk_call(dash_votes_options, this);', 'edit', true, array('typo'=>'options'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Vote results','vote'), 'glyphicon glyphicon-stats', 'desk_call(dash_votes_results, this);', 'edit', true, array('typo'=>'results'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_votes_install, this);', 'delete', true, array('typo'=>'install'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Vote options','vote'), 'glyphicon glyphicon-th-list', 'desk_call(dash_votes_options, this);', 'edit', true, array('typo'=>'options'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Vote results','vote'), 'glyphicon glyphicon-stats', 'desk_call(dash_votes_results, this);', 'edit', true, array('typo'=>'results'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_votes_install, this);', 'delete', true, array('typo'=>'install'))
        );

        $this->setSidebarContent('<div class="vote-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_votes_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_votes_load, this);');

        $this->addStrings(array(
            'Vote results'
        ));

        $this->addVariables(array(
            'dash_votes_options_wnd' => Dash\Dash::getInstance()->getWindowJSName(Options::getClass())
        ));

        $this->includeJS('vote/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $widgets = array();
        $rows = Zira\Models\Widget::getCollection()
                                ->where('name','=',Vote\Models\Vote::WIDGET_CLASS)
                                ->get()
                                ;
        foreach($rows as $row) {
            if (!is_numeric($row->params)) continue;
            $widgets[] = $row->params;
        }
        $votes = Vote\Models\Vote::getCollection()->get();

        $items = array();
        foreach($votes as $vote) {
            $items[]=$this->createBodyFileItem($vote->subject, $vote->subject, $vote->id, 'desk_call(dash_votes_options,this);', false, array('type'=>'txt','inactive'=>in_array($vote->id,$widgets) ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}