<?php
/**
 * Zira project.
 * dash.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Designer\Controllers;

use Zira;
use Designer;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }
    
    public function layout() {
        if (!\Dash\Dash::isFrame()) Zira\Response::forbidden();
        if (!Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        
        $id = (int)Zira\Request::get('id');
        if (!$id) Zira\Response::notFound();
        
        $style = new Designer\Models\Style($id);
        if (!$style->loaded()) Zira\Response::notFound();
        
        Zira\View::setAjax(false);
        Zira\View::setRenderDbWidgets(false);
        Zira\Assets::setActive(false);
        if (!$style->main) {
            $link = Zira\Helper::tag_short('link', array(
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => Zira\Helper::url('style')
            ));
            Zira\View::addHtml($link, Zira\View::VAR_HEAD_BOTTOM);
        }
        
        $block = new Zira\Widgets\Block();
        $block->setCaching(false);
        $block->setPlaceholder(Zira\View::VAR_HEADER);
        $block->setOrder(-1);
        $block->setTitle(Zira\Locale::t('Themes designer'));
        $block->setData('<div id="header-text-example" style="width:400px;float:left;text-align:right;margin:4px">Lorem ipsum dolor sit amet</div>');
        Zira\View::addWidget($block);
        
        $layout_data = &Zira\View::getLayoutDataArray();
        unset($layout_data[Zira\View::VAR_STYLES]);
        unset($layout_data[Zira\View::VAR_SCRIPTS]);
        $body_bottom_scrips = &Zira\View::getBodyBottomScriptsArray();
        $body_bottom_scrips = array();

        Zira\View::addStyle('bootstrap.min.css');
        Zira\View::addStyle('bootstrap-theme.min.css');
        Zira\View::addScript('jquery.min.js');
        Zira\View::addScript('bootstrap.min.js');
        Zira\View::addCoreStyles();
        Zira\View::addCoreScripts();
        Zira\View::addStyle('designer/editor.css?t='.time());
        Zira\View::addScript('designer/editor.js?t='.time());
        /*
        Zira\View::addSlider('slider', array(
            'auto' => true,
            'speed' => 500,
            'pause' => 8000,
            'captions' => true,
            'slideMargin' => 0,
            'adaptiveHeight' => false
        ));
         */
        Zira\View::addColorpickerAssets();
        
        $css = Zira\Helper::tag_open('style');
        $css .= $style->content;
        $css .= Zira\Helper::tag_close('style');
        Zira\View::addHTML($css, Zira\View::VAR_HEAD_BOTTOM);
        
        // extracting current theme meta data
        $metadata = array();
        $metapath = ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . Zira\View::getTheme() . DIRECTORY_SEPARATOR . 'theme.meta';
        if (file_exists($metapath)) {
            $meta = @parse_ini_file($metapath, true);
            if (!empty($meta) && is_array($meta) && array_key_exists('meta', $meta)) {
                $metadata = $meta['meta'];
            }
        }
        
        $script = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'designer_style_is_main = '.($style->main ? 'true' : 'false').';';
        $script .= 'designer_current_theme = \''.Zira\View::getTheme().'\';';
        $script .= 'designer_style_theme = \''.$style->theme.'\';';
        $script .= 'designer_current_theme_is_supported = '.(array_key_exists('editable', $metadata) && $metadata['editable'] ? 'true' : 'false').';';
        $script .= Zira\Helper::tag_close('script');
        Zira\View::addHTML($script, Zira\View::VAR_HEAD_BOTTOM);
        
        Zira\Router::setModule(DEFAULT_MODULE);
        Zira\Router::setController(DEFAULT_CONTROLLER);
        Zira\Router::setAction(DEFAULT_ACTION);
        
        Zira\Page::addBreadcrumb(null,Zira\Locale::t('User profile'));
        
        Zira\View::render(array(), 'designer/page', 'designer/layout');
    }
    
    public function autocompletepage() {
        if (Zira\Request::isPost()) {
            $search = Zira\Request::post('search');
            $window = new Designer\Windows\Style();
            $windowModel = new Designer\Models\Styles($window);
            $response = $windowModel->autoCompletePage($search);
            Zira\Page::render($response);
        }
    }
    
    public function copy() {
        if (Zira\Request::isPost()) {
            $title = Zira\Request::post('title');
            $item = Zira\Request::post('item');
            $window = new Designer\Windows\Styles();
            $windowModel = new Designer\Models\Styles($window);
            $response = $windowModel->copy($item, $title);
            Zira\Page::render($response);
        }
    }
    
    public function activate() {
        if (Zira\Request::isPost()) {
            $item = Zira\Request::post('item');
            $window = new Designer\Windows\Styles();
            $windowModel = new Designer\Models\Styles($window);
            $response = $windowModel->activate($item);
            Zira\Page::render($response);
        }
    }
}