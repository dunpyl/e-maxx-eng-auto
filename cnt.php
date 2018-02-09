<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 0);

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
    $v = $memcache->get($key);
    $v += 1;
    $memcache->set($key, $v, 0, 3*24*60*60);
}

function counterReport() {
    $N = 16;
    $memcache = new Memcached;
    $k = date('Ymd', time() - 24*60*60) . '-';
    $s = $memcache->get($k);
    if ($s === FALSE) {
        $s = 0;
        for ($i = 0; $i < $N; $i++) {
            $s += $memcache->get($k . $i);
        }
        $memcache->set($k, $s, 0, 3*24*60*60);
    }
    header("X-count: $s");
}

if (!isset($injectCount)) {
    counterCount();
} else {
    counterReport();
}

