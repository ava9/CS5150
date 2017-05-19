<?php
    require_once __DIR__."/../../config.php";
    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exportCSV'])) {
        
        require_once 'generateCSV.php';

        // Set the headers for the file download
        header("Content-Description: File Transfer");
        header('Content-Type: application/csv');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Length: ' . filesize($fPath));
        header("Cache-Control: public");
        
        readfile($fPath);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exportKML'])) {
        
        // run the kml generation
        require_once 'generateKML.php';

        //headers for download for kml
        $filename = $_POST['PORCHFEST_NICKNAME'] .'.kml';
        $fPath = __DIR__.'/output/kml/'.$filename;
        header("Content-Description: File Transfer");
        header('Content-Type: application/kml');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Length: ' . filesize($fPath));
        header("Cache-Control: public");
        
        readfile($fPath);
    }
?>