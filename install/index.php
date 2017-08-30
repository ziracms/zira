<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

ob_start();
if (file_exists('../config.php')) $config = @include '../config.php';
if (!empty($config) && is_array($config)) {
    http_response_code(403);
    include('..' . DIRECTORY_SEPARATOR . '403.html');
    exit;
}
ob_clean();

error_reporting(E_ALL);
define('ZIRA_INSTALL', true);
include('..' . DIRECTORY_SEPARATOR . 'const.php');

spl_autoload_register(function($class){
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $path = '..' . DIRECTORY_SEPARATOR . strtolower($class) . '.php';
    if (file_exists($path)) {
        include_once($path);
    } else {
        echo 'Failed to load '.$class.'. File '.$path.' not found';
        exit;
    }
});

$steps = array(
    'environment',
    'license',
    'credentials',
    'configuration',
    'process'
);

$languages = array();
$d=opendir('..' . DIRECTORY_SEPARATOR . LANGUAGES_DIR);
while(($f=readdir($d))!==false) {
    if ($f=='.' || $f=='..') continue;
    if (!is_dir('..' . DIRECTORY_SEPARATOR .LANGUAGES_DIR . DIRECTORY_SEPARATOR . $f)) continue;
    $languages []= $f;
}
closedir($d);

$language = isset($_GET['lang']) && in_array($_GET['lang'], $languages) ? $_GET['lang'] : 'ru';
$step = isset($_GET['step']) ? intval($_GET['step']) : 0;
if ($step<0 || $step>count($steps)) $step=0;
$process = isset($_GET['process']) ? intval($_GET['process']) : 0;

if (!defined('ROOT_DIR')) define('ROOT_DIR','..');
if (!defined('BASE_URL')) define('BASE_URL', '..');
if (!defined('SECRET')) define('SECRET', 'zIraTmPseCret');

Zira\Session::start();
Zira\Locale::load($language, 'install');

if ($process<=0 && $step>0) {
    $response = include('.' . DIRECTORY_SEPARATOR . $steps[$step-1] . '.php');
    echo json_encode($response);
    Zira\Session::close();
    exit;
} else if ($process>0) {
    $response = include('.' . DIRECTORY_SEPARATOR . 'operate.php');
    echo json_encode($response);
    Zira\Session::close();
    exit;
}

Zira\View::setTheme(DEFAULT_THEME);
Zira\View::setRenderJsStrings(false);
Zira\View::setRenderBreadcrumbs(false);
Zira\View::addDefaultAssets();
//Zira\View::addThemeAssets();
Zira\View::addWidget('\Zira\Widgets\Logo');

Zira\Config::set('language', $language);
Zira\Config::set('languages', $languages);
Zira\Config::set('site_name', 'Zira CMS');
Zira\Config::set('site_slogan', '');
Zira\Config::set('site_logo', 'assets/images/zira.png');

Zira\Helper::setAddingLanguageToUrl(false);
$html = Zira\Helper::tag_open('ul', array('id'=>'language-switcher'));
foreach($languages as $_language) {
    if ($_language == $language) $class='active';
    else $class = '';
    $html .= Zira\Helper::tag_open('li');
    $html .= Zira\Helper::tag('a', Zira\Helper::html(ucfirst($_language)), array('href'=>'?lang='.Zira\Helper::html($_language), 'class'=>$class));
    $html .= Zira\Helper::tag_close('li');
}
$html .= Zira\Helper::tag_close('ul');
Zira\View::addHTML($html, Zira\View::VAR_HEADER);

$init_content = Zira\Helper::tag_open('div', array('id'=>'zira-install-container')).
                Zira\Helper::tag('h2', Zira\Locale::t('Welcome to Zira CMS installer')).
                Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('Zira CMS is a lightweight, flexible and easy to use content management system.')).
                Zira\Helper::tag('p', Zira\Locale::t('Installing Zira CMS, you get the most commonly used features right out of the box.')).
                Zira\Helper::tag('p', Zira\Locale::t('No need for extra downloads and plugins setup.')).
                Zira\Helper::tag('p', Zira\Locale::t('Zira CMS brings desktop experience to your website - no web development skills required!')).
                Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('Main features:')).
                Zira\Helper::tag_open('ul').
                Zira\Helper::tag('li', Zira\Locale::t('Simple management')).
                Zira\Helper::tag('li', Zira\Locale::t('Fast and flexible')).
                Zira\Helper::tag('li', Zira\Locale::t('SEO friendly')).
                Zira\Helper::tag('li', Zira\Locale::t('Free of charge')).
                Zira\Helper::tag_close('ul').
                Zira\Helper::tag_open('div', array('style'=>'margin:40px 0px 100px')).
                Zira\Helper::tag('button', Zira\Locale::t('Install'), array('class'=>'btn btn-primary', 'id'=>'zira-install-start-btn')).
                Zira\Helper::tag_close('div').
                Zira\Helper::tag_close('div').
                Zira\Helper::tag_open('div', array('id'=>'zira-install-pager', 'class'=>'btn-group', 'style'=>'display:none;margin:40px 0px 100px')).
                Zira\Helper::tag('button', '⇦ '.Zira\Locale::t('Backward'), array('class'=>'btn btn-default', 'id'=>'zira-install-backward-btn', 'disabled'=>'disabled')).
                Zira\Helper::tag('button', Zira\Locale::t('Forward').' ⇨', array('class'=>'btn btn-primary', 'id'=>'zira-install-forward-btn', 'disabled'=>'disabled')).
                Zira\Helper::tag_close('div');

$init_js = Zira\Helper::tag_open('script',array('type'=>'text/javascript')).
            'zira_strings = {\'Error\':\''.Zira\Locale::t('Error').'\', \'Message\':\''.Zira\Locale::t('Message').'\', \'Close\':\''.Zira\Locale::t('Close').'\', \'Please wait\': \''.Zira\Locale::t('Please wait').'\'};'.
            'zira_install_clean_url = false;'.
            '(function($){'.
            'zira_install_page = 0;'.
            'zira_process_page = 0;'.
            'zira_install_init = function() {'.
            'if (zira_install_page>1) $(\'#zira-install-backward-btn\').removeAttr(\'disabled\');'.
            'else $(\'#zira-install-backward-btn\').attr(\'disabled\', \'disabled\');'.
            'if (zira_install_page<'.count($steps).') $(\'#zira-install-forward-btn\').removeAttr(\'disabled\');'.
            'else $(\'#zira-install-forward-btn\').attr(\'disabled\', \'disabled\');'.
            '$(\'form\').submit(function(e){'.
            'e.stopPropagation(); e.preventDefault();'.
            'zira_install_forward();'.
            '});'.
            '};'.
            'zira_install_backward = function() {'.
            '$(\'#zira-install-backward-btn\').attr(\'disabled\', \'disabled\');'.
            '$(\'#zira-install-forward-btn\').attr(\'disabled\', \'disabled\');'.
            'zira_install_page--;'.
            'zira_install_request();'.
            '};'.
            'zira_install_forward = function() {'.
            '$(\'#zira-install-backward-btn\').attr(\'disabled\', \'disabled\');'.
            '$(\'#zira-install-forward-btn\').attr(\'disabled\', \'disabled\');'.
            'zira_install_page++;'.
            'zira_install_request();'.
            '};'.
            'zira_install_request = function() {'.
            '$(\'body\').css(\'cursor\',\'wait\');'.
            'var data = {};'.
            'var form = $(\'#zira-install-container\').find(\'form\');'.
            'if ($(form).length>0) {'.
            'var formData = $(form).eq(0).serializeArray();'.
            'for (var i=0; i<formData.length; i++) {'.
            'data[formData[i][\'name\']]=formData[i][\'value\'];'.
            '}'.
            '}'.
            'zira_install_xhr=$.post(\'?lang='.Zira\Helper::html($language).'&step=\'+zira_install_page+\'&process=\'+zira_process_page, data, function(response){'.
            'if (!response) { zira_error(\''.Zira\Locale::t('An error occurred').'\'); return; }'.
            'if (response.error) {'.
            'zira_install_page--;'.
            'zira_modal_progress_hide();'.
            'zira_error(response.error);'.
            '}'.
            'if (response.message) zira_message(response.message);'.
            'if (response.content) {'.
            '$(\'#zira-install-container\').html(response.content);'.
            '}'.
            '$(\'body\').css(\'cursor\',\'default\');'.
            '$(\'#zira-install-pager\').show();'.
            '$(\'body, html\').animate({scrollTop:$(\'#content\').offset().top}, 600);'.
            'zira_install_init();'.
            'if (response.script) {'.
            'eval(response.script);'.
            '}'.
            '}, \'json\').always(function(){'.
            'if (zira_install_xhr.status != 200) zira_error(\''.Zira\Locale::t('An error occurred').'\'+\'. \'+\''.Zira\Locale::t('File config.php should be empty.').'\');'.
            '});'.
            '};'.
            '$(document).ready(function(){'.
            '$(\'#zira-install-start-btn\').click(zira_install_forward);'.
            '$(\'#zira-install-backward-btn\').click(zira_install_backward);'.
            '$(\'#zira-install-forward-btn\').click(zira_install_forward);'.
            'zira_install_init();'.
            '});'.
            '})(jQuery);'.
            Zira\Helper::tag_close('script');

$view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            THEMES_DIR . DIRECTORY_SEPARATOR .
            DEFAULT_THEME . DIRECTORY_SEPARATOR .
            'page.php';

$layout_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            'install' . DIRECTORY_SEPARATOR .
            'layout.php';

Zira\View::addBodyBottomScript($init_js);

Zira\View::$data = array(
    Zira\View::VAR_TITLE => Zira\Locale::t('Installation'),
    Zira\View::VAR_CONTENT => $init_content
);

Zira\View::$view = $view_file;
Zira\View::$layout = $layout_file;

Zira\View::renderLayout();
Zira\Session::close();