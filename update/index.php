<?php
/**
 * Zira project.
 * index.php
 * (c)2017 http://dro1d.ru
 */

if (file_exists('.forbidden')) {
    http_response_code(403);
    include('..' . DIRECTORY_SEPARATOR . '403.html');
    exit;
}

require('..' . DIRECTORY_SEPARATOR . 'const.php');
if (file_exists('../config.php')) $config = @include '../config.php';
if (empty($config) || !is_array($config)) {
    http_response_code(403);
    die('Zira is not installed');
}

chdir('..');
define('ZIRA_UPDATE', true);
define('REAL_PATH', realpath(ROOT_DIR));

error_reporting(E_ALL);
function error_handler($severity, $message, $file, $line) {
    if (LOG_ERRORS) {
        Zira\Log::write(Zira\Log::getErrorType($severity).': '.$message.' in '.$file.':'.$line);
    }
    throw new ErrorException(Zira\Log::getErrorType($severity).': '.$message, 0, $severity, $file, $line);
}
set_error_handler('error_handler', E_ALL);

function shutdown_handler() {
    $error = error_get_last();
    if (!$error) {
        Zira::getInstance()->shutdown();
    } else if (isset($error['type']) && $error['type']==E_ERROR) {
        // trying to log fatal errors
        $message = isset($error['message']) ? $error['message'] : 'unknown error';
        $file = isset($error['file']) ? $error['file'] : 'unknown file';
        $line = isset($error['line']) ? $error['line'] : 'unknown line';
        Zira\Log::write('Fatal error: '.$message.' in '.$file.':'.$line);
    }
}
register_shutdown_function('shutdown_handler');

spl_autoload_extensions('.php');
spl_autoload_register();

try {
    \Zira\Config::setSystemDefaults($config);
    unset($config);
} catch (Exception $e) {
    Zira\Response::exception($e);
}

$languages = array();
$d=opendir(ROOT_DIR . DIRECTORY_SEPARATOR . LANGUAGES_DIR);
while(($f=readdir($d))!==false) {
    if ($f=='.' || $f=='..') continue;
    if (!is_dir(ROOT_DIR . DIRECTORY_SEPARATOR .LANGUAGES_DIR . DIRECTORY_SEPARATOR . $f)) continue;
    $languages []= $f;
}
closedir($d);

$language = Zira\Request::get('lang', 'ru');
if (!in_array($language, $languages)) $language = 'en';

Zira\Log::init();
Zira\Session::start();
Zira\Db\Loader::initialize();
Zira\Db\Db::open();
Zira\Config::load();
Zira\Datetime::init();
        
Zira\Locale::load($language, 'update');
if (Zira\Locale::getLanguage() && Zira\Config::get('db_translates')) {
    Zira\Locale::loadDBStrings();
}

$version = Zira::VERSION;
$db_version = Zira\Config::get('db_version', 0);

$v_arr = array();
$d = opendir(ROOT_DIR . DIRECTORY_SEPARATOR . 'update');
while(($f = readdir($d))!==false) {
    if (!is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR . $f)) continue;
    if (strpos($f, 'v')!==0 || strlen($f)<=1) continue;
    $v = intval(substr($f, 1));
    if ($v>0 && !in_array($v, $v_arr)) {
        $v_arr []= $v;
    }
}
sort($v_arr);
$db_update_version = $v_arr[count($v_arr)-1];

Zira\View::setTheme(DEFAULT_THEME);
Zira\View::setRenderJsStrings(false);
Zira\View::setRenderBreadcrumbs(false);
Zira\View::addDefaultAssets();
//Zira\View::addThemeAssets();
Zira\View::addWidget('\Zira\Widgets\Logo');

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

$step = (int)Zira\Request::post('step', 0);
if ($step>0) {
    if($step <= $db_version) {
        $response = array('success' => true);
    } else if ($step <= $db_update_version) {
        try {
            Zira\Db\Db::begin();
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR . 'v' . $step . DIRECTORY_SEPARATOR . 'index.php';
            if (file_exists($path)) include($path);
            Zira\Models\Option::write('db_version', $step);
            Zira\Db\Db::commit();
            $response = array('success' => true);
        } catch(\Exception $e) {
            Zira\Db\Db::rollback();
            $response = array('error' => $e->getMessage());
            Zira\Log::write($e->getMessage());
        }
        Zira\Cache::clear();
    } else {
        $response = array('error' => Zira\Locale::t('An error occurred.'));
    }
    echo json_encode($response);
    Zira\Session::close();
    exit;
}

$init_content = Zira\Helper::tag_open('div', array('id'=>'zira-update-container'));
                
if ($db_version < $db_update_version) {
    $init_content .= Zira\Helper::tag('p', Zira\Locale::t('Database version: %s', $db_version)).
                    Zira\Helper::tag('p', Zira\Locale::t('Database needs to be updated to version: %s', $db_update_version)).
                    Zira\Helper::tag('p', Zira\Locale::t('Zira CMS is ready to update.')).
                    Zira\Helper::tag_open('div', array('style'=>'margin:40px 0px 100px')).
                    Zira\Helper::tag('button', Zira\Locale::t('Update'), array('class'=>'btn btn-primary', 'id'=>'zira-update-start-btn')).
                    Zira\Helper::tag_close('div');
    
    $init_js = Zira\Helper::tag_open('script',array('type'=>'text/javascript')).
            'zira_strings = {\'Error\':\''.Zira\Locale::t('Error').'\', \'Message\':\''.Zira\Locale::t('Message').'\', \'Close\':\''.Zira\Locale::t('Close').'\', \'Please wait\': \''.Zira\Locale::t('Please wait').'\', \'Updated successfully!\': \''.Zira\Locale::t('Updated successfully!').'\', \'Database is up to date.\': \''.Zira\Locale::t('Database is up to date.').'\'};'.
            '(function($){'.
            'zira_update_step = '.$db_version.';'.
            'zira_update_target = '.$db_update_version.';'.
            'zira_update_start_version = zira_update_step;'.
            'zira_update = function() {'.
            '$(\'#zira-update-start-btn\').attr(\'disabled\', \'disabled\');'.  
            'zira_update_step++;'.
            'zira_modal_progress(\''.Zira\Locale::t('Update in progress').'\');'.
            'zira_update_request(zira_update_step);'.
            '};'.
            'zira_update_request = function(step) {'.
            'if (step > zira_update_target) return;'.
            '$(\'body\').css(\'cursor\',\'wait\');'.
            'var data = {\'step\': step};'.
            'zira_update_xhr=$.post(\'?lang='.Zira\Helper::html($language).'\', data, function(response){'.
            'if (!response) { zira_error(\''.Zira\Locale::t('An error occurred').'\'); return; }'.
            'if (response.error) {'.
            'zira_modal_progress_hide();'.
            'zira_error(response.error);'.
            '}'.
            'if (response.success) {'.
            'var progress_total = zira_update_target - zira_update_start_version;'.
            'var progress_current = zira_update_step - zira_update_start_version;'.
            'var progress_percent = Math.floor(progress_current * 100 / progress_total);'.
            'zira_modal_progress_update(progress_percent);'.
            'if (zira_update_step < zira_update_target) {'.
            'zira_update_step++;'.
            'window.setTimeout(\'zira_update_request(zira_update_step);\',1000);'.
            '} else {'.
            'zira_modal_progress_hide();'.
            'zira_message(t(\'Updated successfully!\'));'.
            '$(\'#zira-update-container\').text(t(\'Database is up to date.\'));'.
            '}'.
            '}'.
            '$(\'body\').css(\'cursor\',\'default\');'.
            '}, \'json\').always(function(){'.
            'if (zira_update_xhr.status != 200) zira_error(\''.Zira\Locale::t('An error occurred').'\'+\'.\');'.
            '});'.
            '};'.
            '$(document).ready(function(){'.
            '$(\'#zira-update-start-btn\').click(zira_update);'.
            '});'.
            '})(jQuery);'.
            Zira\Helper::tag_close('script');
} else {
    $init_content .= Zira\Helper::tag('p', Zira\Locale::t('Database is up to date.'));
    
    $init_js = '';
}
$init_content .= Zira\Helper::tag_close('div');

$view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            THEMES_DIR . DIRECTORY_SEPARATOR .
            DEFAULT_THEME . DIRECTORY_SEPARATOR .
            'page.php';

$layout_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            'update' . DIRECTORY_SEPARATOR .
            'layout.php';

Zira\View::addBodyBottomScript($init_js);

Zira\View::$data = array(
    Zira\View::VAR_TITLE => Zira\Locale::t('Zira CMS update wizzard'),
    Zira\View::VAR_CONTENT => $init_content
);

Zira\View::$view = $view_file;
Zira\View::$layout = $layout_file;

Zira\View::renderLayout();
Zira\Session::close();