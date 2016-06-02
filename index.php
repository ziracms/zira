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
    if (Zira\Request::isInstallRequestUri()) header('Location: index.php');
    else if (Zira\Request::isBaseRequestUri()) header('Location: install/index.php');
    exit;
}

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
    Zira::getInstance()->bootstrap();
} catch (Exception $e) {
    Zira\Response::exception($e);
}