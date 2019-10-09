<?php
/**
 * Zira project
 * log.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

class Log {
    protected static $_file;

    /**
     * Preload class to log shutdown errors and set log file
     */
    public static function init() {
        self::$_file = '.'.date('Y-m-d'). '.log';
    }

    public static function exception(\Exception $e) {
        if (LOG_ERRORS) {
            self::write($e->getCode().': '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        }
    }

    public static function write($str) {
        if (!LOG_ERRORS || !self::$_file) return false;

        $log_file = REAL_PATH . DIRECTORY_SEPARATOR .
            LOG_DIR . DIRECTORY_SEPARATOR .
            self::$_file;

        $f=@fopen($log_file,'ab');
        if (!$f) return false;
        fwrite($f, '['.date('Y-m-d H:i:s').'] URI='.Request::uri()."\r\n".$str."\r\n\r\n");
        fclose($f);

        return true;
    }

    public static function getErrorType($type)
    {
        switch($type)
        {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }
        return '';
    }
}