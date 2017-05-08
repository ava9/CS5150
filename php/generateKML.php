<?php

// NEED MYSQL AND APACHE TO BE RUNNING

// input a string: address (i.e. "114 Summit Ave. Ithaca, NY 14850"
// output is a latitude, longitude coordinate pair (i.e. 42.442064,-76.483469)
function getCoordinates($address){
	
	// replace white space with "+" sign (match google search pattern)
	$address = str_replace(" ", "+", $address); 
	
	$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
	 
	$response = file_get_contents($url);
	 
	$json = json_decode($response,TRUE); //array object from the web response
	 
	return ($json['results'][0]['geometry']['location']['lng'].",".$json['results'][0]['geometry']['location']['lat'].",0.0");

}

function printStyle($colorArr, $i){
	// TODO print style
	$s = '"' .$i . '"';
	$i = $i - 1;
	$i = $i % 18;
//	fwrite($myfile, "\t\t\t<Style id=".$s.">\n");
//	fwrite($myfile, "\t\t\t\t<LabelStyle>\n");
//	fwrite($myfile, "\t\t\t\t\t<color>".$colorArr[$i]."</color>\n");
//	fwrite($myfile, "\t\t\t\t\t<colorMode>normal</colorMode>\n");
//	fwrite($myfile, "\t\t\t\t\t<scale>1</scale>\n");
//	fwrite($myfile, "\t\t\t\t</LabelStyle>\n");
//	fwrite($myfile, "\t\t\t</Style>\n");
}

$colorArray = array("icon-1899-000000", "icon-1899-006064", "icon-1899-0097A7", "icon-1899-01579B", "icon-1899-0288D1",
                    "icon-1899-0F9D58", "icon-1899-1A237E", "icon-1899-558B2F", "icon-1899-673AB7", "icon-1899-757575",
                    "icon-1899-795548", "icon-1899-7CB342", "icon-1899-817717", "icon-1899-9C27B0", //"icon-1899-C2185B",
                    "icon-1899-E65100", "icon-1899-F57C00", "icon-1899-F9A825", "icon-1899-FFD600"); //, "icon-1899-FFEA00");
shuffle($colorArray);

// Database credentials
require_once "config.php";
require_once 'routing.php';

// Set date timezone
date_default_timezone_set('America/New_York');
// Create connection
// add DB_USER and DB_PASSWORD later
$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

$fileName = $_POST['PORCHFEST_NICKNAME'] . '.kml';

$myfile = fopen($fileName, "w");
fwrite($myfile, "<?xml version='1.0' encoding='UTF-8'?>\n");
fwrite($myfile, "<kml xmlns='http://www.opengis.net/kml/2.2' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:gx='http://www.google.com/kml/ext/2.2' xmlns:kml='http://www.opengis.net/kml/2.2' xmlns:xal='urn:oasis:names:tc:ciq:xsdschema:xAL:2.0'>\n");
fwrite($myfile, "\t<Document>\n");
$style = file_get_contents("kmlReqStyle.txt");
fwrite($myfile, $style);

$sql = sprintf("SELECT bands.Name, bands.Description, bandstoporchfests.Latitude, bandstoporchfests.Longitude, porchfesttimeslots.StartTime, bands.BandID, bandstoporchfests.TimeslotID, bandstoporchfests.Flagged 
	FROM bands INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
	INNER JOIN porchfesttimeslots ON porchfesttimeslots.TimeslotID = bandstoporchfests.TimeslotID 
	WHERE bandstoporchfests.porchfestID = '%d'
	ORDER BY porchfesttimeslots.StartTime;", $_POST['porchfestid']);

$result = $conn->query($sql);
$colorIndex;
$count = 0;
$prev;
$curr;
$internalCount = 1;
$flipBit = 1;
$open = 0;
$colorIndex = 0;

if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		$count = $count + 1;

		$bandId = $row["BandID"];
		$bandName = htmlspecialchars($row["Name"]);
		$bandDescription = htmlspecialchars($row["Description"]);
		$bandLat = $row["Latitude"];
		$bandLong = $row["Longitude"];
		$bandTimeSlotID = $row["TimeslotID"];
		$bandStartTime = $row["StartTime"];
                $flagged = $row["Flagged"];

		// now you have the band name, decription, location, and start time
		// $bandName, $bandDescription, $bandLocation, $bandStartTime
		//$bandLocation = getCoordinates($bandLocation);
		// YYYY-MM-DD HH:MM:SS
		$t = strtotime($bandStartTime);
		$hour = date('g', $t);
		$min = date('i', $t);
		$a = date('A', $t);
		$time = $hour . ":" . $min . " " . $a;
		
		if ($count == 1){
			$curr = $time;
			$prev = -1;
		}
		else{
			$prev = $curr;
			$curr = $time;
		}
		
		if ($prev == $curr){
			$internalCount = $internalCount + 1;
			$flipBit = 0;
		}
		else{
			$flipBit = 1;
			$internalCount = 1;
			$colorIndex = $colorIndex + 1;
			$colorIndex = $colorIndex % 18;
		}
		
		if (($flipBit == 0) && ($internalCount == 1)){
			$s1 = '"' .$curr . '"';
			fwrite($myfile, "\t\t<Folder id=" . $s1 . ">\n");
			printStyle($colorArray, $colorIndex);
			fwrite($myfile, "\t\t\t<name>". $curr ."</name>\n");
			$open = $open + 1;
		}
		
		if (($flipBit == 1) && ($internalCount == 1)){
			if ($open == 1){
				fwrite($myfile, "\t\t</Folder>\n");
				$open = $open - 1;
			}
			$s2 = '"' .$curr . '"';
			fwrite($myfile, "\t\t<Folder id=" . $s2 . ">\n");
			printStyle($colorArray, $colorIndex);
			fwrite($myfile, "\t\t\t<name>". $curr ."</name>\n");
			$open = $open + 1;
		}
                
                
		
		fwrite($myfile, "\t\t\t<Placemark>\n");
		fwrite($myfile, "\t\t\t\t<name>".$bandName."</name>\n");
		fwrite($myfile, "\t\t\t\t<description>".$bandDescription."</description>\n");
                if ($flagged == 1) {
                    // color red icon-1899-C2185B
                    fwrite($myfile, "\t\t\t\t<styleUrl>#icon-1899-C2185B</styleUrl>\n");
                } elseif ($flagged == 2) {
                    // color yellow icon-1899-FFEA00
                    fwrite($myfile, "\t\t\t\t<styleUrl>#icon-1899-FFEA00</styleUrl>\n");
                } else {
                    fwrite($myfile, "\t\t\t\t<styleUrl>#".$colorArray[$colorIndex]."</styleUrl>\n");
                }
		fwrite($myfile, "\t\t\t\t<Point>\n");
		fwrite($myfile, "\t\t\t\t\t<coordinates>".$bandLong.",".$bandLat.",0.0</coordinates>\n");
		fwrite($myfile, "\t\t\t\t</Point>\n");
		fwrite($myfile, "\t\t\t</Placemark>\n");
		
	}
} else {
	echo "0 results";
}
$conn->close();

if ($open == 1){
	fwrite($myfile, "\t\t</Folder>\n");
	$open = $open - 1;
}

fwrite($myfile, "\t</Document>\n");
fwrite($myfile, "</kml>\n");
fclose($myfile);

?>