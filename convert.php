<?php

require_once 'common.php';
require_once 'markdown/MarkdownExtra.inc.php';

function convertText($text) {
    $params = extractParams($text);
    $params['text'] = \Michelf\MarkdownExtra::defaultTransform($text);
    $html = loadTemplate($params['template'], $params);
    return $html;
}

function extractParams(&$text) {
    $vars = array('template' => 'default',
            'base' => $isLocal ? 'http://localhost:8080' : 'e-maxx-eng.appspot.com');
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

function loadTemplate($name, $params) {
    $template = file_get_contents("templates/$name.html");
    foreach ($params as $name => $value) {
        $template = str_replace("&$name&", $value, $template);
    }
    return $template;
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

