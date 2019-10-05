<?php

$constant['API_KEY'] = 'yourapi';
$constant['API_SECRET'] = 'yoursecret';
$constant['IMAGEBAM_CACHE_PATH'] = __DIR__ . '/../implementation/cache/imagebam.dat';

foreach($constant as $key => $val) {
    if( !defined($key) ) {
        define($key, $val);
    }
}