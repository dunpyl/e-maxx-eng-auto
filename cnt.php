<?php

//error_reporting(E_ALL);
ini_set('display_errors', 0);

function counterHash($s) {
    $n = strlen($s);
    $res = 0;
    for ($i = 0; $i < $n; $i++) {
        $res += ord($s[$i]);
        $res <<= 1;
        $res = ($res % 0x10000) ^ ($res >> 16);
    }
    return $res;
}

function counterIncr($memcache, $key) {
    $v = $memcache->get($key);
    $v += 1;
    $memcache->set($key, $v, 0, 3*24*60*60);
}

function counterCount() {
    $N = 16;

    try {
        $data = explode('.', $_GET['d']);
        $hash = $data[1];
        $data = $data[0];
        if ($hash != counterHash($data)) {
            return;
        }
        $data = json_decode(base64_decode($data));
    } catch (Exception $e) {
        return;
    }

    $key = date('Ymd') . '-' . ($hash % $N);

    $memcache = new Memcached;
    counterIncr($memcache, $key);
    if (isset($data->isNew) && $data->isNew) {
        counterIncr($memcache, $key . '.u');
    }
}

function counterReportFor($memcache, $day, $sfx) {
    $N = 16;
    $k = date('Ymd', time() - $day * 24*60*60) . '-';
    $s = $memcache->get($k . $sfx);
    if ($s === FALSE) {
        $s = 0;
        for ($i = 0; $i < $N; $i++) {
            $s += $memcache->get($k . $i . $sfx);
        }
        $memcache->set($k . $sfx, $s, 0, 3*24*60*60);
    }
    return $s;
}

function counterReport() {
    $memcache = new Memcached;
    $p = counterReportFor($memcache, 1, '');
    $u = counterReportFor($memcache, 1, '.u');
    header("X-count: $p/$u");
}

if (!isset($injectCount)) {
    counterCount();
} else {
    counterReport();
}

