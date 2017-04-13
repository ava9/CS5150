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

		$sql = 'SELECT Deadline FROM porchfests WHERE PorchfestID=1';
		$result = $conn->query($sql);

		$deadline = new DateTime($result->fetch_assoc()['Deadline']);
		list($hour, $minute) = explode(":", $porchfesttime);
		date_time_set($deadline, intval($hour), intval($minute));

		list($year, $month, $day) = explode("-", $porchfestdeadlineday);
		$deadline->setDate(intval($year), intval($month), intval($day));

		$sql = "UPDATE porchfests SET Name='" . $porchfestname . "', Location='" . $porchfestlocation . "', Date='" . $porchfestdate . "', Description='" . $porchfestdescription . "', Deadline='" . $deadline->format("Y-m-d H:i:s")  . "' WHERE PorchfestID=1";

		$result = $conn->query($sql);
		
		if ($result) {
			echo ($conn->error);
			echo "success";
		} else {
			print_r($conn->error);
			echo "fail";
		}

	} elseif (isset($_POST['timeslotstart']) && isset($_POST['timeslotend']) && isset($_POST['start']) && isset($_POST['end']) && isset($_POST['timeslotid'])) {
		// ** editporchfest.php: update timeslot.
		$timeslotid = htmlentities($_POST['timeslotid']);
		$timeslotstart = htmlentities($_POST['timeslotstart']);
		$timeslotend = htmlentities($_POST['timeslotend']);
		$start = date_create_from_format('Y.m.d g:i A', htmlentities($_POST['start']));
		$end = date_create_from_format('Y.m.d g:i A', htmlentities($_POST['end']));

		$datestart = date_create_from_format('g:i A', $timeslotstart);
		$dateend = date_create_from_format('g:i A', $timeslotend);

		$start->setTime($datestart->format('H'), $datestart->format('i'));
		$end->setTime($dateend->format('H'), $dateend->format('i'));

		$sql = "UPDATE porchfesttimeslots SET StartTime='" . $start->format('Y-m-d H:i:s') . "', EndTime='" . $end->format('Y-m-d H:i:s') . "' WHERE PorchfestID=1 AND TimeslotID=" . $timeslotid;

		$result = $conn->query($sql);		

		if ($result) {
			echo 'success';
		} else {
			echo 'fail';
		}

	} elseif (isset($_POST['tid'])) {
		$timeslotid = htmlentities($_POST['tid']);

		$sql = "DELETE FROM `porchfesttimeslots` WHERE TimeslotID=" . $timeslotid;

		$result = $conn->query($sql);		

		if ($result) {
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
		echo "<th> Contact </th>";
		echo "</tr>";

		function email_href($conn, $name) {
	        $result = $conn->query("SELECT Members FROM bands WHERE Name = '" . $name . "'");
	        $band = $result->fetch_assoc();
	        $members = explode(',', $band['Members']);

	        $recipient = $members[0];
	        $cc = '';
	        unset($members[0]);
	        foreach ($members as $key => $value) {
	          $cc = $cc . $value . ',';
	        }

	        $subject = sprintf("[Porchfest] %s", $name);
	        return sprintf("mailto:%s?cc=%s&subject=%s", $recipient, $cc, $subject);
	    }

		if ($name == "") {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID = 1 ORDER BY bands.Name";

		} else {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID = 1 AND bands.Name LIKE '%" . $name . "%' ORDER BY bands.Name; ";
		}

      	$result = $conn->query($sql);

	    while($band = $result->fetch_assoc()) {
			echo '<tr>';
			echo '<td>' . $band['Name'] . '</td>';
			echo '<td>' . $band['Description'] . '</td>';
			echo '<td> List of members </td>';
			echo '<td> <a data-target="#timeslotModal" data-toggle="modal"> Time Slots </a> </td>';
			echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
			echo '<td> <a href="../editband.php"> Edit </a> </td>';
			echo '<td> <a href="' . email_href($conn, $band['Name']) . '" target="_blank"> Email </a> </td>';
		}

		echo '</table>';
	} else {
		print_r($_POST);
		// throw new Exception("variable not found");
	}


?>