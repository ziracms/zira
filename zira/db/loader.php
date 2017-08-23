<?php
/**
 * Zira project.
 * loader.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db;

class Loader {
    protected static $_classes = array(
        'db',
        'field',
        'table',
        'orm',
        'collection',
        'alter'
    );

    public static function initialize() {
        if (strpos(DB_TYPE,'.')!==false) throw new \Exception('Invalid database type');
        $db_type = trim(strtolower(DB_TYPE));
        $root = ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR;
        foreach(self::$_classes as $class) {
            $path = $root . $db_type . '.' . $class . '.php';
            if (!file_exists($path) || !is_file($path) || !is_readable($path)) {
                throw new \Exception('Failed to load database class');
            }
            require_once $path;
            $class_name = __NAMESPACE__.'\\'.ucfirst($class);
            if (!class_exists($class_name, false)) {
                throw new \Exception('Database class not found');
            }
        }
    }
}