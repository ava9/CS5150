<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Band Sign-Up</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-purple.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <script src="../js/navbar.es6"></script>
  <script src="../js/loginmodal.es6"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script src="../js/jquery.js"></script>
  <script src="../js/addressautocomplete.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>

<!-- BEGIN body -->
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    // add DB_USER and DB_PASSWORD later
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  ?>

  <div class="container"> <!-- Container div -->
    <script type="text/javascript">writeloginmodal();</script>

    <div class="row">
      <script type="text/javascript">writenav();</script>
    </div>
    
    <div class="row">
      <h1 style="text-align:center;"> Sign-up to perform for Ithaca Porchfest </h1>
    </div>

    <p> If you would like to perform for Ithaca Porchfest, please fill out the form below. 
      <br>Filling out this form will create an account that you can log back into to edit your information.
    </p>

    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#myModal">
      Already have an account?
    </button>

    <form role="form" class="form-horizontal">
      <h4> Account Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Your Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John Doe" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Your Email</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="johndoe@gmail.com" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="password" class="form-control" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Confirm Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="password" class="form-control" />
                  </div>
              </div>
          </div>
      </div>
      <br>
      <h4> Band Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Band Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John and Friends" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Description </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John and Friends plays cool music." />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Porch Location </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Available Times </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
          <?php 
            $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '1' ORDER BY StartTime;";

            $result = $conn->query($sql);
            while($timeslot = $result->fetch_assoc()) {
              echo "<input type='checkbox' value='timeslot'" . $timeslot['TimeslotID'] . "/>" . " " . explode(' ', $timeslot['StartTime'])[1] . 
                    " to " . explode(' ', $timeslot['EndTime'])[1] . " on " . explode(' ', $timeslot['StartTime'])[0] . "<br>";
            }
          ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Band Member Emails </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                    <div id="dynamicInput">Band Member 1<br>
                      <input type="email" class="form-control" name="myInputs[]" placeholder="friend@gmail.com">
                    </div>
                  <input type="button" value="Add another band member" onClick="addInput('dynamicInput');">
                  </div>
              </div>
          </div>
      </div>
      <button type="button" class="btn btn-primary btn-sm">
                                        Submit</button>
    </form>

  </div> <!-- end container div -->

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

</body>
  <script src="../js/addMembers.js"></script>
</html>