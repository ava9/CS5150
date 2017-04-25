<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php require_once "../php/modules/stdHead.php" ?>  
  <link rel="stylesheet" href="/cs5150/php/modules/token-input-facebook.css" type="text/css" />


  <title>Band Sign-Up</title>
</head>

<!-- BEGIN body -->
<body>

  <div class="container"> <!-- Container div -->
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>
    <?php require_once "../php/routing.php"; ?>

    <?php
  // input a string: address (i.e. "114 Summit Ave. Ithaca, NY 14850"
  // output is a latitude, longitude coordinate pair (i.e. 42.442064,-76.483469)
  function getCoordinates($address){
    
    // replace white space with "+" sign (match google search pattern)
    $address = str_replace(" ", "+", $address); 
    
    $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
     
    $response = file_get_contents($url);
     
    $json = json_decode($response,TRUE); //array object from the web response
     
    return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng'].",0.0");

  }
  $bandnameError = $descriptionError = $locationError = "";

  if (isset($_POST['submitInfo'])) {
    if (!isset($_SESSION['logged_user'])) {
      if (empty($_POST['name'])) {
        $nameError = 'Missing';
      }
      else {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['email'])) {
        $emailError = 'Missing';
      }
      else {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $emailError = "Invalid email format"; 
            }
      }
      if (empty($_POST['mobile'])) {
        $mobileError = 'Missing';
      }
      else {
        $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['password'])) {
        $passwordError = 'Missing';
      }
      else {
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['confirmPassword'])) {
        $confirmPasswordError = 'Missing';
      }
      else {
        $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
      }
    }
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
    $bandmembers = htmlentities($_POST['bandmembers']);
    $bandcomment = htmlentities($_POST['bandcomment']);
    $bandconflicts = htmlentities($_POST['bandconflicts']);
    if (empty($_POST['porchlocation'])) {
      $locationError = 'Missing';
    }
    else {
      $porchlocation = htmlentities($_POST['porchlocation']);
    }

    // handle new user logic
    if (!isset($_SESSION['logged_user'])) {
      if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '') {
        
        require_once('../php/config.php');
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($password != $confirmPassword) {
          echo "<script type='text/javascript'>alert('Passwords do not match!');</script>";
        } else {
          $result = $mysqli->query("SELECT * FROM users WHERE email = '$email'");
          $row = $result->fetch_row();
          if (empty($row)) {
            $prep = $mysqli->prepare("INSERT INTO users (Email, Password, Name, ContactInfo) VALUES (?,?,?,?)");
            $prep->bind_param("ssss", $email, $password, $name, $mobile);
            $prep->execute();
            if ($prep->affected_rows) {
              echo "<script type='text/javascript'>alert('$name, you have been added successfully!.');</script>";
            } else {
              echo "<script type='text/javascript'>alert('DB failed to add you!.');</script>";
            }
          } else { 
            echo "<script type='text/javascript'>alert('User already exists!.');</script>";
          }
        }
      }
    }

    $latlong = explode(',', getCoordinates($porchlocation));
    $lat = $latlong[0];
    $long = $latlong[1];

    // Insert into bands
    $prep = $mysqli->prepare("INSERT INTO bands (Name, Description, Members, Comment, Conflicts) 
                              VALUES (?,?,?,?,?)");
    $prep->bind_param("sssss", $bandname, $banddescription, $bandmembers, $bandcomment, $bandconflicts);
    $prep->execute();

    // Insert into IDs
    $sql = "SELECT BandID FROM bands ORDER BY BandID DESC LIMIT 1";
    $result = $conn->query($sql);
    $bandID = $result->fetch_assoc()['BandID'];

    $prep = $mysqli->prepare("INSERT INTO bandstoporchfests (PorchfestID, BandID, PorchLocation, Latitude, Longitude) 
                              VALUES (?,?,?,?,?)");
    $prep->bind_param("sssss", $porchfestID, $bandID, $porchlocation, $lat, $long);
    $prep->execute();

    if (!isset($_SESSION['logged_user'])) {
      $sql = "SELECT UserID FROM users ORDER BY UserID DESC LIMIT 1";
      $result = $mysqli->query($sql);
      $userID = $result->fetch_assoc()['UserID'];
    }
    else {
      $userID = $_SESSION['logged_user'];
    }

    $prep = $mysqli->prepare("INSERT INTO userstobands (UserID, BandID) VALUES (?,?)");
    $prep->bind_param("ss", $userID, $bandID);
    $prep->execute();

    // Insert into bandavailabletimes
    if (isset($_POST['available'])) {
      $available = $_POST['available'];
      foreach($available as $timeslot) {
        $prep = $mysqli->prepare("INSERT INTO bandavailabletimes (BandID, TimeslotID)
                                  VALUES (?,?)");
        $prep->bind_param("ss", $bandID, $timeslot);
        $prep->execute();
      }
    }

    if ($prep->affected_rows) {
        echo "<script type='text/javascript'>alert('The band, $bandname, has been added successfully!.');</script>";
    } else {
      echo "<script type='text/javascript'>alert('Something went wrong...');</script>";
    }
  }
?>



    <?php $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = ""; ?>


    
    <div class="row">
      <h1 style="text-align:center;"> 
        Sign-up to perform for <?php echo PORCHFEST_NAME; ?>
      </h1>
    </div>

    <h4 style="text-align:center;"> If you would like to perform for <?php echo PORCHFEST_NAME; ?>,
      please fill out the form below. 
    </h4>

    <?php if (!isset($_SESSION['logged_user'])) { ?>
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#loginModal">
      Already have an account?
    </button>
    <?php } ?>

    <form role="form" class="form-horizontal" id='submit-info-form' method='POST'>
      <?php if (!isset($_SESSION['logged_user'])) { ?>
      <h4> Account Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Your Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="text" class="form-control" name="name" placeholder="John Doe" /> <?php echo '<span class="error">'; echo $nameError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Your Email</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="email" class="form-control" name="email" placeholder="johndoe@gmail.com" /> <?php echo '<span class="error">'; echo $emailError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Mobile</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="tel" class="form-control" name="mobile" placeholder="(123) 456-7891" /> <?php echo '<span class="error">'; echo $mobileError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="password" name="password" class="form-control" placeholder="Password" /> <?php echo '<span class="error">'; echo $passwordError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Confirm Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="password" name="confirmPassword" class="form-control" placeholder="Password" /> <?php echo '<span class="error">'; echo $confirmPasswordError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <br>
      <?php } ?>
      <br>
      <h4> Band Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Band Name </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required name="bandname" type="text" class="form-control" placeholder="John and Friends" /> <?php echo '<span class="error">'; echo $bandnameError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Description </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required name="banddescription" type="text" class="form-control" placeholder="John and Friends plays cool music." /> <?php echo '<span class="error">'; echo $descriptionError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Porch Location </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required name="porchlocation" id="autocomplete" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input> <?php echo '<span class="error">'; echo $locationError; echo '</span>'; ?>
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
            require_once "../php/config.php";
            // Create connection
            $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE porchfests.Nickname = '%s'", PORCHFEST_NICKNAME);
            $result = $conn->query($sql);
            $porchfestID = $result->fetch_assoc()['PorchfestID'];

            $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $porchfestID . "' ORDER BY StartTime;";

            $result = $conn->query($sql);
            while($timeslot = $result->fetch_assoc()) {
              $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
              $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
              $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
              echo "<input name='available[]' type='checkbox' value='" . $timeslot['TimeslotID'] . "' />" . " " . $starttime . 
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
                      <input name="bandmembers" type="text" class="form-control" placeholder="member1@gmail.com,member2@gmail.com,member3@gmail.com" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Conflicting Bands </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input name="bandconflicts" id="conflict-input" type="text" class="form-control" placeholder="Band1,Band2,Band3" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Comments </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input name="bandcomment" type="text" class="form-control" placeholder="Any additional comments" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2"></label>
        <div class="col-sm-10">
            <div class="row">
                <div class="col-md-9">
                  <button type="submit" name="submitInfo" class="btn btn-primary btn-sm"> Submit </button>
                </div>
            </div>
        </div>
      </div>  
    </form>

  </div> <!-- end container div -->

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>


<script type='text/javascript'>
  $(document).ready(function () {
      $("#conflict-input").tokenInput("/cs5150/html/band-listing.php", {
                preventDuplicates: true, theme: "facebook"
            });
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