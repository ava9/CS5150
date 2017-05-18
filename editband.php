<?php 
# This page is where bands can edit the information they submitted
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
    require_once "config.php";
    require_once CODE_ROOT . "/php/modules/stdHead.php";
  ?> 
  <link rel="stylesheet" href="<?php echo CSS_TOKEN_INPUT; ?>" type="text/css" />
  <title>Edit Band</title>
</head>

<body>
  <div id="editalert"></div>
  <?php 
  require_once CODE_ROOT . "/php/modules/routing.php";
  require_once CODE_ROOT . "/php/modules/login.php";
  require_once CODE_ROOT . "/php/modules/navigation.php";?>
  <?php // Database credentials
    

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Gets porchfestID from url
    $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE porchfests.Name = '%s'", PORCHFEST_NAME);
    $result = $conn->query($sql);
    $porchfestID = $result->fetch_assoc()['PorchfestID'];

    // Gets bandID from url
    $sql = sprintf("SELECT BandID FROM bands WHERE bands.Name = '%s'", $conn->real_escape_string(BAND_NAME));
    $result = $conn->query($sql);
    $bandID = $result->fetch_assoc()['BandID'];

  $bandnameError = $descriptionError = $locationError = $urlError = "";
  // Checks if form was submitted
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Checks to make sure required fields are not empty in form
    if (empty($_POST['bandname'])) {
      $bandnameError = 'Missing';
    }
    else {
      $bandname = htmlentities($_POST['bandname']);
    }
    if (empty($_POST['banddescription'])) {
      $descriptionError = 'Missing';
    }
    else {
      $banddescription = htmlentities($_POST['banddescription']);
    }
    if (empty($_POST['bandURL'])) {
      $urlError = 'Missing';
    }
    else {
      $bandURL = filter_var($_POST['bandURL'], FILTER_SANITIZE_STRING);
    }
    $bandmembers = htmlentities($_POST['bandmembers']);
    $bandcomment = htmlentities($_POST['bandcomment']);
    $bandconflicts = htmlentities($_POST['bandconflicts']);
    if (empty($_POST['porchlocation'])) {
      $locationError = 'Missing';
    }
    else {
      $porchlocation = htmlentities($_POST['porchlocation']);
    }

    // Query to update information in the bands table
    $sql = "UPDATE bands SET Name='" . $bandname . "', Description='" . $banddescription . "', 
            URL = '" . $bandURL . "', Members = '" . $bandmembers . "', Comment = '" . $bandcomment . "' WHERE BandID= '" . $bandID . "'";
    $result = $conn->query($sql);
    
    // Query to update information in the bandstoporchfests table
    $sql = "UPDATE bandstoporchfests SET PorchLocation='" . $porchlocation . "'  WHERE PorchfestID = '" . $porchfestID . "' AND BandID= '" . $bandID . "'";
    $result = $conn->query($sql);

    // First deletes all bandavailabletimes
    $sql = "DELETE FROM bandavailabletimes WHERE BandID= '" . $bandID . "'";
    $result = $conn->query($sql);

    // Then adds back all new available times to bandavailabletimes
    if (isset($_POST['available'])) {
      $available = $_POST['available'];
      foreach($available as $timeslot) {
        $sql = "INSERT INTO bandavailabletimes (BandID, TimeslotID) VALUES (?, ?)";
        $prep = $mysqli->prepare($sql);
        $prep->bind_param("ss", $bandID, $timeslot);
        $prep->execute();
      }
    }

    // First deletes all conflicts
    $sql = "DELETE FROM bandconflicts WHERE BandID1= '" . $bandID . "' OR BandID2 = '" . $bandID . "'";
    $result = $conn->query($sql);

    // Insert into bandconflicts table
    // Insert the conflicts into bandconflicts
    if (isset($_POST['bandconflicts'])) {
      $bandconflictlist = explode(',', $bandconflicts);
      foreach ($bandconflictlist as $bconflict) {
        $prep = $mysqli->prepare("INSERT INTO bandconflicts (BandID1, BandID2) 
                                  VALUES (?,?)");
        $prep->bind_param("ss", $bandID, $bconflict);
        $prep->execute();
      }
    }

    echo '<script> $("#editalert").html(\'<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your band information was updated successfully!. </div>\'); </script>';

  }

  // Gets all band information
  $sql = sprintf("SELECT * FROM bands 
        INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID
        WHERE bandstoporchfests.PorchfestID = '%s' AND bands.BandID = '%s'", 
        $porchfestID, $bandID);

  $result = $conn->query($sql);
  $band = $result->fetch_assoc();
  ?>
<!-- Form for bands to change their information with current information from database displayed -->
<div class="container" style="padding-top: 60px;">
  <div class="row">
    <!-- edit form column -->
    <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
      <?php require_once CODE_ROOT . "/php/modules/forms/bandEditForm.php"; ?>
    </div>
  </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<!-- JavaScript to prevent forms from submitting on enter -->
<script type='text/javascript'>
  // enable form validation
  $.validate({
    lang: 'en',
    modules : 'date'
  });

  var conflictlist = <?php echo json_encode($conflictsarray); ?>;

  $(document).ready(function () {
    $("#conflict-input").tokenInput("<?php echo PHP_BAND_LISTING . "?id=" . $porchfestID; ?>", {
        preventDuplicates: true, theme: "facebook", prePopulate: conflictlist
    });
  });

  // function for editalert display
  $('body').click(function() {
        $("#editalert").html('');
  });
  
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>

</body>
</html>


