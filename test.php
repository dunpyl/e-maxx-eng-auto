<?php

require_once 'common.php';

$path = $_GET['path'];
$doUpdate = $_GET['update'];

if (empty($path)) {
    echo file_get_contents('./testform.php');
    return;
}

$json = getRequest("$url/$path");
if ($json !== false) {
    $data = json_decode($json);
    if (!is_object($data) || !isset($data->name)) {
        echo "but could not be decoded :(\n$json";
        return;
    }
    echo "{$data->size} {$data->sha}\n";
    if (!empty($doUpdate)) {
        $binary = getRequest($data->download_url);
        storeFile("$storage/$path", $binary);
        echo "Updated!\n";
    }
} else {
    echo "File not found: $url/$path\n";
}


