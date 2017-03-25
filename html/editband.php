<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PorchFest - My Settings</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-purple.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <script src="../js/navbar.es6"></script>
  <script src="../js/addMember.js"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script src="../js/jquery.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM bands 
            INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID
            INNER JOIN userstobands ON bands.BandID = userstobands.BandID
            WHERE userstobands.UserID = '1' AND bandstoporchfests.PorchfestID = '1' AND bands.BandID = '1'";

    $result = $conn->query($sql);
    $band = $result->fetch_assoc();
  ?>
<script type="text/javascript">writenav();</script>
<div class="container" style="padding-top: 60px;">
  <div class="row">
    <!-- edit form column -->
    <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
      <h3>Band Information</h3>
      <form class="form-horizontal" role="form">
        <div class="form-group">
          <label class="col-lg-3 control-label">Name:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $band['Name'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Description:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $band['Description'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Comment:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $band['Comment'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Porch Location:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $band['PorchLocation'] . '"' ?> type="text">
          </div>
        </div>
        <?php
          $sql = "SELECT * FROM bandmembers where BandID = '1'";
          $result = $conn->query($sql);
          if (mysqli_num_rows($result) > 0) {
            echo '<div class="form-group">
                  <label class="col-lg-3 control-label">Band Member Emails:</label>';
            while($bandmember = $result->fetch_assoc()) {
              echo '<div class="col-lg-8">
                      <input class="form-control" value=' . $bandmember['Email'] . 'type="text">
                    </div>';
            }
            echo '</div>';
          }
        ?>
        <div class="form-group">
          <label for="name" class="col-lg-3 control-label"> Available Times </label>
          <div class="col-lg-8">
          <?php 
            $sql = "SELECT * FROM porchfesttimeslots 
                    INNER JOIN bandavailabletimes ON bandavailabletimes.TimeslotID = porchfesttimeslots.TimeslotID
                    WHERE PorchfestID = '1' AND bandavailabletimes.BandID = '1' 
                    ORDER BY StartTime";

            $result = $conn->query($sql);
            while($timeslot = $result->fetch_assoc()) {
              $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
              $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
              $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
              echo "<input checked type='checkbox' value='timeslot'" . $timeslot['TimeslotID'] . "/>" . " " . $starttime . 
                    "-" . $endtime . " on " . $day . "<br>";
            }

            $sql = "SELECT * FROM porchfesttimeslots 
                    LEFT JOIN bandavailabletimes ON porchfesttimeslots.TimeslotID = bandavailabletimes.TimeslotID
                    WHERE PorchfestID = '1' AND bandavailabletimes.TimeslotID IS NULL";

            $result = $conn->query($sql);
            while($timeslot = $result->fetch_assoc()) {
              $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
              $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
              $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
              echo "<input type='checkbox' value='timeslot'" . $timeslot['TimeslotID'] . "/>" . " " . $starttime . 
                    "-" . $endtime . " on " . $day . "<br>";
            }
          ?>
          </div>
      </div>
        <div class="form-group">
          <label class="col-md-3 control-label"></label>
          <div class="col-md-8">
            <input class="btn btn-primary" value="Save Changes" type="submit">
            <span></span>
            <input class="btn btn-default" value="Cancel" type="reset">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>


