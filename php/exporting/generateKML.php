<?php

$colorArray = array("icon-1899-000000", "icon-1899-006064", "icon-1899-0097A7", "icon-1899-01579B", "icon-1899-0288D1",
                    "icon-1899-0F9D58", "icon-1899-1A237E", "icon-1899-558B2F", "icon-1899-673AB7", "icon-1899-757575",
                    "icon-1899-795548", "icon-1899-7CB342", "icon-1899-817717", "icon-1899-9C27B0", 
                    "icon-1899-E65100", "icon-1899-F57C00", "icon-1899-F9A825", "icon-1899-FFD600");
shuffle($colorArray);

// DB credentials and porchfest info
require_once '../../config.php';

$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Set date timezone
date_default_timezone_set('America/New_York');

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

// set file name for output
$fileName = __DIR__.'/output/kml/'.$_POST['PORCHFEST_NICKNAME'] . '.kml';
$myfile = fopen($fileName, "w");

// file headers
fwrite($myfile, "<?xml version='1.0' encoding='UTF-8'?>\n");
fwrite($myfile, "<kml xmlns='http://www.opengis.net/kml/2.2' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:gx='http://www.google.com/kml/ext/2.2' xmlns:kml='http://www.opengis.net/kml/2.2' xmlns:xal='urn:oasis:names:tc:ciq:xsdschema:xAL:2.0'>\n");
fwrite($myfile, "\t<Document>\n");
// get required styles into kml
$style = file_get_contents("kmlReqStyle.txt");
fwrite($myfile, $style);

// fetch data from database
$sql = sprintf("SELECT bands.Name, bands.Description, bandstoporchfests.Latitude, bandstoporchfests.Longitude, porchfesttimeslots.StartTime, bandstoporchfests.Flagged 
	FROM bands INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
	INNER JOIN porchfesttimeslots ON porchfesttimeslots.TimeslotID = bandstoporchfests.TimeslotID 
	WHERE bandstoporchfests.porchfestID = '%d'
	ORDER BY porchfesttimeslots.StartTime;", $_POST['PORCHFEST_ID']);

$result = $conn->query($sql);

// only output if data exists
if ($result->num_rows > 0) {
	// output data of each row

	// init logic variables
	$prevStartTime = -1;
	$colorIndex = 0;
	$currColor = $colorArray[$colorIndex];
	$firstIter = 1;
	while($row = $result->fetch_assoc()) {

		// store data in variables
		$bandName = htmlspecialchars($row["Name"]);
		$bandDescription = htmlspecialchars($row["Description"]);
		$bandLat = $row["Latitude"];
		$bandLong = $row["Longitude"];
		$bandStartTime = $row["StartTime"];
        $flagged = $row["Flagged"];

		// parse time from this format:
		// YYYY-MM-DD HH:MM:SS
		$t = strtotime($bandStartTime);
		$hour = date('g', $t);
		$min = date('i', $t);
		$AMPM = date('A', $t);
		$currStartTime = $hour . ":" . $min . " " . $AMPM;

		// folder logic (group by the time slots)
		if ($firstIter == 1) {
			$firstIter = 0;
			fwrite($myfile, '\t\t<Folder id="' . $currStartTime . '">\n');
			fwrite($myfile, '\t\t\t<name>'. $currStartTime .'</name>\n');
		} elseif ($prevStartTime != $currStartTime) {
			fwrite($myfile, '\t\t</Folder>\n');
			fwrite($myfile, '\t\t<Folder id="' . $currStartTime . '">\n');
			fwrite($myfile, '\t\t\t<name>'. $currStartTime .'</name>\n');
			$colorIndex = ($colorIndex + 1) % count($colorArray);
			$currColor = $colorArray[$colorIndex];
		}    
		
		// create pin with color
		fwrite($myfile, "\t\t\t<Placemark>\n");
		fwrite($myfile, "\t\t\t\t<name>".$bandName."</name>\n");
		fwrite($myfile, "\t\t\t\t<description>".$bandDescription."</description>\n");
        if ($flagged == 1) {
            // color red when flag is 1
            fwrite($myfile, "\t\t\t\t<styleUrl>#icon-1899-C2185B</styleUrl>\n");
        } elseif ($flagged == 2) {
            // color yellow when flag is 2
            fwrite($myfile, "\t\t\t\t<styleUrl>#icon-1899-FFEA00</styleUrl>\n");
        } else {
        	// else, assign the color of the current time slot
            fwrite($myfile, "\t\t\t\t<styleUrl>#".$currColor."</styleUrl>\n");
        }
		fwrite($myfile, "\t\t\t\t<Point>\n");
		fwrite($myfile, "\t\t\t\t\t<coordinates>".$bandLong.",".$bandLat.",0.0</coordinates>\n");
		fwrite($myfile, "\t\t\t\t</Point>\n");
		fwrite($myfile, "\t\t\t</Placemark>\n");

		// assign the prevStartTime
		$prevStartTime = $currStartTime;
		
	}
	if ($firstIter == 0) {
		fwrite($myfile, "\t\t</Folder>\n");
	}
}
$conn->close();

fwrite($myfile, "\t</Document>\n");
fwrite($myfile, "</kml>\n");
fclose($myfile);

?>