<?php
	require_once "config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	function __isset($name) {
	    return $this->args[$name];
	}

	
	if (isset($_POST['publishbutton'])) {
		// ** editporchfest.php: PUBLISH: publish porchfest or not
		$sql = "UPDATE porchfests SET Published=NOT(Published) WHERE PorchfestID='" . $_POST['porchfestid'] . "'";
		$result = $conn->query($sql);

		if ($result) {	
			echo "success";
		} else {
			echo "fail";
		}
	} elseif (isset($_POST['schedule'])) {
		// ** editporchfest.php: SCHEDULE: run scheduling algorithm
		require_once '../scheduling/PorchfestScheduling/main.php';
		if (True) {	
			echo "success";
		} else {
			echo "fail";
		}
	} elseif (isset($_GET['mass_email'])) {
		// sprintf("mailto:%s?cc=%s&subject=%s", $recipient, $cc, $subject);
		$all_emails = "";
		// foreach ($_GET as $key => $value) {
		// 	if (is_int($key) and $value) {

		// 	}
		// }
		echo "mailto:?bcc=selectedmembers@porchfest.com&subject=[Ithaca Porchfest]";
	} elseif (isset($_POST['porchfestname']) && isset($_POST['porchfestlocation']) && isset($_POST['porchfestdate']) && isset($_POST['porchfestdescription']) && isset($_POST['porchfesttime']) && isset($_POST['porchfestdeadlineday']) && isset($_POST['porchfestid'])) {
		// ** editporchfest.php: MANAGE PORCHFEST: form to manage porchfest 
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
		// ** editporchfest.php: SCHEDULE: form to manage whether a conflict arose from a band change
		$timeslotID = htmlentities($_GET['timeslotid']); // The timeslot that the band was changed to 
		$bandID = htmlentities($_GET['bandid']);         // The id of the band where the timeslot was changed

		// Query whether a band that was listed as conflicting with another band
		// is currently in the timeslot that this band was updated to, then report a conflict 

		// First, get all the conflicts related to the current band
		// Conflicts are two way, so we have to find conflicts where the current band is either
		// listed as the first or second band
		$sql = sprintf("SELECT BandID2 FROM bandconflicts WHERE BandID1 = '%s'
						UNION
						SELECT BandID1 FROM bandconflicts WHERE BandID2 = '%s'", $bandID, $bandID);
		$result = $conn->query($sql);

		// For each conflicting band, see if it is currently in the $timeslotid. If so, conflict!
		while ($conflict = $result->fetch_assoc()) {
			$conflictbandID = -1;
			try {
				$conflictbandID = $conflict['BandID2'];
			} catch (Exception $e) {
				$conflictbandID = $conflict['BandID1'];
			}

			$sql2 = sprintf("SELECT * FROM bandstoporchfests WHERE BandID = '%s' AND TimeslotID = '%s'",
							$conflictbandID, $timeslotID);
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
				echo "overlap";
				return;
			}
		}
		echo "no overlap";

	} else if (isset($_POST['porchfestid']) && isset($_POST['newstart']) && isset($_POST['newend'])) {
		// ** editporchfest.php: MANAGE TIMESLOTS: form to create new timeslot.
		$porchfestid = $_POST['porchfestid'];
		$timeslotstart = htmlentities($_POST['newstart']);
		$timeslotend = htmlentities($_POST['newend']);

		$sql = "SELECT Date FROM porchfests WHERE PorchfestID=" . $porchfestid;

		$result = $conn->query($sql);

		$date = $result->fetch_assoc()['Date'];

		$start = date_create_from_format("Y-m-d H:i", $date . " " . $timeslotstart);
		$end = date_create_from_format("Y-m-d H:i", $date . " " . $timeslotend);

		$sql1 = sprintf("INSERT INTO porchfesttimeslots (StartTime, EndTime, PorchfestID) VALUES ('%s', '%s', '%s')", $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $porchfestid);

	    $result1 = $conn->query($sql1);

	    $sql2 = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $porchfestid . "' AND TimeslotID='" . $conn->insert_id . "';";

        $result2 = $conn->query($sql2);

        $timeslot = $result2->fetch_assoc();

        $start_time = date_create($timeslot['StartTime']);
	    $end_time = date_create($timeslot['EndTime']);

	    $label = '<div class="col-xs-6 col-sm-3 timeslot-label"><span id="' . $timeslot['TimeslotID'] . '-' . date_format($start_time, 'Y.m.d-g:iA') . "-" . date_format($end_time, 'Y.m.d-g:iA') . '" class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';

		
		if ($result1) {
			echo $label;
		} else {
			echo 'fail';
		}
	} elseif (isset($_POST['timeslotstart']) && isset($_POST['timeslotend']) && isset($_POST['start']) && isset($_POST['end']) && isset($_POST['timeslotid']) && isset($_POST['porchfestid'])) {
		// ** editporchfest.php: MANAGE TIMESLOTS: update timeslot.
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
		// ** editporchfest.php: MANAGE TIMESLOTS: delete timeslot.
		$timeslotid = htmlentities($_POST['tid']);

		$sql = "DELETE FROM `porchfesttimeslots` WHERE TimeslotID='" . $timeslotid . "'";

		$result = $conn->query($sql);		

		if ($result) {
			echo 'success';
		} else {
			echo $conn->error;
			echo 'fail';
		}

	} elseif (isset($_GET['bandname']) && isset($_GET['porchfestid'])) {
		// ** editporchfest.php: MANAGE BANDS: search functionality to display bands that match name.
		$name = $mysqli->real_escape_string(htmlentities($_GET['bandname']));
		$porchfestid = $_GET['porchfestid'];
		echo "<table class='responsive table'> <!-- begin table -->";
		echo "<tr class='fixed' data-status= 'fixed'>";
		echo "<th> Name </th>";
		echo "<th> Description </th>";
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
			echo '<tr class="' . (is_null($band['TimeslotID']) ? '' : $band['TimeslotID']) . '">';
			echo '<td>' . $band['Name'] . '</td>';
			echo '<td>' . $band['Description'] . '</td>';
			echo '<td> <a data-target="#timeslotModal" data-toggle="modal"> Time Slots </a> </td>';
			echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
			echo '<td> <a href="../editband.php"> Edit </a> </td>';
			echo '<td> <a href="' . email_href($conn, $mysqli->real_escape_string($band['Name'])) . '" target="_blank"> Email </a> </td>';
		}

		echo '</table>';
	} else {
		echo 'here in ajax ';
		print_r($_POST);
		// throw new Exception("variable not found");
	}
?>