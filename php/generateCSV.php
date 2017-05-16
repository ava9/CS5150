<?php
    require_once '../config.php';
    // create file for new porchfest csv
    $filename = $_POST['PORCHFEST_NICKNAME'] . ".csv";
    $fPath = __DIR__ . '/output/csv/' . $filename;
    $csv = fopen($fPath, 'w');

    // add header to csv
    fputcsv($csv, array('Band Name', 'Location', 'Start Time', 'End Time', 'Description', 'Members', 'URL', 'Comment'));

    // fetch porchfest info from database
    $sql = sprintf("SELECT * 
            FROM bands INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
            INNER JOIN porchfesttimeslots ON porchfesttimeslots.TimeslotID = bandstoporchfests.TimeslotID 
            WHERE bandstoporchfests.porchfestID = '%d'
            ORDER BY porchfesttimeslots.StartTime;", $_POST['PORCHFEST_ID']);        
    $result = $conn->query($sql);

    // print all data into the csv
    while ($row = $result->fetch_assoc()) {
        // parse the dates from the database
        $start_time = date_create_from_format("Y-m-d H:i:s", $row['StartTime']); 
        $end_time = date_create_from_format("Y-m-d H:i:s", $row['EndTime']); 
        $start_time = $start_time->format('h:i A');
        $end_time = $end_time->format('h:i A');

        // put in csv
        fputcsv($csv, array($row['Name'], $row['PorchLocation'], $start_time, $end_time, 
            $row['Description'], $row['Members'], $row['URL'], $row['Comment']));
    }
    fclose($csv);
?>