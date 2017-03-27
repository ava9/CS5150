<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../php/modules/stdHead.php" ?>  
  <?php require_once "../php/modules/login.php"; ?>
  <?php require_once "../php/modules/navigation.php"; ?>
  <title>Edit Band</title>
</head>

<body>

  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bandname = htmlentities($_POST['bandname']);
    $banddescription = htmlentities($_POST['banddescription']);
    $bandmembers = htmlentities($_POST['bandmembers']);
    $bandcomment = htmlentities($_POST['bandcomment']);
    $bandconflicts = htmlentities($_POST['bandconflicts']);
    $porchlocation = htmlentities($_POST['porchlocation']);

    $sql = "UPDATE bands SET Name='" . $bandname . "', Description='" . $banddescription . "', 
            Members = '" . $bandmembers . "', Comment = '" . $bandcomment . "', Conflicts = '" . $bandconflicts . "' WHERE BandID='1'";
    $result = $conn->query($sql);
    
    $sql = "UPDATE bandstoporchfests SET PorchLocation='" . $porchlocation . "'  WHERE PorchfestID = '1' AND BandID='1'";
    $result = $conn->query($sql);

    $sql = "DELETE FROM bandavailabletimes WHERE BandID = '1'";
    $result = $conn->query($sql);

    if (isset($_POST['available'])) {
      $available = $_POST['available'];
      foreach($available as $timeslot) {
        $sql = "INSERT INTO bandavailabletimes (BandID, TimeslotID) VALUES ('1', '" . $timeslot . "')";
        $result = $conn->query($sql);
      }
    }
  }

  $sql = "SELECT * FROM bands 
        INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID
        INNER JOIN userstobands ON bands.BandID = userstobands.BandID
        WHERE userstobands.UserID = '1' AND bandstoporchfests.PorchfestID = '1' AND bands.BandID = '1'";

  $result = $conn->query($sql);
  $band = $result->fetch_assoc();
  ?>
<div class="container" style="padding-top: 60px;">
  <div class="row">
    <!-- edit form column -->
    <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
      <h3>Band Information</h3>
      <form class="form-horizontal" role="form" action="editband.php" method="POST">
        <div class="form-group">
          <label class="col-lg-3 control-label">Name:</label>
          <div class="col-lg-8">
            <input name="bandname" class="form-control" value=<?php echo '"' . $band['Name'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Description:</label>
          <div class="col-lg-8">
            <input name="banddescription" class="form-control" value=<?php echo '"' . $band['Description'] . '"' ?> type="text">
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
            <input name="bandconflicts" class="form-control" value=<?php echo '"' . $band['Conflicts'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Porch Location:</label>
          <div class="col-lg-8">
            <input name="porchlocation" id="autocomplete" class="form-control" value=<?php echo '"' . $band['PorchLocation'] . '"' ?> onFocus="geolocate()"  type="text">
          </div>
        </div>
        <div class="form-group">
          <label for="name" class="col-lg-3 control-label"> Available Times </label>
          <div class="col-lg-8">
          <?php 
            $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID='1' ORDER BY StartTime";

            $result = $conn->query($sql);
            while($timeslot = $result->fetch_assoc()) {
              $sql2 = "SELECT * FROM bandavailabletimes
                       WHERE BandID = '1' AND TimeslotID = '" . $timeslot['TimeslotID'] . "'";
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
            <input class="btn btn-default" value="Cancel" type="reset">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

</body>
</html>


