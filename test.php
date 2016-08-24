<?php

//require_once dirname(__FILE__) . '/markdown/MarkdownExtra.inc.php';

$url = 'https://api.github.com/repos/e-maxx-eng/e-maxx-eng/contents/src';

$isLocal = (strpos(getenv('SERVER_SOFTWARE'), 'Development') !== false);
$storage = $isLocal ? './.data' : 'gs:.../';

$path = $_GET['path'];
$doUpdate = $_GET['update'];

if (empty($path)) {
    echo file_get_contents('./testform.php');
    return;
}

$file = getRequest("$url/$path");
if ($file !== false) {
    $file = json_decode($file);
    echo "{$file->size} {$file->sha}\n";
    if (!empty($doUpdate)) {
        $data = getRequest($file->download_url);
        file_put_contents($path, $data);
        echo "Updated!\n";
    }
} else {
    echo "File not found!";
}

function getRequest($url) {
    $opts = ['http' => ['method' => 'GET', 'header' => ['User-Agent: PHP']],
        "ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
    $context = stream_context_create($opts);
    return @file_get_contents($url, false, $context);
}

