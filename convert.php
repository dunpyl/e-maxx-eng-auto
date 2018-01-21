<?php

require_once 'common.php';
require_once 'markdown/MarkdownExtra.inc.php';

function retrieveFile($path, $conv = true, $reloadDelay = 300) {
    global $ghurl, $histPrefix, $storage;
    $file = "$storage$path";
    $filetime = file_exists($file) ? filemtime($file) : 0;
    $timeleft = time() - $filetime;

    if ($timeleft > $reloadDelay) {
        $md = $conv ? preg_replace('/\.html$/', '.md', $path) : $path;
        $json = getRequest($ghurl . $md);
        if ($json !== false) {
            $data = json_decode($json);
            if (!is_object($data) || !isset($data->name)) {
                header("HTTP/1.1 404 Not Found"); 
                echo "Object is retrieved from GitHub but could not be decoded :(\n";
                return null;
            }
        } else {
                header("HTTP/1.1 404 Not Found"); 
                echo "Source ${ghurl}${md} is not found at GitHub :(";
                return null;
        }
        $html = getRequest($data->download_url);
        if ($conv) {
            $html = convertText($html, $histPrefix . $md, $path);
        }
        storeFile($file, $html);
        $from = 'github';
    } else {
        $html = file_get_contents($file);
        $from = 'cache';
    }
    header("X-file-" . rand() . ": $path retrieved from $from, filetime=$filetime, timeleft=$timeleft");
    return $html;
}

function convertText($text, $history, $path) {
    $params = extractParams($text);
    $params['history'] = $history;
    $text = substituteParams($text, $params);
    $template = retrieveFile('/_templates/' . $params['template'] . '.html', false, 3600);
    $params['text'] = \Michelf\MarkdownExtra::defaultTransform($text);
    $html = substituteParams($template, $params);
    $html = improveAnchors($html, $path);
    return $html;
}

function extractParams(&$text) {
    global $serverUrl, $protocol;
    $vars = array(
            'template' => 'default', 'baseurl' => $serverUrl,
            'year' => date('Y'),
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

function improveAnchors($html, $path) {
    return str_replace('<a href="#', '<a href="' . $path . '#', $html);
}

