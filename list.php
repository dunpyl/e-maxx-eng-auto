<?php

$url = 'https://api.github.com/repos/e-maxx-eng/e-maxx-eng/contents/src';

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


