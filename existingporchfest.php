<?php
# This page is for users who already have an existing porchfest website and want to use our scheduling services 
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
  <title>PorchFest - Integrate Porchfest </title>
</head>

<!-- BEGIN body -->
<body>
  <?php
    // Variables for server side validation
    $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = "";
    $porchfestNameError = $nicknameError = $urlError = $descriptionError = $locationError = $dateError = $deadlineError = "";

    // Check if the form was submitted
    if (isset($_POST['submitInfo'])) {
      // If the user is not logged in then new account will be created
      if (!isset($_SESSION['logged_user'])) {
        require_once CODE_ROOT . "/php/modules/accountValidation.php";
      }
      if (empty($_POST['porchfestName'])) {
        $porchfestNameError = 'Missing';
      }
      else {
        $porchfestName = filter_var($_POST['porchfestName'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['nickname'])) {
        $nicknameError = 'Missing';
      }
      else {
        $nickname = filter_var($_POST['nickname'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['porchfestURL'])) {
        $urlError = 'Missing';
      }
      else {
        $porchfestURL = filter_var($_POST['porchfestURL'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['description'])) {
        $descriptionError = 'Missing';
      }
      else {
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['location'])) {
        $locationError = 'Missing';
      }
      else {
        $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['date'])) {
        $dateError = 'Missing';
      }
      else {
        $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
      }
      if (empty($_POST['deadline'])) {
        $deadlineError = 'Missing';
      }
      else {
        $deadline = filter_var($_POST['deadline'], FILTER_SANITIZE_STRING);
      }
    
      // Creates a new user if currently not logged in
      if (!isset($_SESSION['logged_user'])) {
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '') {
          
          $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
          if ($password != $confirmPassword) {
            echo "<script type='text/javascript'>alert('Passwords do not match!');</script>";
          } else {
            $sql = sprintf("SELECT * FROM users WHERE Email = '%s'", $email);
            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            if (empty($row)) {
              $password = hash("sha256", ($password . SALT));
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

      // Create new porchfest from information submitted in form
      if (isset($_POST['porchfestName']) && isset($_POST['nickname']) && isset($_POST['description']) && isset($_POST['location']) && isset($_POST['date']) && isset($_POST['deadline']) && isset($_POST['porchfestURL']) && $porchfestName != '' && $nickname != '' && $description != '' && $location != '' && $date != '' && $deadline != '' && $porchfestURL != '') {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // First, validate the inputted nickname is unique
        $sql = "SELECT PorchfestID FROM porchfests WHERE Nickname = '$nickname'";
        $result = $mysqli->query($sql);
        if ($result->num_rows != 0) {
          echo "<script type='text/javascript'>alert('Cannot create a porchfest with the same nickname!')</script>";
        } else {
          // Insert information into porchfests table
          $prep = $mysqli->prepare("INSERT INTO porchfests (URL, Name, Nickname, Location, Date, Description, Deadline) VALUES (?,?,?,?,?,?,?)");
          $prep->bind_param("sssssss", $porchfestURL, $porchfestName, $nickname, $location, $date, $description, $deadline);
          $prep->execute();
          if ($prep->affected_rows) {
            echo "<script type='text/javascript'>alert('The porchfest, $porchfestName, has been added successfully!.');";
            echo "window.location='" . EDIT_PORCHFEST_URL . "/$nickname';</script>"; 
          } else {
            echo "<script type='text/javascript'>alert('Something went wrong...');</script>";
          }

          // Get newest porchfestID that was just created
          $sql = "SELECT PorchfestID FROM porchfests ORDER BY PorchfestID DESC LIMIT 1";
          $result = $mysqli->query($sql);
          $porchfestID = $result->fetch_assoc();

          // Get newest userID that was just created
          if (!isset($_SESSION['logged_user'])) {
            $sql = "SELECT UserID FROM users ORDER BY UserID DESC LIMIT 1";
            $result = $mysqli->query($sql);
            $userID = $result->fetch_assoc()['UserID'];
          }
          else {
            $userID = $_SESSION['logged_user'];
          }

          // Insert into userstoporchfests table
          $prep = $mysqli->prepare("INSERT INTO userstoporchfests (UserID, PorchfestID) VALUES (?,?)");
          $prep->bind_param("ss", $userID, $porchfestID['PorchfestID']);
          $prep->execute();
        }
      }
    } 
  ?>
  
  <div class="container"> <!-- Container div -->
    <!-- navBar and login -->
  <?php 
    require_once CODE_ROOT . "/php/modules/login.php";
    require_once CODE_ROOT . "/php/modules/navigation.php";?>
    
    <div class="row">
      <h1 style="text-align:center;"> Integrate Your Porchfest Website </h1>
    </div>

    <h4 style="text-align:center;"> This website helps you manage your Porchfest by storing band sign-up information. <br> You can then schedule the performances for your Porchfest using our scheduling algorithm. <br> If you would like to integrate your Porchfest website, please fill out the form below. 
    </h4>

    <?php if (!isset($_SESSION['logged_user'])) { ?>
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#loginModal">
      Already have an account?
    </button>
    <?php } ?>

    <!-- Form for submitting account information --> 
    <form role="form" class="form-horizontal" id="submit-info-form" method="POST" action="<?php echo EXISTING_PORCHFEST_URL; ?>">
      <?php if (!isset($_SESSION['logged_user'])) {
        require_once CODE_ROOT . "/php/modules/accountForm.php";
      } ?>
      <br>
      <?php create_hyperlink(NEW_PORCHFEST_URL, 'Want to create a new Porchfest website?'); ?>
      <h4> Porchfest Information </h4>
      <!-- Form for submitting porchfest information -->
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Porchfest Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input data-validation="length" data-validation-length="min1" required type="text" class="form-control" name="porchfestName" placeholder="Ithaca Porchfest" /> <?php echo '<span class="error">'; echo $porchfestNameError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Nickname <?php tooltip("This is the name that will appear in the url for attendees/musicians. For example, for the Ithaca Porchfest with nickname “ithaca”, the url will appear as porchfest.life/view/ithaca. THIS FIELD CANNOT CHANGE ONCE INPUTTED.") ?> </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input data-validation="length" data-validation-length="min1" required type="text" class="form-control" name="nickname" placeholder="Ithaca Porchfest" /> <?php echo '<span class="error">'; echo $nicknameError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Existing Porchfest Website URL <?php tooltip("If you already have an existing porchfest page, it will be integrated into this porchfest site so that attendees/musicians will be able to view your unique porchfest page and you will be able to use the scheduling and managing features of this site.") ?> </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input data-validation="url" required type="url" class="form-control" name="porchfestURL" placeholder="http://www.porchfest.org" /> <?php echo '<span class="error">'; echo $urlError; echo '</span>'; ?>
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
                      <input required type="text" class="form-control" name="description" placeholder="John and Friends plays cool music." /> <?php echo '<span class="error">'; echo $descriptionError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Location <?php tooltip("The city or town in which the Porchfest event will be held. When typing the location, please select from the resulting dropdown so that the corresponding pin can be placed on a Google map.") ?> </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required id="autocomplete" name="location" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input> <?php echo '<span class="error">'; echo $locationError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Date <?php tooltip("Date when the Porchfest will be held.") ?> </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input data-validation="date" required type="date" class="form-control" name="date" placeholder="Date" /> <?php echo '<span class="error">'; echo $dateError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Sign-up Deadline <?php tooltip("Day by which bands must register to play at this Porchfest event. Default time for the deadline in 11:59pm on this day.") ?> </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input data-validation="date" required type="date" class="form-control" name="deadline" placeholder="Date" /> <?php echo '<span class="error">'; echo $deadlineError; echo '</span>'; ?>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2"></label>
        <div class="col-sm-10">
          <div class="row">
            <div class="col-md-9">
              <button type="submit" name="submitInfo" class="btn btn-primary btn-sm">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div> <!-- end container div -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<!-- JavaScript to not submit the form on enter -->
<script type='text/javascript'>
  // enable form validation
  $.validate({
    lang: 'en',
    modules : 'date'
  });
  
  $(document).ready(function() {
    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
  });

  $(function() {
    $("[name='porchfestName']").blur(function(){
      var val = $(this).val();
      if (!$("[name='nickname']").val()) {
        val = val.replace(/\s+/g, '-').toLowerCase();
        $("[name='nickname']").val(val);
      }
    });
  });

</script>

</body>
</html>