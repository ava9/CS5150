<?php
	require_once "config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	function __isset($name) {
	    return $this->args[$name];
	}

	// ** editporchfest.php: publish porchfest or not
	if (isset($_POST['publishbutton'])) {
		$sql = "UPDATE porchfests SET Published=NOT(Published) WHERE PorchfestID='" . $_POST['porchfestid'] . "'";
		$result = $conn->query($sql);

		if ($result) {
			echo "success";
		} else {
			echo "fail";
		}
	}
	// ** editporchfest.php: form to manage porchfest 
	elseif (isset($_POST['porchfestname']) && isset($_POST['porchfestlocation']) && isset($_POST['porchfestdate']) && isset($_POST['porchfestdescription']) && isset($_POST['porchfesttime']) && isset($_POST['porchfestdeadlineday']) && isset($_POST['porchfestid'])) {
		$porchfestname = htmlentities($_POST['porchfestname']);
		$porchfestlocation = htmlentities($_POST['porchfestlocation']);
		$porchfestdate = htmlentities($_POST['porchfestdate']);
		$porchfestdescription = htmlentities($_POST['porchfestdescription']);
		$porchfesttime = htmlentities($_POST['porchfesttime']);
		$porchfestdeadlineday = htmlentities($_POST['porchfestdeadlineday']);
		$porchfestid = $_POST['porchfestid'];


		$sql = 'SELECT Deadline FROM porchfests WHERE PorchfestID=' . $porchfestid;
		$result = $conn->query($sql);

		$deadline = new DateTime($result->fetch_assoc()['Deadline']);
		list($hour, $minute) = explode(":", $porchfesttime);
		date_time_set($deadline, intval($hour), intval($minute));

		list($year, $month, $day) = explode("-", $porchfestdeadlineday);
		$deadline->setDate(intval($year), intval($month), intval($day));

		$sql = "UPDATE porchfests SET Name='" . $porchfestname . "', Location='" . $porchfestlocation . "', Date='" . $porchfestdate . "', Description='" . $porchfestdescription . "', Deadline='" . $deadline->format("Y-m-d H:i:s")  . "' WHERE PorchfestID = '" . $porchfestid . "'";

		$result = $conn->query($sql);
		
		if ($result) {
			echo "success";
		} else {
			echo "fail";
		}

	} elseif (isset($_GET['timeslotid']) && isset($_GET['bandid'])) {
		$timeslotid = htmlentities($_GET['timeslotid']);
		$bandid = htmlentities($_GET['bandid']);

		// $sql = sprintf("SELECT * FROM bandconflicts WHERE ")


		$sql = "SELECT * FROM `bandstoporchfests` WHERE TimeslotID=" . $timeslotid . " AND NOT(BandID=" . $bandid .")";

		$result = $conn->query($sql);

		$sql2 = "UPDATE `bandstoporchfests` SET TimeSlotID=" . $timeslotid . " WHERE BandID=" . $bandid;

		$result2 = $conn->query($sql2);

		if ($result->num_rows > 0) {
			echo "overlap";
		} else {
			echo "no overlap";
		}
	} elseif (isset($_POST['timeslotstart']) && isset($_POST['timeslotend']) && isset($_POST['start']) && isset($_POST['end']) && isset($_POST['timeslotid'])
			&& isset($_POST['porchfestid'])) {
		// ** editporchfest.php: update timeslot.
		$porchfestid = $_POST['porchfestid'];
		$timeslotid = htmlentities($_POST['timeslotid']);
		$timeslotstart = htmlentities($_POST['timeslotstart']);
		$timeslotend = htmlentities($_POST['timeslotend']);
		$start = date_create_from_format('Y.m.d g:iA', htmlentities($_POST['start']));
		$end = date_create_from_format('Y.m.d g:iA', htmlentities($_POST['end']));

		$datestart = date_create_from_format('g:iA', $timeslotstart);
		$dateend = date_create_from_format('g:iA', $timeslotend);

		$start->setTime($datestart->format('H'), $datestart->format('i'));
		$end->setTime($dateend->format('H'), $dateend->format('i'));

		$sql = "UPDATE porchfesttimeslots SET StartTime='" . $start->format('Y-m-d H:i:s') . "', EndTime='" . $end->format('Y-m-d H:i:s') . "' WHERE PorchfestID=". $porchfestid . " AND TimeslotID=" . $timeslotid;

		$result = $conn->query($sql);		

		if ($result) {
			echo "success";
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

	} elseif (isset($_GET['bandname']) && isset($_GET['porchfestid'])) {
		// ** editporchfest.php: search functionality to display bands that match name.
		$name = htmlentities($_GET['bandname']);
		$porchfestid = $_GET['porchfestid'];
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
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID =" . $porchfestid . " ORDER BY bands.Name";

		} else {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID =" . $porchfestid . " AND bands.Name LIKE '%" . $name . "%' ORDER BY bands.Name; ";
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
		echo 'here in ajax';
		print_r($_POST);
		// throw new Exception("variable not found");
	}


?>