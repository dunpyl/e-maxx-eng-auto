<?php

require_once dirname(__FILE__) . '/markdown/MarkdownExtra.inc.php';

$url = 'https://api.github.com/repos/e-maxx-eng/e-maxx-eng/contents/src';

function getRequest($url) {
    $opts = ['http' => ['method' => 'GET', 'header' => ['User-Agent: PHP']],
        "ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
    $context = stream_context_create($opts);
    return @file_get_contents($url, false, $context);
}

function listDir($path) {
    global $url;
    $res = array();
    $list = json_decode(getRequest($url . $path));
    foreach ($list as $entry) {
        $name = $entry->name;
        $full = "$path/$name";
        switch ($entry->type) {
            case 'file':
                $res[] = [$full, $entry->size, $entry->sha];
                break;
            case 'dir':
                $subRes = listDir($full);
                $res = array_merge($res, $subRes);
                break;
            default:
                // just skip
        }
    }
    return $res;
}

$isLocal = (strpos(getenv('SERVER_SOFTWARE'), 'Development') !== false);

$path = $_GET['path'];
$file = getRequest("$url/$path");
if ($file !== false) {
    $file = json_decode($file);
    echo $file->size . " " . $file->sha;
} else {
    echo "File not found!";
}

