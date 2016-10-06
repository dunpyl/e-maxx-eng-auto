<?php

require_once 'common.php';
require_once 'markdown/MarkdownExtra.inc.php';

function convertText($text, $history) {
    $params = extractParams($text);
    $params['history'] = $history;
    $text = substituteParams($text, $params);
    $template = file_get_contents('templates/' . $params['template'] . '.html');
    $params['text'] = \Michelf\MarkdownExtra::defaultTransform($text);
    $html = substituteParams($template, $params);
    return $html;
}

function extractParams(&$text) {
    global $serverUrl, $protocol;
    $vars = array(
            'template' => 'default', 'baseurl' => $serverUrl,
            'protocol' => $protocol, 'imgroot' => 'https://raw.githubusercontent.com/e-maxx-eng/e-maxx-eng/master/img');
    $lines = explode("\n", $text);
    $res = array();
    foreach ($lines as $line) {
        $matches = array();
        if (preg_match('/^\s*\<\!\-\-\?([a-z]+)\s+(.*)\-\-\>\s*$/', $line, $matches)) {
            $vars[$matches[1]] = $matches[2];
            continue;
        }
        $res[] = $line;
    }
    $text = implode("\n", $res);
    return $vars;
}

function substituteParams($text, $params) {
    foreach ($params as $name => $value) {
        $text = str_replace("&$name&", $value, $text);
    }
    return $text;
}

function rootPath() {
    global $argv;
    if (count($argv) < 2) {
        $path = realpath('target');
    } else {
        $path = $argv[1];
    }
    if ($path[1] == ':') { // windows path with drive letter
        $path = '/' . str_replace('\\', '/', $path);
    }
    if (substr($path, 0, 5) != 'http:') {
        $path = 'file://' . $path;
    }
    return "$path";
}

