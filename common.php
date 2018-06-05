<?php

use google\appengine\api\app_identity\AppIdentityService;

$appId = AppIdentityService::getApplicationId();

$isLocal = (strpos(getenv('SERVER_SOFTWARE'), 'Development') !== false);
$gsprefix = "gs://$appId.appspot.com";
list($ghProject, $ghSecret, $customHost) = ghclient();
$ghurl = "https://api.github.com/repos/$ghProject/contents/src";
$ghRawContentUrl = "https://raw.githubusercontent.com/$ghProject/master/img";
$histPrefix = "https://github.com/$ghProject/commits/master/src";
$storage = $isLocal ? './.data' : "$gsprefix/data";
$protocol = 'https';
$serverUrl = ($isLocal ? 'http' : $protocol) . '://' . $_SERVER['HTTP_HOST'];

function ghclient() {
    global $gsprefix;
    $prj = 'e-maxx-eng/e-maxx-eng';
    $secret = '';
    $host = '';
    $content = @file_get_contents("$gsprefix/gh-client.txt");
    if (!empty($content)) {
        $content = preg_split('/\s+/', trim($content));
        if (count($content) > 0) {
            $secret = $content[0];
            if (count($content) > 1) {
                $prj = $content[1];
            }
            if (count($content) > 2) {
                $host = $content[2];
            }
        }
    }
    return array($prj, $secret, $host);
}

function getRequest($url) {
    global $ghSecret;
    $opts = ['http' => ['method' => 'GET', 'header' => 'User-Agent: PHP'],
        "ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
    $context = stream_context_create($opts);
    $url = $url . $ghSecret;
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

