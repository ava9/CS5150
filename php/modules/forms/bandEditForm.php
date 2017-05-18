<!-- Form for editing band information -->
<h3>Band Information</h3>
<form class="form-horizontal" role="form"  method="POST">
  <div class="form-group">
    <label class="col-lg-3 control-label">Name:</label>
    <div class="col-lg-8">
      <input data-validation="length" data-validation-length="min1" required name="bandname" class="form-control" value=<?php echo '"' . $band['Name'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $bandnameError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Description:</label>
    <div class="col-lg-8">
      <input required name="banddescription" class="form-control" value=<?php echo '"' . $band['Description'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $descriptionError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Description:</label>
    <div class="col-lg-8">
      <input required name="bandURL" class="form-control" value=<?php echo '"' . $band['URL'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $urlError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Member Emails:</label>
    <div class="col-lg-8">
      <input name="bandmembers" class="form-control" value=<?php echo '"' . $band['Members'] . '"' ?> type="text">
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Comment:</label>
    <div class="col-lg-8">
      <input name="bandcomment" class="form-control" value=<?php echo '"' . $band['Comment'] . '"' ?> type="text">
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Conflicts:</label>
    <div class="col-lg-8">
      <?php 
        // Gets all band conflicts
        $sql = "SELECT * from bands INNER JOIN bandconflicts ON 
        (bands.BandID = bandconflicts.BandID1 OR bands.BandID = bandconflicts.BandID2) WHERE bands.BandID != '" . $bandID . "'";
        $result = $conn->query($sql);
        while ($conflictingbands = $result->fetch_assoc()) {
          $conflictsarray[] = array("id" => $conflictingbands['BandID'], "name" => $conflictingbands['Name']);
        }
      ?>
      <input name="bandconflicts" id="conflict-input" type="text" class="form-control" placeholder="Band1,Band2,Band3" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Porch Location:</label>
    <div class="col-lg-8">
      <input required name="porchlocation" id="autocomplete" class="form-control" value=<?php echo '"' . $band['PorchLocation'] . '"' ?> onFocus="geolocate()"  type="text"> <?php echo '<span class="error">'; echo $locationError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label for="name" class="col-lg-3 control-label"> Available Times </label>
    <div class="col-lg-8">
    <?php 
      // Query to get all available timeslots from porchfest
      $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID= '" . $porchfestID . "' ORDER BY StartTime";

      // Displays all available timeslots and then checks the boxes that the band already indicated as available
      $result = $conn->query($sql);
      while($timeslot = $result->fetch_assoc()) {
        $sql2 = sprintf("SELECT * FROM bandavailabletimes WHERE BandID = '%s' AND TimeslotID = '%s'", 
                  $bandID, $timeslot['TimeslotID']);
        $result2 = $conn->query($sql2);

        $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
        $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
        $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
        if ($result2->num_rows > 0) {
          echo "<input checked name='available[]' type='checkbox' value='" . $timeslot['TimeslotID'] . "' />" . " " . $starttime . 
                "-" . $endtime . " on " . $day . "<br>";
        }
        else {
          echo "<input name='available[]' type='checkbox' value='" . $timeslot['TimeslotID'] . "' />" . " " . $starttime . 
                "-" . $endtime . " on " . $day . "<br>";
        }
      }
    ?>
    </div>
</div>
  <div class="form-group">
    <label class="col-md-3 control-label"></label>
    <div class="col-md-8">
      <input name="submit" class="btn btn-primary" value="Save Changes" type="submit">
      <span></span>
      <input class="btn btn-default" onclick="history.back()" value="Cancel" type="reset">
    </div>
  </div>
</form>