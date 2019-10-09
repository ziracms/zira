<?php
/**
 * Zira project.
 * editortext.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;

abstract class Editor extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-list-alt';
    protected static $_title = 'Editor';

    protected $_is_wysiwyg = false;

    abstract public function load();

    public function setWysiwyg($wysiwyg) {
        $this->_is_wysiwyg = (bool)$wysiwyg;
    }

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setToolbarEnabled(false);
        $this->setReloadButtonEnabled(false);
    }

    public function create() {
        if (!$this->_is_wysiwyg) {
            $this->createTextEditor();
        } else {
            $this->createHtmlEditor();
        }

        $editor_css = Zira\Helper::cssThemeUrl('main.css');
        $editor_css_file = 'editor.css';
        if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . Zira\View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . CSS_DIR . DIRECTORY_SEPARATOR .$editor_css_file)) {
            $editor_css = rtrim(BASE_URL,'/') . '/' . THEMES_DIR . '/' . Zira\View::getTheme() . '/' . ASSETS_DIR . '/' . CSS_DIR . '/' . $editor_css_file . '?t=' . time();
        }

        $this->addStrings(array(
            'Save',
            'File manager',
            'Add collapsible block',
            'Hidden text',
            'Collapsible block'
        ));

        $this->addVariables(array(
            'dash_editor_language' => Zira\Locale::getLanguage(),
            'dash_editor_css' => $editor_css
        ), true);

        $this->includeJS('dash/editor');
    }

    public function createTextEditor() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                $this->getTextOnLoadJs()
            )
        );
        $this->setOnFocusJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_text_focus, this);'
            )
        );
        $this->setOnUpdateContentJSCallback(
            $this->createJSCallback(
               'desk_call(dash_editor_text_update, this);'
            )
        );
        $this->setOnResizeJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_text_resize, this);'
            )
        );
    }

    public function createHtmlEditor() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                $this->getHtmlOnLoadJs()
            )
        );
        $this->setOnFocusJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_html_focus, this);'
            )
        );
        $this->setOnBlurJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_html_blur, this);'
            )
        );
        $this->setOnResizeJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_html_resize, this);'
            )
        );
        $this->setOnUpdateContentJSCallback(
            $this->createJSCallback(
               'desk_call(dash_editor_html_update, this);'
            )
        );
        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_editor_html_drop, this, element);'
            )
        );
    }

    protected function getTextOnLoadJs() {
        return 'desk_call(dash_editor_text_load, this);';
    }

    protected function getHtmlOnLoadJs() {
        return 'desk_call(dash_editor_html_load, this);';
    }

    protected function getBodyContent($content, $item_name, $item_value, $textarea_id) {
        if (!$this->_is_wysiwyg) {
            return $this->getTextBodyContent($content, $item_name, $item_value, $textarea_id);
        } else {
            return $this->getHtmlBodyContent($content, $item_name, $item_value, $textarea_id);
        }
    }

    protected function getTextBodyContent($content, $item_name, $item_value, $textarea_id) {
        return Zira\Helper::tag_open('form', array('style'=>'width:100%;height:100%;')).
                Zira\Helper::tag_open('textarea', array('id'=>$this->getTextContentId($textarea_id),'name'=>'content','style'=>'width:100%;height:100%;border:none;outline:none;resize:none;padding:10px 14px')).
                $content.
                Zira\Helper::tag_close('textarea').
                Zira\Helper::tag_short('input', array('type'=>'hidden', 'name'=>$item_name, 'value'=>$item_value)).
                Zira\Helper::tag_close('form')
                ;
    }

    protected function getHtmlBodyContent($content, $item_name, $item_value, $textarea_id) {
        return Zira\Helper::tag_open('form', array('style'=>'width:100%;height:100%;')).
            Zira\Helper::tag_open('textarea', array('id'=>$this->getHtmlContentId($textarea_id),'name'=>'content','class'=>'editable','style'=>'width:100%;height:100%;border:none;')).
            $content.
            Zira\Helper::tag_close('textarea').
            Zira\Helper::tag_short('input', array('type'=>'hidden', 'name'=>$item_name, 'value'=>$item_value)).
            Zira\Helper::tag_close('form')
            ;
    }

    protected function getTextContentId($window_id) {
        return $window_id.'-content';
    }

    protected function getHtmlContentId($window_id) {
        return $window_id.'-content';
    }
}