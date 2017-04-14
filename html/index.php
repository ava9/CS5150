<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <title>PorchFest - Home</title>
</head>

<!-- BEGIN body -->
<body>
<?php 
  if (isset($_POST['submitInfo'])) {
    if (!isset($_SESSION['logged_user'])) {
      $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
      $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
      $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_STRING);
      $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
      $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
    }
    $porchfestName = filter_var($_POST['porchfestName'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $deadline = filter_var($_POST['deadline'], FILTER_SANITIZE_STRING);

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

    // handle new porchfest Logic
    if (isset($_POST['porchfestName']) && isset($_POST['description']) && isset($_POST['location']) && isset($_POST['date']) && isset($_POST['deadline']) && $porchfestName != '' && $description != '' && $location != '' && $date != '' && $deadline != '') {
      require_once('../php/config.php');
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      $prep = $mysqli->prepare("INSERT INTO porchfests (Name, Location, Date, Description, Deadline) VALUES (?,?,?,?,?)");
      $prep->bind_param("sssss", $porchfestName, $location, $date, $description, $deadline);
      $prep->execute();
      if ($prep->affected_rows) {
        echo "<script type='text/javascript'>alert('The porchfest, $porchfestName, has been added successfully!.');</script>";
      } else {
        echo "<script type='text/javascript'>alert('Something went wrong...');</script>";
      }

      $sql = "SELECT PorchfestID FROM porchfests ORDER BY PorchfestID DESC LIMIT 1";
      $result = $mysqli->query($sql);
      $porchfestID = $result->fetch_assoc();

      if (!isset($_SESSION['logged_user'])) {
        $sql = "SELECT UserID FROM users ORDER BY UserID DESC LIMIT 1";
        $result = $mysqli->query($sql);
        $userID = $result->fetch_assoc();
      }
      else {
        $userID = $_SESSION['logged_user'];
      }

      $prep = $mysqli->prepare("INSERT INTO userstoporchfests (UserID, PorchfestID) VALUES (?,?)");
      $prep->bind_param("ss", $userID, $porchfestID['PorchfestID']);
      $prep->execute();
    }
  } 
?>
  <div class="container"> 
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>
    
    <div class="row">
      <h1 style="text-align:center;"> Create a Porchfest Website </h1>
    </div>

    <h4 style="text-align:center;"> This website helps you create a simple Porchfest website to get started. <br> Attendees will be able to view all Porchfest information which you can also manage. <br> If you would like to create a Porchfest website, please fill out the form below. 
    </h4>

    <?php if (!isset($_SESSION['logged_user'])) { ?>
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#loginModal">
      Already have an account?
    </button>
    <?php } ?>

    <form role="form" class="form-horizontal" id='submit-info-form' method='POST' action='index.php'>
      <?php if (!isset($_SESSION['logged_user'])) { ?>
      <h4> Account Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Your Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="text" class="form-control" name="name" placeholder="John Doe" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Your Email</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="email" class="form-control" name="email" placeholder="johndoe@gmail.com" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Mobile</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="tel" class="form-control" name="mobile" placeholder="(123) 456-7891" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="password" name="password" class="form-control" placeholder="Password" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Confirm Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="password" name="confirmPassword" class="form-control" placeholder="Password" />
                  </div>
              </div>
          </div>
      </div>
      <br>
      <?php } ?>
      <a href="./existingporchfest"> Already have an existing Porchfest website? </a>
      <h4> Porchfest Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Porchfest Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="text" name="porchfestName" class="form-control" placeholder="Ithaca Porchfest" />
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
                      <input required type="text" class="form-control" name="description" placeholder="John and Friends plays cool music." />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Location </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required id="autocomplete" name="location" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Date </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="date" name="date" class="form-control" placeholder="Date" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label"> Sign-up Deadline </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input required type="datetime-local" name="deadline" lass="form-control" placeholder="Deadline" />
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