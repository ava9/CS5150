<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    require_once __DIR__.'/../config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Return the URI of the current page (everything past the domain) 
    // Example: porchfest.life/a/b/c -> a/b/c
    function getCurrentUri() {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    $base_url = getCurrentUri();
    $uri_array = array_values(array_filter(explode('/', $base_url)));
    // Set useful variables based off the url fields
    if (in_array($uri_array[0], ['view', 'edit', 'bandsignup'])) {
        $sql = sprintf("SELECT PorchfestID, Name from porchfests WHERE Nickname = '%s'", $uri_array[1]);
        $result = $mysqli->query($sql);
        if ($result->num_rows != 1) {
            die("ERROR, NOT A VALID PORCHFEST");
        }
        $result = $result->fetch_assoc();

        define('PORCHFEST_NICKNAME', $uri_array[1]);
        define('PORCHFEST_NAME', $result['Name']);
        define('PORCHFEST_ID', $result['PorchfestID']);
    }
    // Set the band name, if in editband.php, to BAND_NAME variable.
    // Involves decoding urlstring, then replacing "--" with "-" and "-" with "spaces"
    if (isset($uri_array[2])) {
        define('BAND_NAME', 
            preg_replace('/-{2}/', '-', 
                preg_replace('/(?<!-)-(?!-)/', '\1 \2', urldecode($uri_array[2]))));
    }

    // For dashboard, you just have to be logged in
    if ($uri_array[0] == 'dashboard') {
        if (!isset($_SESSION['logged_user'])) {
            header('HTTP/1.0 403 Forbidden');
            die('FORBIDDEN');
        }
    }

    // Forbid the page if it is not able to be accessed by the user
    if (in_array($uri_array[0], ['edit'])) {
        // Simple case, if user is not logged in, then these pages should not be accessed
        if (!isset($_SESSION['logged_user'])) {
            header('HTTP/1.0 403 Forbidden');
            die('FORBIDDEN');
        }
        // For editporchfest, the userID must be an organizer for the porchfest
        $sql = sprintf("SELECT * from porchfests 
                        INNER JOIN userstoporchfests ON userstoporchfests.UserID = '%s'
                        WHERE porchfests.Nickname = '%s'", $_SESSION['logged_user'], PORCHFEST_NICKNAME);
        $result = $mysqli->query($sql);
        $accepted_users = $result->num_rows;
        // For editband, the userID is either the organizer or a member of the band
        if (isset($uri_array[2])) {
            $sql = sprintf("SELECT * FROM bands
                          INNER JOIN userstobands ON userstobands.UserID = '%s'
                          AND userstobands.BandID = bands.BandID
                          WHERE bands.Name = '%s'", $_SESSION['logged_user'], BAND_NAME);
            $result = $mysqli->query($sql);
            $accepted_users = $accepted_users + $result->num_rows;
        }
        if ($accepted_users == 0) {
            header('HTTP/1.0 403 Forbidden');
            die('FORBIDDEN');
        }
    }
?>
