<?php
    function getCurrentUri() {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    $base_url = getCurrentUri();
    // echo sprintf("Current URI: %s \n", $base_url);
    $uri_array = array_values(array_filter(explode('/', $base_url)));
    // foreach ($uri_array as $key => $value) {
    //     $uri_array[$key] = ucwords(urldecode($value));
    // }
    if (in_array($uri_array[0], ['singleporchfest', 'editporchfest', 'bandsignup', 'editband'])) {
        define('PORCHFEST_NAME_RAW', $uri_array[1]);
        define('PORCHFEST_NAME_CLEAN', ucwords(urldecode($uri_array[1])));
    }
?>

