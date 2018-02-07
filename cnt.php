<?php

echo rand();

$memcache = new Memcached;
$memcache->set('bla', "t:" . time(), 0, 600);
