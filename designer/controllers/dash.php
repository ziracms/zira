<?php
/**
 * Zira project.
 * dash.php
 * (c)2017 http://dro1d.ru
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
        Zira\View::addStyle('designer/editor.css');
        Zira\View::addScript('designer/editor.js');
        Zira\View::addSlider('slider', array(
            'auto' => true,
            'speed' => 500,
            'pause' => 8000,
            'captions' => true,
            'slideMargin' => 0,
            'adaptiveHeight' => false
        ));
        Zira\View::addColorpickerAssets();
        
        $css = Zira\Helper::tag_open('style');
        $css .= $style->content;
        $css .= Zira\Helper::tag_close('style');
        Zira\View::addHTML($css, Zira\View::VAR_HEAD_BOTTOM);
        
        Zira\Router::setModule(DEFAULT_MODULE);
        Zira\Router::setController(DEFAULT_CONTROLLER);
        Zira\Router::setAction(DEFAULT_ACTION);
        
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
}