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
	echo ("\t\t\t<Style id=".$s.">\n");
	echo ("\t\t\t\t<LabelStyle>\n");
	echo ("\t\t\t\t\t<color>".$colorArr[$i]."</color>\n");
	echo ("\t\t\t\t\t<colorMode>normal</colorMode>\n");
	echo ("\t\t\t\t\t<scale>1</scale>\n");
	echo ("\t\t\t\t</LabelStyle>\n");
	echo ("\t\t\t</Style>\n");
}

$colorArray = array("501400FF", "501478FF", "5014F0FF", "5078FF00", "50FF7800", "50FF78F0", "50FF78B4", "50140000", "50147800", "5014F000", "50780000", "50780078", "507800B4", "500078F0", "507800F0", "507882F0", "508278F0", "50F07814", "5078A03C", "501478C8");

//$myfile = fopen("output.kml", "w");
echo ("<?xml version='1.0' encoding='UTF-8'?>\n");
echo ("<kml xmlns='http://www.opengis.net/kml/2.2' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:gx='http://www.google.com/kml/ext/2.2' xmlns:kml='http://www.opengis.net/kml/2.2' xmlns:xal='urn:oasis:names:tc:ciq:xsdschema:xAL:2.0'>\n");
echo ("\t<Document>\n");




// Database credentials
require_once "config.php";

// Set date timezone
date_default_timezone_set('America/New_York');

// Create connection
// add DB_USER and DB_PASSWORD later
$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT bands.Name, bands.Description, PorchLocation, porchfesttimeslots.StartTime FROM bands INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID INNER JOIN porchfesttimeslots ON porchfesttimeslots.TimeslotID = bandstoporchfests.TimeslotID ORDER BY porchfesttimeslots.StartTime;";

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
		$bandName = $row["Name"];
		$bandDescription = $row["Description"];
		$bandLocation = $row["PorchLocation"];
		$bandTimeSlotID = $row["TimeslotID"];
		$bandStartTime = $row["StartTime"];

		// now you have the band name, decription, location, and start time
		// $bandName, $bandDescription, $bandLocation, $bandStartTime
		$bandLocation = getCoordinates($bandLocation);
		// YYYY-MM-DD HH:MM:SS
		$t = strtotime($bandStartTime);
		$hour = date('g', $t);
		$min = date('i', $t);
		$a = date('A', $t);
		$time = $hour . ":" . $min . " " . $a;
		
		if ($count == 1){
			$curr = $time;
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
			$colorIndex = $colorIndex % 20;
		}
		
		if (($flipBit == 0) && ($internalCount == 1)){
			$s1 = '"' .$curr . '"';
			echo ("\t\t<Folder id=" . $s1 . ">\n");
			printStyle($colorArray, $colorIndex);
			echo ("\t\t\t<name>". $curr ."</name>\n");
			$open = $open + 1;
		}
		
		if (($flipBit == 1) && ($internalCount == 1)){
			if ($open == 1){
				echo ("\t\t</Folder>\n");
				$open = $open - 1;
			}
			$s2 = '"' .$curr . '"';
			echo ("\t\t<Folder id=" . $s2 . ">\n");
			printStyle($colorArray, $colorIndex);
			echo ("\t\t\t<name>". $curr ."</name>\n");
			$open = $open + 1;
		}
		
		echo ("\t\t\t<Placemark>\n");
		echo ("\t\t\t\t<name>".$bandName."</name>\n");
		echo ("\t\t\t\t<description>".$bandDescription."</description>\n");
		echo ("\t\t\t\t<styleUrl>#".$colorIndex."</styleUrl>\n");
		echo ("\t\t\t\t<Point>\n");
		echo ("\t\t\t\t\t<coordinates>".$bandLocation."</coordinates>\n");
		echo ("\t\t\t\t</Point>\n");
		echo ("\t\t\t</Placemark>\n");
		
	}
} else {
	echo "0 results";
}
$conn->close();

if ($open == 1){
	echo ("\t\t</Folder>\n");
	$open = $open - 1;
}

echo ("\t</Document>\n");
echo ("</kml>\n");
//fclose($myfile);

?>