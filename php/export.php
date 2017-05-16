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
        $fPath = __DIR__.'/output/csv/'.$filename;
        $csv = fopen($fPath, 'w');
        fputcsv($csv, array('Band Name', 'Location', 'Start Time', 'End Time', 'Description', 'Members', 'URL', 'Comment'));

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
            $sql2 = sprintf("SELECT * from bands
                            JOIN bandstoporchfests on bands.BandID = bandstoporchfests.BandID 
                            WHERE bandstoporchfests.PorchfestID = '%s' and bandstoporchfests.TimeslotID = '%s'",
                            $_POST['porchfestid'], $timeslot['TimeslotID']);
            $result2 = $conn->query($sql2);

            while ($bandinfo = $result2->fetch_assoc()) {
                fputcsv($csv, array($bandinfo['Name'], $bandinfo['PorchLocation'], $start_time, $end_time, 
                    $bandinfo['Description'], $bandinfo['Members'], $bandinfo['URL'], $bandinfo['Comment']));
            }
        }
        fclose($csv);

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