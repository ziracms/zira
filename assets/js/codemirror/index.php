<?php
/**
 * Zira project.
 * codemirror.gzip.php
 * (c)2016 http://dro1d.ru
 */

error_reporting(0);

require('../../../const.php');

$files = array(
    'codemirror.js',
    'javascript.js',
    'css.js',
    'xml.js',
    'htmlmixed.js',
    'simplescrollbars.js'
);

$output = '';

foreach ($files as $file) {
    if (!file_exists($file)) exit('File not found');
    if (!is_readable($file)) exit('File is not readable');
    $output .=  '// ' . $file . "\r\n\r\n" . file_get_contents($file) . "\r\n\r\n";
}

header_remove('X-Powered-By');
header_remove('Pragma');
header_remove('Set-Cookie');
header("Content-Type: text/javascript; charset=utf-8");
header('Cache-Control: public');
header("Expires: ".date('r',time()+3600*24));
header('HTTP/1.1 200 OK');

$accept_encoding = '';
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match( '/\b(x-gzip|gzip)\b/', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), $match)) {
    $accept_encoding = $match[1];
}
if (empty($accept_encoding) && defined('FORCE_GZIP_ASSETS') && FORCE_GZIP_ASSETS) $accept_encoding = 'gzip';
if (function_exists('gzencode') && !@ini_get('zlib.output_compression') && !empty($accept_encoding)) {
    header("Vary: Accept-Encoding");
    header("Content-Encoding: " . $accept_encoding);

    $output = gzencode($output, 9, FORCE_GZIP);
}

echo $output;