<?php
	// the, ?, numbers, any special characters 
	
	function str_replace_first($from, $to, $subject)
	{
		$from = '/'.preg_quote($from, '/').'/';

		return preg_replace($from, $to, $subject, 1);
	}
	
	function cmp($s1, $s2){
		
		$string1 = str_replace_first("The ", "", $s1[0]);
		$string1 = str_replace_first("the ", "", $string1);
		$string1 = str_replace_first("?", "", $string1);
		$string1 = str_replace_first("!", "", $string1);
		$string1 = strtolower($string1);
		//$string1 = str_replace_first('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', "", $string1);
		
		$string2 = str_replace_first("The ", "", $s2[0]);
		$string2 = str_replace_first("the ", "", $string2);
		$string2 = str_replace_first("?", "", $string2);
		$string2 = str_replace_first("!", "", $string2);
		$string2 = strtolower($string2);

		//$string2 = str_replace_first('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', "", $string2);

		return strcmp($string1, $string2);
	}	
	// database host
	define('DB_HOST', '127.0.0.1');

	// database name
	define('DB_NAME', 'porchfest3');
	// Your MySQL / Course Server username
	define('DB_USER', 'root');
	// ...and password
	define('DB_PASSWORD', '');
	// also salt
	define('SALT', 'PorchfestsForever');
	
	// Create connection
	// add DB_USER and DB_PASSWORD later
	$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$sql = sprintf("SELECT bands.Name FROM bands INNER JOIN bandstoporchfests WHERE bands.BandID = bandstoporchfests.BandID AND bandstoporchfests.PorchfestID = 1 ORDER BY Name, bandstoporchfests.PorchfestID;");
	
	$result = $conn->query($sql);
	
	if ($result->num_rows == 0) {
		echo "Looks like no bands have signed up yet. Check back later!";
	}
	else {
		//var_dump($result->fetch_all());
		$bands = $result->fetch_all();
		usort($bands, "cmp");
		var_dump($bands);
		
	}
?>