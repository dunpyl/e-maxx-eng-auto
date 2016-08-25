<?php

$ghurl = 'https://api.github.com/repos/e-maxx-eng/e-maxx-eng/contents/src';

$isLocal = (strpos(getenv('SERVER_SOFTWARE'), 'Development') !== false);
$gsprefix = 'gs://e-maxx-eng.appspot.com';
$storage = $isLocal ? './.data' : "$gsprefix/data";
$serverUrl = $isLocal ? 'http://' . $_SERVER['HTTP_HOST'] : 'http://e-maxx-eng.appspot.com';

function ghclient() {
    global $gsprefix;
    $ghclient = @file_get_contents("$gsprefix/gh-client.txt");
    if (empty($ghclient)) {
        $ghclient = '';
    }
    return $ghclient;
}

function getRequest($url) {
    $opts = ['http' => ['method' => 'GET', 'header' => 'User-Agent: PHP'],
        "ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
    $context = stream_context_create($opts);
    $url = $url . ghclient();
    return @file_get_contents($url, false, $context);
}

function storeFile($path, $data) {
    $i = strrpos($path, '/');
    $dir = substr($path, 0, $i);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, $data);
}
