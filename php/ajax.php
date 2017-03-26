<?php
	require_once "config.php";

	function __isset($name) {
	    return $this->args[$name];
	}


	// Create connection
	// add DB_USER and DB_PASSWORD later
	$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// ** editporchfest.php: form to manage porchfest 
	if (isset($_POST['porchfestname']) && isset($_POST['porchfestlocation']) && isset($_POST['porchfestdate']) && isset($_POST['porchfestdescription']) && isset($_POST['porchfesttime']) && isset($_POST['porchfestdeadlineday'])) {
		$porchfestname = htmlentities($_POST['porchfestname']);
		$porchfestlocation = htmlentities($_POST['porchfestlocation']);
		$porchfestdate = htmlentities($_POST['porchfestdate']);
		$porchfestdescription = htmlentities($_POST['porchfestdescription']);
		$porchfesttime = htmlentities($_POST['porchfesttime']);
		$porchfestdeadlineday = htmlentities($_POST['porchfestdeadlineday']);

		$sql = "UPDATE porchfests SET Name='" . $porchfestname . "', Location='" . $porchfestlocation . "', Date = '" . $porchfestdate . "' WHERE PorchfestID=1";

		$result = $conn->query($sql);
		
		if ($result) {
			echo "success";
		} else {
			echo "fail";
		}

	} elseif (isset($_POST['timeslotstart']) && isset($_POST['timeslotend']) && isset($_POST['start']) && isset($_POST['end'])) {
		// ** editporchfest.php: update timeslot.
		$timeslotstart = htmlentities($_POST['timeslotstart']);
		$timeslotend = htmlentities($_POST['timeslotend']);
		$start = htmlentities($_POST['start']);
		$end = htmlentities($_POST['end']);

		$datestart = date_create_from_format('g:i A', $timeslotstart);
		$dateend = date_create_from_format('g:i A', $timeslotend);

		$sql = "SELECT TimeslotID FROM porchfesttimeslots WHERE StartTime='" . $start . "' AND EndTime='". $end . "'";

		// $sql = "UPDATE porchfesttimeslots SET StartTime='" . $timeslotstart . "', EndTime='" . $timeslotend . "' WHERE PorchfestID=1 AND ";

		$result = $conn->query($sql);

		echo $mysqli->error;

		if ($result) {
			print_r($result->fetch_assoc());
			echo 'success';
		} else {
			echo 'fail';
		}

	} elseif (isset($_GET['bandname'])) {
		// ** editporchfest.php: search functionality to display bands that match name.
		$name = htmlentities($_GET['bandname']);
		echo "<table class='responsive table'> <!-- begin table -->";
		echo "<tr data-status= 'fixed'>";
		echo "<th> Name </th>";
		echo "<th> Description </th>";
		echo "<th> Members </th>";
		echo "<th> Time Slots </th>";
		echo "<th> Scheduled </th>";
		echo "<th> Manage </th>";
		echo "</tr>";

		if ($name == "") {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID = 1 ORDER BY bands.Name";
			$result = $conn->query($sql);

		} else {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID = 1 AND bands.Name LIKE '%" . $name . "%' ORDER BY bands.Name; ";
		}

      	$result = $conn->query($sql);

	    while($band = $result->fetch_assoc()) {
			echo '<tr>';
			echo '<td><a href="#"">' . $band['Name'] . '</a></td>';
			echo '<td>' . $band['Description'] . '</td>';
			echo '<td> List of members </td>';
			echo '<td> <a data-target="#timeslotModal" data-toggle="modal"> Time Slots </a> </td>';
			echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
			echo '<td> <a href="#"> Edit </a> </td>';
		}

		echo '</table>';
	} else {
		print_r($_POST);
		// throw new Exception("variable not found");
	}


?>