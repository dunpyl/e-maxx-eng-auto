<?php

require_once 'common.php';

$path = $_SERVER['REQUEST_URI'];

echo file_get_contents("$storage$path");
