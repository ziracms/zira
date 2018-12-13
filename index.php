<?php
/**
 * Zira project
 * index.php
 * (c)2015 http://dro1d.ru
 */

define('START_TIME', microtime(true));

require('const.php');
if (file_exists('config.php')) $config = @include('config.php');
if ((empty($config) || !is_array($config))) {
    require_once('zira/request.php');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    if (Zira\Request::isInstallRequestUri()) header('Location: index.php');
    else if (Zira\Request::isBaseRequestUri() || Zira\Request::isBaseRequestUriAlt()) header('Location: install/index.php');
    exit;
}

define('REAL_PATH', realpath(ROOT_DIR));

error_reporting(E_ALL);
function error_handler($severity, $message, $file, $line) {
    if (LOG_ERRORS) {
        Zira\Log::write(Zira\Log::getErrorType($severity).': '.$message.' in '.$file.':'.$line);
    }
    if ((defined('DEBUG') && DEBUG) || ($severity != E_DEPRECATED && $severity != E_STRICT)) {
        throw new ErrorException(Zira\Log::getErrorType($severity).': '.$message, 0, $severity, $file, $line);
    }
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
    Zira::getInstance()->bootstrap();
} catch (Exception $e) {
    Zira\Response::exception($e);
}