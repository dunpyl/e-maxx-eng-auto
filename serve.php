<?php

require_once 'common.php';
require_once 'convert.php';
$injectCount = true;
require_once 'cnt.php';

$path = $_SERVER['REQUEST_URI'];

if ($customHost !== '' && strpos($serverUrl, '.appspot.') !== false) {
    $serverUrl = preg_replace('/[^\.]+\.appspot\.com/', $customHost, $serverUrl);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $protocol://$serverUrl$path");
    return;
}

if ($path == '/index.html') {
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: $serverUrl");
    return;
} else if ($path == '/') {
    $path = '/index.html';
}

$path = preg_replace('/\?.*/', '', $path);

$html = retrieveFile($path);
if ($html === null) {
    exit();
}

header("Cache-Control: max-age=3600");
if (preg_match('/.*\.html/', $path)) {
    header("Content-Type: text/html; charset=UTF-8");
}

header("X-Config: $ghProject, " . strlen($ghSecret));

if ($injectCount) {
    $html = preg_replace('/\<body/i', "<body data-v=\"$injectCount\"", $html);
}
echo $html;
