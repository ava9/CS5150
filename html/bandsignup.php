<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <!-- we aren't using mdl anymore? feel free to delete this unless we are using its js?? -->
  <!-- <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script> -->
  
  <title>Band Sign-Up</title>

</head>

<!-- BEGIN body -->
<body>

  <div class="container"> <!-- Container div -->
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>
    
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
          <label for="name" class="col-sm-2 control-label"> Your Name </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John Doe" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Your Email </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="johndoe@gmail.com" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="password" class="form-control" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Confirm Password </label>
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
          <label for="name" class="col-sm-2 control-label"> Band Name </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John and Friends" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Description </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" placeholder="John and Friends plays cool music." />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Porch Location </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input id="autocomplete" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Available Times </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
          <?php 
            $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '1' ORDER BY StartTime;";

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
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Band Member Emails </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                    <div id="dynamicInput"> Band Member 1 <br>
                      <input type="email" class="form-control" name="myInputs[]" placeholder="friend@gmail.com">
                    </div>
                  <input type="button" value="Add another band member" onClick="addInput('dynamicInput');">
                  </div>
              </div>
          </div>
      </div>
      <button type="button" class="btn btn-primary btn-sm"> Submit </button>
    </form>

  </div> <!-- end container div -->

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

</body>
  <script src="../js/addMembers.js"></script>
</html>