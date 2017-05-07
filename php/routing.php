<?php
    require_once("config.php");
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

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
    if (in_array($uri_array[0], ['view', 'edit', 'bandsignup'])) {
        $sql = sprintf("SELECT PorchfestID, Name from porchfests WHERE Nickname = '%s'", $uri_array[1]);
        $result = $mysqli->query($sql)->fetch_assoc();
        $name = $result['Name'];
        $porchfestID = $result['PorchfestID'];

        define('PORCHFEST_NICKNAME', $uri_array[1]);
        define('PORCHFEST_NAME', $name);
        define('PORCHFEST_ID', $porchfestID);
    }
    if (isset($uri_array[2])) {
        define('BAND_NAME', preg_replace('/-{2}/', '-', preg_replace('/([^-])-{1}([^-])/', '\1 \2', $uri_array[2])));
    }
?>

