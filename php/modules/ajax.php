<?php
	require_once __DIR__."/../config.php";
	require_once "sort.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	function __isset($name) {
	    return $this->args[$name];
	}

	function getBandNames($bands, $conflicts) {
        $names = array();
        foreach($bands as $band) {
          array_push($names, $conflicts[$band]["name"]);
        }

        return $names;
    }


    function getConflicts($conflicts, $bandID) {
        $result = array();
        if (sizeof($conflicts[$bandID]["current"]) > 0) {
          array_push($result, 'This band conflicts with: ' . implode(", ", getBandNames($conflicts[$bandID]["current"], $conflicts)));
          array_push($result, 'This band conflicts with: ' . implode(", ", $conflicts[$bandID]["current"]));
        } else {
          array_push($result, 'No conflicts');
          array_push($result, 'No conflicts');
        }

        return $result;
    }

	// publish button clicked
	if (isset($_POST['publishbutton'])) {
		// flip the publish bit
		$sql = "UPDATE porchfests SET Published=NOT(Published) WHERE PorchfestID='" . $_POST['porchfestid'] . "'";
		$changePubResult = $conn->query($sql);
		if (!$changePubResult) {
			echo "fail\n";
			die('Could not flip publish bit');
		}

		// get current publish status
		$sql = "SELECT Published FROM porchfests WHERE PorchfestID='" .$_POST['porchfestid']. "'";
		$getPubResult = $conn->query($sql);
		if (!$getPubResult) {
			echo "fail\n";
			die('Could not get current pub status');
		}

		
		// if unpublished, set all flags to the special values
		$isPub = $getPubResult->fetch_assoc()['Published'];
		if (!$isPub) {
			ob_start();
			require_once 'scheduling/updateMap.php';
			ob_end_clean();
		// if published, set all flags to 0
		} else {
			$sql = "UPDATE bandstoporchfests SET Flagged=0 WHERE PorchfestID='" . $_POST['porchfestid'] . "'";
			$updateZerosResult = $conn->query($sql);
			if (!$updateZerosResult) {
				echo "fail\n";
				die("Cound not update to all zeros");
			}
		}

		// echo success if not dying already
		echo 'success';

	} elseif (isset($_POST['schedule'])) {
		// ** editporchfest.php: SCHEDULE: run scheduling algorithm
		ob_start();
		require_once 'scheduling/main.php';
		ob_end_clean();

		$sql = 'SELECT scheduled FROM porchfests WHERE PorchfestID=' . $_POST['porchfestid'];
		$result = $conn->query($sql);
		$isSched = $result->fetch_assoc()['scheduled'];	

		if ($isSched) {	
			echo "success";
		} else {
			echo "fail";
		}
	// Send out email to ALL band members
	} elseif (isset($_GET['all_email'])) {
		$all_emails = "";

		// Members are comma seperated, can be joined together
		$sql = sprintf("SELECT Members from bands 
						INNER JOIN bandstoporchfests WHERE bandstoporchfests.PorchfestID = '%s' 
						AND bands.BandID = bandstoporchfests.BandID", $_GET['porchfestid']);
		$result = $conn->query($sql);
		while ($emails = $result->fetch_assoc()) {
			$all_emails = sprintf("%s%s,", $all_emails, $emails['Members']);
		}

		// Create mailto link
		echo sprintf("mailto:?bcc=%s&subject=[%s]", $all_emails, $_GET['porchfestname']);
	// Send out email to select group of timeslots
	} elseif (isset($_GET['mass_email'])) {
		$all_emails = "";
		$timeslots = array();
		foreach ($_GET as $key => $value) {
			if (is_int($key) && filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
				array_push($timeslots, (string)$key);
			}
		}
		$timeslots_str = join("', '", $timeslots);
		// Members are comma seperated, can be joined together
		$sql = sprintf("SELECT Members from bands 
						INNER JOIN bandstoporchfests WHERE bandstoporchfests.PorchfestID = '%s' 
						AND bands.BandID = bandstoporchfests.BandID
						AND bandstoporchfests.TimeslotID IN ('$timeslots_str')", $_GET['porchfestid']);
		$result = $conn->query($sql);
		while ($emails = $result->fetch_assoc()) {
			$all_emails = sprintf("%s%s,", $all_emails, $emails['Members']);
		}
		// Create mailto link
		echo sprintf("mailto:?bcc=%s&subject=[%s]", $all_emails, $_GET['porchfestname']);
	// Update schedule (save-changes-button)
	} elseif (isset($_POST['json'])) {

		$new_schedule = json_decode($_POST['json'], True);

		$sql = "";
		foreach($new_schedule as $key => $band) {
			if ($band['original'] != $band['tid']) {
				$sql.= "UPDATE bandstoporchfests SET TimeslotID='" . $band['tid'] . "' WHERE BandID='" . $key . "'; ";
			}
		}

		$result = $conn->multi_query($sql);
		
		if ($result) {
			ob_start();
			require_once 'scheduling/updateMap.php';
			ob_end_clean();
			echo 'success';
		} elseif (!$sql) {
			echo 'success';
		} else {
			echo 'failure';
		}

	} elseif (isset($_GET['delete']) && isset($_GET['pid'])) {
		$porchfestid = $GET['pid'];

		$sql = "DELETE FROM porchfests WHERE PorchfestID=" . $porchfestid;

		// $result = $conn->query($sql);

		if ($result) {
			echo "success";
		} else {
			echo "fail";
		}


	} elseif (isset($_POST['porchfestname']) && isset($_POST['porchfestlocation']) && isset($_POST['porchfestdate']) && isset($_POST['porchfestdescription']) && isset($_POST['porchfestdeadlineday']) && isset($_POST['porchfestid'])) {
		// ** editporchfest.php: MANAGE PORCHFEST: form to manage porchfest 
		$porchfestname = htmlentities($_POST['porchfestname']);
		$porchfestlocation = htmlentities($_POST['porchfestlocation']);
		$porchfestdate = htmlentities($_POST['porchfestdate']);
		$porchfestdescription = htmlentities($_POST['porchfestdescription']);
		$porchfestdeadlineday = htmlentities($_POST['porchfestdeadlineday']);
		$porchfestid = $_POST['porchfestid'];


		$sql = 'SELECT Deadline FROM porchfests WHERE PorchfestID=' . $porchfestid;
		$result = $conn->query($sql);

		$deadline = new DateTime($result->fetch_assoc()['Deadline']);

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

	} elseif (isset($_POST['poid']) && isset($_POST['conflicts']) && isset($_POST['bname'])) {
		$bname = $mysqli->real_escape_string(htmlentities($_POST['bname']));
		$conflicts = json_decode($_POST['conflicts'], True);
		$porchfestid = htmlentities($_POST['poid']);

		echo '<table class="responsive table"> <!-- begin table -->
		<tr class="fixed" data-status= "fixed">
		<th> Name </th>
		<th> Timeslots </th>
		<th> Conflicts </th>
		</tr>';

		if ($bname == "") {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID =" . $porchfestid . " ORDER BY bands.Name";



		} else {
			$sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID WHERE PorchfestID =" . $porchfestid . " AND bands.Name LIKE '%" . $bname . "%' ORDER BY bands.Name; ";
		}

		$result = $conn->query($sql);

		$bands = array();
        while ($band = $result->fetch_assoc()) {
          $bands[] = $band;
        }

        usort($bands, "cmp");

                                        
        foreach($bands as $band) {
			$conflictList = getConflicts($conflicts, $band['BandID']);

			echo '<tr id="' . 'band-' . $band['BandID'] . '" class="' . (is_null($band['TimeslotID']) ? '' : $band['TimeslotID']) . ' ' . ($conflictList[0] != 'No conflicts' ? 'hasconflict' : '') . '">';
			echo '<td>' . $band['Name'] . '</td>';
			$sql2 = 'SELECT * FROM `porchfesttimeslots` INNER JOIN bandavailabletimes ON porchfesttimeslots.TimeslotID = bandavailabletimes.TimeslotID WHERE bandavailabletimes.bandID=' . $band['BandID'];

			echo '<td><select class="timesdropdown" id="times-' . $band['BandID'] . '">';

			$sql3 = 'SELECT * FROM `porchfesttimeslots` INNER JOIN bandavailabletimes ON porchfesttimeslots.TimeslotID = bandavailabletimes.TimeslotID WHERE bandavailabletimes.bandID="' . $band['BandID'] . '" AND TimeslotID=' . $conflicts[$band['BandID']]["tid"];

			$result3 = $conn->query($sql3);

			if ($result3->num_rows > 0) {
				$assigned = $result3->fetch_assoc();

				$start_time_assigned = date_create($assigned['StartTime']);
				$end_time_assigned = date_create($assigned['EndTime']);

				echo '<option value="' . $assigned['TimeslotID'] . '">' . date_format($start_time_assigned, 'g:i A') . "-" . date_format($end_time_assigned, 'g:i A') . '</option>';
			}

			$result2 = $conn->query($sql2);
			while ($timeslots = $result2->fetch_assoc()) {
				$start_time = date_create($timeslots['StartTime']);
				$end_time = date_create($timeslots['EndTime']);

				if (!($start_time == $start_time_assigned && $end_time == $end_time_assigned)) {
					echo '<option value="' . $timeslots['TimeslotID'] . '">' . date_format($start_time, 'g:i A') . "-" . date_format($end_time, 'g:i A') . '</option>';
				}
			}

			echo '</td></select>';
			echo '<td>';
			echo '<p id="conflict-names-' . $band['BandID'] . '"> ' . $conflictList[0]  . ' <p>';
			echo '<input type="hidden" id="conflicts-' . $band['BandID'] .'" value="' . $conflictList[1] . '">';
			echo '</td>';
		}

		echo '</table> <!-- end table -->';

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

	    $bands = array();
        while ($band = $result->fetch_assoc()) {
          $bands[] = $band;
        }

        usort($bands, "cmp");

                                        
        foreach($bands as $band) {
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
		print_r($_GET);
		// throw new Exception("variable not found");
	}
?>