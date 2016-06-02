<?php
/**
 * Zira project.
 * environment.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

$supported = true;

$os_name = php_uname('s');
$server_name = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '?';
$server_name .= ' ('.php_sapi_name().')';

$phpversion = phpversion();
if (floatval($phpversion)<5.5) $supported = false;
$php_prefix = Zira\Helper::tag('span',null,array('class'=>(floatval($phpversion)>=5.5 ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));

$pdo_installed = class_exists('PDO');
if ($pdo_installed) {
    $pdo_drivers = \PDO::getAvailableDrivers();
} else {
    $supported = false;
}
$pdo = Zira\Helper::tag('span',null,array('class'=>($pdo_installed ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$pdo .= ' PDO: '.($pdo_installed ? implode(', ',$pdo_drivers) : Zira\Locale::t('not supported'));

$gdversion = 0;
if (function_exists('gd_info')) {
	$gd_info = gd_info();
	$gdversion = $gd_info['GD Version'] ;
}
if (!$gdversion) $supported = false;
$gd = Zira\Helper::tag('span',null,array('class'=>($gdversion ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$gd .= ' GD '.($gdversion ? $gdversion : Zira\Locale::t('not supported'));

$zip_supported = class_exists('ZipArchive');
$zip = Zira\Helper::tag('span',null,array('class'=>($zip_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$zip .= ' ZIP '.($zip_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

$gzip_supported = function_exists('gzencode') && !@ini_get('zlib.output_compression');
$gzip = Zira\Helper::tag('span',null,array('class'=>($gzip_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$gzip .= ' GZIP '.($gzip_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

$openssl_supported = function_exists('openssl_random_pseudo_bytes');
if (!$openssl_supported) $supported = false;
$openssl = Zira\Helper::tag('span',null,array('class'=>($openssl_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$openssl .= ' OpenSSL '.($openssl_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

$mbstring_supported = function_exists('mb_check_encoding');
if (!$mbstring_supported) $supported = false;
$mbstring = Zira\Helper::tag('span',null,array('class'=>($mbstring_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$mbstring .= ' mbstring '.($mbstring_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

$json_supported = function_exists('json_encode');
if (!$json_supported) $supported = false;
$json = Zira\Helper::tag('span',null,array('class'=>($json_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$json .= ' JSON '.($json_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

$cache_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR);
if (!$cache_dir_writatable) $supported = false;
$cache_dir = Zira\Helper::tag('span',null,array('class'=>($cache_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$cache_dir .= ' '.Zira\Locale::t('%s directory','cache').' '.($cache_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

$log_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR);
if (!$log_dir_writatable) $supported = false;
$log_dir = Zira\Helper::tag('span',null,array('class'=>($log_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$log_dir .= ' '.Zira\Locale::t('%s directory','log').' '.($log_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

$upload_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR);
if (!$upload_dir_writatable) $supported = false;
$upload_dir = Zira\Helper::tag('span',null,array('class'=>($upload_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$upload_dir .= ' '.Zira\Locale::t('%s directory','uploads').' '.($upload_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

$htaccess_exists = file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . '.htaccess');
if (!$htaccess_exists) $supported = false;
$htaccess = Zira\Helper::tag('span',null,array('class'=>($htaccess_exists ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$htaccess .= ' '.Zira\Locale::t('File %s','.htaccess').' '.($htaccess_exists ? Zira\Locale::t('is exists') : Zira\Locale::t('is not exists'));

$robots_exists = file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'robots.txt');
if (!$robots_exists) $supported = false;
$robots = Zira\Helper::tag('span',null,array('class'=>($robots_exists ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
$robots .= ' '.Zira\Locale::t('File %s','robots.txt').' '.($robots_exists ? Zira\Locale::t('is exists') : Zira\Locale::t('is not exists'));

$config_exists = file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'config.php');
$config_writatable = false;
if (!$config_exists) $supported = false;
else $config_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . 'config.php');
if (!$config_writatable) $supported = false;
$config = Zira\Helper::tag('span',null,array('class'=>($config_exists && $config_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
if (!$config_exists) $config .= ' '.Zira\Locale::t('File %s','config.php').' '.($config_exists ? Zira\Locale::t('is exists') : Zira\Locale::t('is not exists'));
else $config .= ' '.Zira\Locale::t('File %s','config.php').' '.($config_writatable ? Zira\Locale::t('is writeable') : Zira\Locale::t('is not writeable'));

$response = array('content'=>Zira\Helper::tag('style', '.system-ok { color: green; } .system-warning { color: red; }').
                        ($supported ? Zira\Helper::tag_open('h2').Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-ok-sign')).' '.Zira\Locale::t('System is ready to be installed').Zira\Helper::tag_close('h2') : Zira\Helper::tag('h2', Zira\Locale::t('Preparing to install'))).
                        Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                        (!$htaccess_exists || !$robots_exists || !$config_exists ?
                        Zira\Helper::tag('p', Zira\Locale::t('You have to rename the following files:')).
                        Zira\Helper::tag_open('ul').
                        Zira\Helper::tag('li', 'htaccess.txt ⇨ .htaccess').
                        Zira\Helper::tag('li', 'robots.src.txt ⇨ robots.txt').
                        Zira\Helper::tag('li', 'config.src.php ⇨ config.php').
                        Zira\Helper::tag_close('ul').
                        Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p')
                        : '').
                        Zira\Helper::tag('p', Zira\Locale::t('Server test:')).
                        Zira\Helper::tag_open('ul', array('class'=>'system-options-list', 'style'=>'list-style-type: none; padding: 0px')).
                        Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.$os_name.' / '.$server_name.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$php_prefix.' PHP '.$phpversion.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$pdo.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$gd.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$zip.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$gzip.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$openssl.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$mbstring.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$json.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-question-sign')).' '.Zira\Locale::t('Clean URLs').' '.Zira\Helper::tag('span',Zira\Locale::t('is being checked...'),array('id'=>'sys-info-clean-url-option')).Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$cache_dir.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$log_dir.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$upload_dir.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$htaccess.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$robots.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_open('li').$config.Zira\Helper::tag_close('li').
                        Zira\Helper::tag_close('ul'),
                  'script' =>
                        'zira_install_clean_url = false;'.
                        '$.get(\''.Zira\Helper::baseUrl('install/check/1').'\', function(response){'.
                        'if (response) zira_install_clean_url = true;'.
                        '}).always(function(){'.
                        'if (zira_install_clean_url) {'.
                        '$(\'#sys-info-clean-url-option\').text(\''.Zira\Locale::t('supported').'\');'.
                        '$(\'#sys-info-clean-url-option\').parent().children(\'.glyphicon\').removeClass(\'glyphicon-question-sign\').addClass(\'glyphicon-ok-sign\').addClass(\'system-ok\');'.
                        '} else {'.
                        '$(\'#sys-info-clean-url-option\').text(\''.Zira\Locale::t('not supported').'\');'.
                        '$(\'#sys-info-clean-url-option\').parent().children(\'.glyphicon\').removeClass(\'glyphicon-question-sign\').addClass(\'glyphicon-warning-sign\').addClass(\'system-warning\');'.
                        '}'.
                        '});'
);

if (!$supported) $response['error'] = !$htaccess_exists || !$robots_exists || !$config_exists ? Zira\Locale::t('Filesystem need to be prepared for installation') : Zira\Locale::t('Zira installer cannot continue');

return $response;