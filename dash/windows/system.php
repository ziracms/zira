<?php
/**
 * Zira project.
 * system.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Dash;
use Zira\Permission;

class System extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-info-sign';
    protected static $_title = 'System';

    protected $_help_url = 'zira/help/system';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setToolbarEnabled(false);
    }

    protected function getLogo() {
        return Zira\Helper::tag_open('div', array('style'=>'width:180px;margin:20px auto')).
                Zira\Helper::tag_short('img', array('src'=>Zira\Helper::imgUrl('zira.png'),'width'=>70,'height'=>70,'alt'=>'Zira')).
                Zira\Helper::tag('span', 'Zira', array('style'=>'font-size:40px;vertical-align:middle')).
                Zira\Helper::tag_open('a', array('href'=>'http://dro1d.ru', 'target'=>'_blank', 'style'=>'display:block;text-align:center;color:#472053')).
                Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-copyright-mark')).
                ' dro1d.ru'.
                Zira\Helper::tag_close('a').
                Zira\Helper::tag_close('div');
    }

    protected function getInfo() {
        $os_name = php_uname('s');
        $server_name = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '?';
        $server_name .= ' ('.php_sapi_name().')';

        $phpversion = phpversion();
        $php_prefix = Zira\Helper::tag('span',null,array('class'=>(floatval($phpversion)>=5.5 ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));

        $pdo_installed = class_exists('PDO');
        if ($pdo_installed) {
            $pdo_drivers = \PDO::getAvailableDrivers();
        }
        $pdo = Zira\Helper::tag('span',null,array('class'=>($pdo_installed ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $pdo .= ' PDO: '.($pdo_installed ? implode(', ',$pdo_drivers) : Zira\Locale::t('not supported'));

        $gdversion = function_exists('gd_info') ? gd_info()['GD Version'] : 0;
        $gd = Zira\Helper::tag('span',null,array('class'=>($gdversion ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $gd .= ' GD '.($gdversion ? $gdversion : Zira\Locale::t('not supported'));

        $zip_supported = class_exists('ZipArchive');
        $zip = Zira\Helper::tag('span',null,array('class'=>($zip_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $zip .= ' ZIP '.($zip_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

        $gzip_supported = function_exists('gzencode') && !@ini_get('zlib.output_compression');
        $gzip = Zira\Helper::tag('span',null,array('class'=>($gzip_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $gzip .= ' GZIP '.($gzip_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

        $openssl_supported = function_exists('openssl_random_pseudo_bytes');
        $openssl = Zira\Helper::tag('span',null,array('class'=>($openssl_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $openssl .= ' OpenSSL '.($openssl_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

        $mbstring_supported = function_exists('mb_check_encoding');
        $mbstring = Zira\Helper::tag('span',null,array('class'=>($mbstring_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $mbstring .= ' mbstring '.($mbstring_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

        $json_supported = function_exists('json_encode');
        $json = Zira\Helper::tag('span',null,array('class'=>($json_supported ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $json .= ' JSON '.($json_supported ? Zira\Locale::t('supported') : Zira\Locale::t('not supported'));

        $cache_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR);
        $cache_dir = Zira\Helper::tag('span',null,array('class'=>($cache_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $cache_dir .= ' '.Zira\Locale::t('%s directory','cache').' '.($cache_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

        $log_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR);
        $log_dir = Zira\Helper::tag('span',null,array('class'=>($log_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $log_dir .= ' '.Zira\Locale::t('%s directory','log').' '.($log_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

        $upload_dir_writatable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR);
        $upload_dir = Zira\Helper::tag('span',null,array('class'=>($upload_dir_writatable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $upload_dir .= ' '.Zira\Locale::t('%s directory','uploads').' '.($upload_dir_writatable ? Zira\Locale::t('is writable') : Zira\Locale::t('is not writable'));

        $config_writable = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . 'config.php');
        $config = Zira\Helper::tag('span',null,array('class'=>(!$config_writable ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $config .= ' config.php '.(!$config_writable ? Zira\Locale::t('is protected') : Zira\Locale::t('is not protected'));

        $cron_run = Zira\Config::get('cron_run');
        $cron_ok = $cron_run && time() - $cron_run < 2592000;
        $cron = Zira\Helper::tag('span',null,array('class'=>($cron_ok ? 'glyphicon glyphicon-ok-sign system-ok' : 'glyphicon glyphicon-warning-sign system-warning')));
        $cron .= ' '.Zira\Locale::t('Cron').' '.($cron_run ? Zira\Locale::t('last run %s', date('Y/m/d H:i:s',$cron_run)) : Zira\Locale::t('never run'));

        return Zira\Helper::tag_open('ul', array('class'=>'system-options-list','style'=>'width:400px;list-style-type:none;margin:20px auto;list-style-position:inside;background-color:#e5d7f6;padding:10px;border-radius:3px;box-shadow:0px 0px 2px #dddddd;')).
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.Zira\Locale::t('Version: %s',Zira::VERSION).Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.$os_name.' / '.$server_name.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.Zira\Locale::t('Server time: %s',date('Y/m/d H:i:s')).Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$php_prefix.' PHP '.$phpversion.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.Zira\Db\Db::version().Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$pdo.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$gd.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$zip.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$gzip.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$openssl.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$mbstring.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$json.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-question-sign')).' '.Zira\Locale::t('Clean URLs').' '.Zira\Helper::tag('span',Zira\Locale::t('is being checked...'),array('id'=>'sys-info-clean-url-option')).Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.Zira\Locale::t('Memory limit').': '.@ini_get('memory_limit').Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-info-sign')).' '.Zira\Locale::t('Max. upload size').': '.min(@ini_get('post_max_size'),@ini_get('upload_max_filesize')).Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$cron.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$cache_dir.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$log_dir.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$upload_dir.Zira\Helper::tag_close('li').
                Zira\Helper::tag_open('li').$config.Zira\Helper::tag_close('li').
                Zira\Helper::tag_close('ul');
    }

    public function create() {
        $this->setBodyContent(
            $this->getLogo()
        );

        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Database dump'), 'glyphicon glyphicon-save', 'desk_call(dash_system_dump, this);', 'create'),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Clear cache'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_system_cache, this);', 'create'),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('System files'), 'glyphicon glyphicon-tree-deciduous', 'desk_call(dash_system_files, this);', 'create')
            ))
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Database dump'), 'glyphicon glyphicon-save', 'desk_call(dash_system_dump, this);', 'create'),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Clear cache'), 'glyphicon glyphicon-remove-sign', 'desk_call(dash_system_cache, this);', 'create'),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('System files'), 'glyphicon glyphicon-tree-deciduous', 'desk_call(dash_system_files, this);', 'create')
        ));

        $this->addStrings(array(
            'supported',
            'not supported'
        ));

        $this->includeJS('dash/system');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $this->setBodyContent(
            $this->getLogo().
            $this->getInfo()
        );

        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_system_load, this);'
            )
        );
    }
}