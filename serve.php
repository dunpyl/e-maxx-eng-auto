<?php

require_once 'common.php';
require_once 'convert.php';

$path = $_SERVER['REQUEST_URI'];

if ($path == '/index.html') {
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: $serverUrl");
    return;
} else if ($path == '/') {
    $path = '/index.html';
}

$path = preg_replace('/\?.*/', '', $path);

$file = "$storage$path";

$filetime = file_exists($file) ? filemtime($file) : 0;
$timeleft = time() - $filetime;

if ($timeleft > 300) {
    $md = preg_replace('/\.html$/', '.md', $path);
    $history = $histPrefix . $md;
    $json = getRequest($ghurl . $md);
    if ($json !== false) {
        $data = json_decode($json);
        if (!is_object($data) || !isset($data->name)) {
            header("HTTP/1.1 404 Not Found"); 
            echo "Object is retrieved from GitHub but could not be decoded :(\n";
            return;
        }
    } else {
            header("HTTP/1.1 404 Not Found"); 
            echo "Source is not found at GitHub :(";
            return;
    }
    $binary = getRequest($data->download_url);
    $html = convertText($binary, $history);
    storeFile($file, $html);
} else {
    $html = file_get_contents($file);
}

header("Cache-Control: max-age=3600");
header("X-File-Time: $filetime $timeleft");

echo $html;

