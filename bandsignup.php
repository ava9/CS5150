<?php 
# This page is where bands can sign up for a specific porchfest.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php 
    require_once "config.php";
    require_once CODE_ROOT . "/php/modules/stdHead.php";
  ?>  
  <link rel="stylesheet" href="<?php echo CSS_TOKEN_INPUT; ?>" type="text/css" />
  <title>Band Sign-Up</title>
</head>

<!-- BEGIN body -->
<body>

  <div class="container"> <!-- Container div -->
    <!-- navBar and login and routing -->
    <?php require_once CODE_ROOT . "/php/modules/login.php"; ?>
    <?php require_once CODE_ROOT . "/php/modules/navigation.php"; ?>
    <?php require_once CODE_ROOT . "/php/modules/routing.php"; ?>

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

      // Create dataabase connection
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Get the porchfestID from the url
      $porchfestID = PORCHFEST_ID;

      // Check if current date is past the deadline
      date_default_timezone_set('America/New_York');
      $sql = sprintf("SELECT * FROM porchfests WHERE PorchfestID='%s'", 
                      $mysqli->real_escape_string($porchfestID));
      $result = $mysqli->query($sql);
      $porchfestDate = new DateTime($result->fetch_assoc()['Deadline']);
      if ($porchfestDate->format("Y-m-d") < date("Y-m-d")) {
        echo '<h1 style="text-align:center;"> 
        Sorry, the deadline for signing up to play for ' . PORCHFEST_NAME . ' has passed.
        </h1>';
      }
      else {

      // Variables for server side validation
      $bandnameError = $descriptionError = $locationError = $urlError = "";

      // Check that the form was submitted
      if (isset($_POST['submitInfo'])) {
        // If the user is not logged in then new account will be created
        if (!isset($_SESSION['logged_user'])) {
          require_once CODE_ROOT . "/php/modules/accountValidation.php";
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

        // Creates a new user if currently not logged in
        if (!isset($_SESSION['logged_user'])) {
          if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '') {
            // Check that the passwords match otherwise create a popup
            if ($password != $confirmPassword) {
              echo "<script type='text/javascript'>alert('Passwords do not match!');</script>";
            } else {
              // Check that the email is not already in the database
              $sql = sprintf("SELECT * FROM users WHERE email = '%s'", $mysqli->real_escape_string($email));
              $result = $mysqli->query($sql);
              $row = $result->fetch_row();
              // If email is unique then insert new user information into database
              if (empty($row)) {
                // hash password
                $password = hash("sha256", ($password . SALT));
                $prep = $mysqli->prepare("INSERT INTO users (Email, Password, Name, ContactInfo) VALUES (?,?,?,?)");
                $prep->bind_param("ssss", $email, $password, $name, $mobile);
                $prep->execute();
                if ($prep->affected_rows) {
                  // Get newest userID that was just created, set it as logged in
                  $sql = "SELECT UserID FROM users ORDER BY UserID DESC LIMIT 1";
                  $result = $mysqli->query($sql);
                  $userID = $result->fetch_assoc()['UserID'];
                  $_SESSION['logged_user'] = $userID;
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

        // Insert into bands table
        $prep = $mysqli->prepare("INSERT INTO bands (Name, Description, Members, Comment, URL) 
                                  VALUES (?,?,?,?,?)");
        $prep->bind_param("sssss", $bandname, $banddescription, $bandmembers, $bandcomment, $bandURL);
        $prep->execute();

        // Get the bandID of the just recently inserted band
        $sql = "SELECT BandID FROM bands ORDER BY BandID DESC LIMIT 1";
        $result = $mysqli->query($sql);
        $bandID = $result->fetch_assoc()['BandID'];

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

        // Insert into bandstoporchfests table
        $sql = "SELECT BandID FROM bands ORDER BY BandID DESC LIMIT 1";
        $result = $mysqli->query($sql);
        $bandID = $result->fetch_assoc()['BandID'];
        $prep = $mysqli->prepare("INSERT INTO bandstoporchfests (PorchfestID, BandID, PorchLocation, Latitude, Longitude) 
                                  VALUES (?,?,?,?,?)");
        $prep->bind_param("sssss", $porchfestID, $bandID, $porchlocation, $lat, $long);
        $prep->execute();

        // Insert into userstobands table
        $userID = $_SESSION['logged_user'];
        
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

        // Popup to show if queries successfully executed
        if ($prep->affected_rows) {
          echo "<script type='text/javascript'>alert('The band, $bandname, has been added successfully!');";
          echo "window.location='" . DASHBOARD_URL . "';</script>"; 
        } else {
          echo "<script type='text/javascript'>alert('Something went wrong...');</script>";
        }
      }
    
      // Variables for server side validation
      $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = ""; 
    ?>
    
    <!-- Form that is displayed to users to signup a band -->
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
    <!-- Form for submitting account information -->
    <form role="form" class="form-horizontal" id='submit-info-form' method='POST'>
      <?php 
        if (!isset($_SESSION['logged_user'])) {
          require_once CODE_ROOT . "/php/modules/forms/accountSignupForm.php";
        } 
        echo "<br>";
        require_once CODE_ROOT . "/php/modules/forms/bandSignupForm.php";
      ?>
    </form>
    <?php } ?>

  </div> <!-- end container div -->

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<script type='text/javascript'>
  // enable form validation
  $.validate({
    lang: 'en',
    modules : 'date'
  });
  
  $(document).ready(function () {
    $("#conflict-input").tokenInput("<?php echo PHP_BAND_LISTING . "?id=" . $porchfestID; ?>", {
        queryParam: "q",
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