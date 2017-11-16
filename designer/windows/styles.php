<?php
/**
 * Zira project.
 * styles.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Windows;

use Dash;
use Zira;
use Designer;
use Zira\Permission;

class Styles extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-picture';
    protected static $_title = 'Themes designer';

    //protected $_help_url = 'zira/help/styles';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(Designer\Windows\Style::getClass());
        $this->setEditActionWindowClass(Designer\Windows\Style::getClass());

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok', 'desk_call(designer_styles_activate, this);', 'edit', true, array('typo'=>'activate'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(designer_styles_copy, this);', 'edit', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Style editor'), 'glyphicon glyphicon-eye-open', 'desk_call(designer_designer_wnd, this);', 'edit', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(designer_css_wnd, this);', 'edit', true)
        );
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok', 'desk_call(designer_styles_activate, this);', 'edit', true, array('typo'=>'activate'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(designer_styles_copy, this);', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Style editor'), 'glyphicon glyphicon-eye-open', 'desk_call(designer_designer_wnd, this);', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(designer_css_wnd, this);', 'edit', true)
        );
        
        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(designer_styles_select, this);'
            )
        );
        
        $this->addDefaultOnLoadScript('desk_call(designer_styles_load, this);');
        
        $this->addStrings(array(
            'Enter title',
            'CSS code',
            'Color',
            'Theme font'
        ));
        
        $this->addVariables(array(
            'designer_style_autocomplete_url' => Zira\Helper::url('designer/dash/autocompletepage'),
            'designer_layout_url' => Zira\Helper::url('designer/dash/layout?'.Dash\Dash::GET_FRAME_PARAM.'='.Dash\Dash::GET_FRAME_VALUE),
            'designer_editor_wnd' => Dash\Dash::getInstance()->getWindowJSName(Designer\Windows\Designer::getClass()),
            'designer_css_editor_wnd' => Dash\Dash::getInstance()->getWindowJSName(Designer\Windows\Editor::getClass())
        ));
        
        $this->includeJS('designer/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $styles = Designer\Models\Style::getCollection()
                ->where('theme', '=', Zira\View::getTheme())
                ->order_by('id')
                ->get();

        $items = array();
        foreach($styles as $style) {
            $items[]=$this->createBodyFileItem($style->title, $style->url, $style->id, 'desk_call(designer_designer_wnd, this);', false, array('type'=>'html', 'inactive'=>$style->active ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}