<?php

require_once 'common.php';
require_once 'convert.php';

$path = $_SERVER['REQUEST_URI'];

$text = file_get_contents("$storage$path");

$html = convertText($text);

echo $html;
