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
	 
	return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);
	 
}

$arr = array("icon-1889-000000", "icon-1889-FF00FF", "icon-1889-800080", "icon-1889-0000FF", "icon-1889-000080", "icon-1889-00FFFF", "icon-1889-008080", "icon-1889-00FF00", "icon-1889-008000", "icon-1889-FFFF00", "icon-1889-808000", "icon-1889-FF0000", "icon-1889-800000", "icon-1889-C0C0C0", "icon-1889-808080");

$myfile = fopen("output.kml", "w");
fwrite($myfile, "<?xml version='1.0' encoding='UTF-8'?>\n");
fwrite($myfile, "<kml xmlns='http://www.opengis.net/kml/2.2'>\n");
fwrite($myfile, "\t<Document>\n");
fwrite($myfile, "\t<name>Porchfest</name>\n");
fwrite($myfile, "\t<description><![CDATA[]]></description>\n");

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

$sql = "SELECT BandID, Name, Description FROM bands;";

$result = $conn->query($sql);
$bandLocation;
$bandTimeSlotID;
// SELECT bands.Name, bands.Description, PorchLocation, porchfesttimeslots.StartTime
//FROM bands 
//INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
//INNER JOIN porchfesttimeslots ON porchfesttimeslots.TimeslotID = bandstoporchfests.TimeslotID
//ORDER BY porchfesttimeslots.StartTime 
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		$bandId = $row["BandID"];
		$bandName = $row["Name"];
		$bandDescription = $row["Description"];
		$sql2 = "SELECT PorchLocation, TimeslotID FROM bands INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID WHERE bands.BandID = ";
		$sql2 = $sql2 .$bandId . ";";
		$result2 = $conn->query($sql2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {
				$bandLocation = $row2["PorchLocation"];
				$bandTimeSlotID = $row2["TimeslotID"];
			}
		}
		$sql3 = "SELECT * FROM `porchfesttimeslots` WHERE porchfesttimeslots.TimeslotID = ";
		$sql3 = $sql3 .$bandTimeSlotID . ";";
		$result3 = $conn->query($sql3);
		
		if ($result3->num_rows > 0) {
			while($row3 = $result3->fetch_assoc()) {
				$bandStartTime = $row3["StartTime"];
			}
		}
		// now you have the band name, decription, location, and start time
		// $bandName, $bandDescription, $bandLocation, $bandStartTime
		$bandLocation = getCoordinates($bandLocation);
		// YYYY-MM-DD HH:MM:SS
		$hour = strtotime($bandStartTime);
		$hour = date('H', $hour);
		
		fwrite($myfile, "\t\t<Placemark>\n");
		fwrite($myfile, "\t\t\t<name>".$bandName."</name>\n");
		fwrite($myfile, "\t\t\t<description>".$bandDescription."</description>\n");
		fwrite($myfile, "\t\t\t<styleUrl>#icon-1899-0288D1</styleUrl>\n");
		fwrite($myfile, "\t\t\t<Point>\n");
		fwrite($myfile, "\t\t\t\t<coordinates>".$bandLocation."</coordinates>\n");
		fwrite($myfile, "\t\t\t</Point>\n");
		fwrite($myfile, "\t\t</Placemark>\n");
		
	}
} else {
	echo "0 results";
}
$conn->close();

fwrite($myfile, "\t</Document>\n");
fwrite($myfile, "</kml>\n");
fclose($myfile);

?>