<?php
/**
 * Zira project
 * cache.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class Cache {
    const LOCK_FILE = 'lock';
    protected static $_lock_handler;

    public static function isLockFile($file) {
        return $file == '.' . self::LOCK_FILE . '.cache';
    }

    public static function set($key, $data, $serialize=false) {
        if (!Config::get('caching')) return false;
        if (!self::lock(true)) return false;

        if ($serialize) {
            $data = serialize($data);
        }

        $cache_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                        CACHE_DIR . DIRECTORY_SEPARATOR .
                        '.' . $key . '.cache';

        if (file_exists($cache_file)) @chmod($cache_file, 0660);

        $f=@fopen($cache_file,'wb');
        if (!$f) {
            self::unlock();
            return false;
        }
        fwrite($f, $data);
        fclose($f);

        //@chmod($cache_file, 0000);

        self::unlock();

        return true;
    }

    public static function setArray($key, $data) {
        return self::set($key, $data, true);
    }

    public static function setObject($key, $data) {
        return self::set($key, $data, true);
    }

    protected static function isExpired($cache_file) {
        $mtime = filemtime($cache_file);
        if (!$mtime) return true;
        return (time()-$mtime>Config::get('cache_lifetime'));
    }

    public static function get($key, $unserialize = false) {
        if (!Config::get('caching')) return false;
        if (!self::lock()) return false;

        $cache_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                        CACHE_DIR . DIRECTORY_SEPARATOR .
                        '.' . $key . '.cache';

        if (!file_exists($cache_file) || 
            self::isExpired($cache_file) 
        ) {
            self::unlock();
            return false;
        }

        @chmod($cache_file, 0440);
        $data = @file_get_contents($cache_file);
        //@chmod($cache_file, 0000);

        if (empty($data)) {
            self::unlock();
            return false;
        }

        if ($unserialize) {
            $data = unserialize($data);
        }

        self::unlock();

        return $data;
    }

    public static function lock($write=false,$block=false) {
        $cache_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                CACHE_DIR . DIRECTORY_SEPARATOR .
                '.' . self::LOCK_FILE . '.cache';

        self::$_lock_handler=@fopen($cache_file,'wb');
        if (!self::$_lock_handler) return false;

        if (!$block) {
            $lock = $write ? (LOCK_EX | LOCK_NB) : (LOCK_SH | LOCK_NB);
        } else {
            $lock = $write ? LOCK_EX : LOCK_SH;
        }

        return flock(self::$_lock_handler, $lock);
    }

    public static function unlock() {
        $cache_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                CACHE_DIR . DIRECTORY_SEPARATOR .
                '.' . self::LOCK_FILE . '.cache';

        if (!file_exists($cache_file)) return false;
        if (!self::$_lock_handler) return false;

        $result = flock(self::$_lock_handler, LOCK_UN);

        fclose(self::$_lock_handler);

        return $result;
    }

    public static function getArray($key) {
        return self::get($key, true);
    }

    public static function getObject($key) {
        return self::get($key, true);
    }

    public static function clear($force=false) {
        if (!Config::get('caching') && !$force) return false;
        if (!self::lock(true, true)) return false;
        if ($force && !Assets::lock(true, true)) {
            self::unlock();
            return false;
        } 
        $d = @opendir(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR);
        if (!$d) {
            self::unlock();
            if ($force) Assets::unlock();
            return false;
        }
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || !is_file(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR . DIRECTORY_SEPARATOR . $f)) continue;
            if (substr($f,-6)!='.cache') continue;
            if (self::isLockFile($f) || Assets::isAssetsLockFile($f)) continue;
            if (!$force && Assets::isAssetsCacheFile($f)) continue;
            @chmod(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR . DIRECTORY_SEPARATOR . $f, 0660);
            @unlink(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR . DIRECTORY_SEPARATOR . $f);
        }
        closedir($d);
        if (Config::get('caching') && $force) {
            Assets::merge(false);
            Assets::mergeCSSContent(false);
            Assets::mergeJSContent(false);
        }
        self::unlock();
        if ($force) Assets::unlock();
        return true;
    }
}