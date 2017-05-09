<?php
    require_once __DIR__."/../config.php";
    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exportCSV'])) {
        // Get the name of the porchfest using ID for the file name
        // Create the CSV file with the heading
        // 'Band Name, Location, Start Time, End Time' 
        $sql = sprintf("SELECT Nickname FROM porchfests WHERE PorchfestID = '%s'", $_POST['porchfestid']);
        $porchfestName = $conn->query($sql)->fetch_assoc()['Nickname'];

        $filename = sprintf("%s_schedule.csv", $porchfestName);
        $csv = fopen($filename, 'w');
        fputcsv($csv, array('Band Name, Location, Start Time, End Time'));

        // Get all timeslots for the requested porchfest
        // Store in array format [ID -> start/end time]
        $sql = sprintf("SELECT * FROM porchfesttimeslots WHERE PorchfestID = '%s' ORDER BY StartTime", $_POST['porchfestid']);
        $result = $conn->query($sql);
        while ($timeslot = $result->fetch_assoc()) {
            // Extract the start and end time in the format hour:minute
            $start_time = date_create_from_format("Y-m-d H:i:s", $timeslot['StartTime']); 
            $end_time = date_create_from_format("Y-m-d H:i:s", $timeslot['EndTime']); 
            $start_time = $start_time->format('h:i A');
            $end_time = $end_time->format('h:i A');
            
            // For each timeslotID, pull all bands in that timeslot from the porchfest
            // Write the band name, location, starttime, and endtime to the specified format, then export it
            $sql2 = sprintf("SELECT bands.Name, bandstoporchfests.PorchLocation from bands
                            JOIN bandstoporchfests on bands.BandID = bandstoporchfests.BandID 
                            WHERE bandstoporchfests.PorchfestID = '%s' and bandstoporchfests.TimeslotID = '%s'",
                            $_POST['porchfestid'], $timeslot['TimeslotID']);
            $result2 = $conn->query($sql2);

            while ($bandinfo = $result2->fetch_assoc()) {
                fputcsv($csv, array($bandinfo['Name'], $bandinfo['PorchLocation'], $start_time, $end_time));
            }
        }
        fclose($csv);

        // Set the headers for the file download
        header("Content-Description: File Transfer");
        header('Content-Type: application/csv');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Length: ' . filesize($filename));
        header("Cache-Control: public");
        
        readfile($filename);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exportKML'])) {
        

        $filename = $_POST['PORCHFEST_NICKNAME'] .'.kml';
        header("Content-Description: File Transfer");
        header('Content-Type: application/kml');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Length: ' . filesize($filename));
        header("Cache-Control: public");
        require_once 'generateKML.php';
        
        readfile($filename);
    }
?>