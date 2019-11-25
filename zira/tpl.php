<?php
/**
 * Zira project
 * tpl.php
 * (c)2015 https://github.com/ziracms/zira
 */

function t($str, $arg = null) {
    return Zira\Locale::t($str, $arg);
}

function tm($str, $module, $arg = null) {
    return Zira\Locale::tm($str, $module, $arg);
}

function render($data, $view) {
    Zira\View::renderView($data, $view);
}

function renderSlider() {
    $type = Zira\Config::get('slider_type');
    if (!Zira\Router::getRequest() && Zira\Router::getModule()==DEFAULT_MODULE && Zira\Router::getController()==DEFAULT_CONTROLLER && Zira\Router::getAction()==DEFAULT_ACTION) {
        $type = Zira\Config::get('home_slider_type', $type);
    }
    if ($type == 'fullscreen') return;
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_SLIDER_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_SLIDER_DATA], Zira\Page::VIEW_PLACEHOLDER_SLIDER_VIEW);
    }
}

function renderFullscreenSlider() {
    $type = Zira\Config::get('slider_type');
    if (!Zira\Router::getRequest() && Zira\Router::getModule()==DEFAULT_MODULE && Zira\Router::getController()==DEFAULT_CONTROLLER && Zira\Router::getAction()==DEFAULT_ACTION) {
        $type = Zira\Config::get('home_slider_type', $type);
    }
    if ($type != 'fullscreen') return;
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_SLIDER_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_SLIDER_DATA], Zira\Page::VIEW_PLACEHOLDER_SLIDER_VIEW);
    }
}

function renderVideo() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_VIDEO_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_VIDEO_DATA], Zira\Page::VIEW_PLACEHOLDER_VIDEO_VIEW);
    }
}

function renderGallery() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_GALLERY_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_GALLERY_DATA], Zira\Page::VIEW_PLACEHOLDER_GALLERY_VIEW);
    }
}

function renderAudio() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_AUDIO_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_AUDIO_DATA], Zira\Page::VIEW_PLACEHOLDER_AUDIO_VIEW);
    }
}

function renderFiles() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_FILES_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_FILES_DATA], Zira\Page::VIEW_PLACEHOLDER_FILES_VIEW);
    }
}

function renderComments() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_COMMENTS_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_COMMENTS_DATA], Zira\Page::VIEW_PLACEHOLDER_COMMENTS_VIEW);
    }
}

function renderContentView() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA])) {
        Zira\Page::renderContentView(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA]);
    }
}

function renderTags() {
    if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_TAGS_DATA])) {
        render(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_TAGS_DATA], Zira\Page::VIEW_PLACEHOLDER_TAGS_VIEW);
    }
}

function renderContentWidgets() {
    if (Zira\View::$content_widgets_rendered) return;
    Zira\View::$content_widgets_rendered = true;
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT);
}

function layout_js_begin() {
    ob_start();
}

function layout_js_end() {
    Zira\View::addBodyBottomScript(ob_get_clean());
}

function breadcrumbs() {
    echo Zira\Page::breadcrumbs();
}

function layout_head() {
    echo
        Zira\View::getLayoutData(Zira\View::VAR_HEAD_TOP) .
        Zira\View::getLayoutData(Zira\View::VAR_CHARSET) .
        Zira\View::getLayoutData(Zira\View::VAR_TITLE) .
        Zira\View::getLayoutData(Zira\View::VAR_META) .
        Zira\View::getLayoutData(Zira\View::VAR_STYLES) .
        (!INSERT_SCRIPTS_TO_BODY ? Zira\View::getLayoutData(Zira\View::VAR_SCRIPTS) : '').
        Zira\View::getLayoutData(Zira\View::VAR_HEAD_BOTTOM)
    ;
    Zira\View::renderWidgets(Zira\View::VAR_HEAD_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_HEAD_BOTTOM);
}

function layout_body_top() {
    echo Zira\View::getLayoutData(Zira\View::VAR_BODY_TOP);
    Zira\View::includePlaceholderViews(Zira\View::VAR_BODY_TOP);
    Zira\View::renderWidgets(Zira\View::VAR_BODY_TOP);
}

function layout_body_bottom() {
    echo  Zira\View::getBodyBottomScripts() . 
          Zira\View::getLayoutData(Zira\View::VAR_BODY_BOTTOM);
    Zira\View::renderWidgets(Zira\View::VAR_BODY_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_BODY_BOTTOM);
}

function layout_content_top() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT_TOP);
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT_TOP);
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT_TOP);
}

function layout_content_bottom() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT_BOTTOM);
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT_BOTTOM);
}

function layout_sidebar_left() {
    echo Zira\View::getLayoutData(Zira\View::VAR_SIDEBAR_LEFT);
    Zira\View::renderWidgets(Zira\View::VAR_SIDEBAR_LEFT);
    Zira\View::includePlaceholderViews(Zira\View::VAR_SIDEBAR_LEFT);
}

function layout_sidebar_right() {
    echo Zira\View::getLayoutData(Zira\View::VAR_SIDEBAR_RIGHT);
    Zira\View::renderWidgets(Zira\View::VAR_SIDEBAR_RIGHT);
    Zira\View::includePlaceholderViews(Zira\View::VAR_SIDEBAR_RIGHT);
}

function layout_header() {
    echo Zira\View::getLayoutData(Zira\View::VAR_HEADER);
    Zira\View::includePlaceholderViews(Zira\View::VAR_HEADER);
    Zira\View::renderWidgets(Zira\View::VAR_HEADER);
}

function layout_footer() {
    Zira\View::renderWidgets(Zira\View::VAR_FOOTER);
    Zira\View::includePlaceholderViews(Zira\View::VAR_FOOTER);
    echo Zira\View::getLayoutData(Zira\View::VAR_FOOTER);
}

function layout_content() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT);
    Zira\View::renderContent(Zira\View::$data, Zira\View::$view);
    renderContentWidgets();
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT);
}