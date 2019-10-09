<?php
/**
 * Zira project.
 * index.php
 * (c)2016 https://github.com/ziracms/zira
 */

error_reporting(0);

require('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'const.php');

const ASSETS_ROOT = 'cache';
const ASSETS_CACHE_FILE = '.js.cache';
const CONTENT_CACHE_FILE = '.js.content.cache';
const ASSETS_GZIP_CACHE_FILE = '.js.gz.cache';
const LOCK_FILE = '.assetlock.cache';

$etag = isset($_GET['t']) ? intval($_GET['t']) : 0;
$gzip = intval(substr($etag,0,1))>1;
$assets_root = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . ASSETS_ROOT;
$path =  $assets_root . DIRECTORY_SEPARATOR . ASSETS_CACHE_FILE;
$content_path =  $assets_root . DIRECTORY_SEPARATOR . CONTENT_CACHE_FILE;
$gz_path = $assets_root . DIRECTORY_SEPARATOR . ASSETS_GZIP_CACHE_FILE;
$lock = $assets_root . DIRECTORY_SEPARATOR . LOCK_FILE;

$noCache = false;
$lock_handler=@fopen($lock,'wb');
if (!$lock_handler || !flock($lock_handler, LOCK_SH)) {
    $noCache = true;
}

if (!file_exists($path) || !is_readable($path)) {
    flock($lock_handler, LOCK_UN);
    fclose($lock_handler);
    exit('File not found');
}

if (!$noCache) {
    header_remove('X-Powered-By');
    header_remove('Pragma');
    header_remove('Set-Cookie');
    header("Content-Type: text/javascript; charset=utf-8");
    header('Cache-Control: public');
    header("Expires: ".date('r',time()+3600*24*30));
}

if (!empty($etag)) header('ETag: '.$etag);
if (empty($etag) || !isset($_SERVER['HTTP_IF_NONE_MATCH']) || $etag!=$_SERVER['HTTP_IF_NONE_MATCH']) {
    header('HTTP/1.1 200 OK');

    $accept_encoding = '';
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match( '/\b(x-gzip|gzip)\b/', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), $match)) {
        $accept_encoding = $match[1];
    }
    if (empty($accept_encoding) && defined('FORCE_GZIP_ASSETS') && FORCE_GZIP_ASSETS) $accept_encoding = 'gzip';
    if ($gzip && function_exists('gzencode') && !@ini_get('zlib.output_compression') && !empty($accept_encoding)) {
        header("Vary: Accept-Encoding");
        header("Content-Encoding: " . $accept_encoding);

        if (file_exists($gz_path) && is_readable($gz_path) && filesize($gz_path)>0 && filemtime($gz_path)==filemtime($path)) {
            $output = file_get_contents($gz_path);
        } else {
            $output = file_get_contents($path);
            if (file_exists($content_path) && is_readable($content_path)) {
                $output .= file_get_contents($content_path);
            }
            $output = gzencode($output, 9, FORCE_GZIP);

            if (is_writable($assets_root) && ($f=fopen($gz_path,'wb'))!==false) {
                fwrite($f, $output);
                fclose($f);
                touch($gz_path, filemtime($path));
            }
        }
    } else {
        $output = file_get_contents($path);
        if (file_exists($content_path) && is_readable($content_path)) {
            $output .= file_get_contents($content_path);
        }
    }

    echo $output;
} else {
    header('HTTP/1.1 304 Not Modified');
}

flock($lock_handler, LOCK_UN);
fclose($lock_handler);