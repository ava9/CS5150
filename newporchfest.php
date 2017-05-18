<?php 
# This page is for users who do not have an existing porchfest and want to create one
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
    <?php 
      require_once "config.php";
      require_once CODE_ROOT . "/php/modules/stdHead.php"; ?>
    <title>PorchFest - Create New Porchfest</title>
</head>

<!-- BEGIN body -->
<body>
<?php 
  // Variables for server side validation
  $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = "";
  $porchfestNameError = $nicknameError = $descriptionError = $locationError = $dateError = $deadlineError = "";

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
          $result = $mysqli->query("SELECT * FROM users WHERE email = '$email'");
          $row = $result->fetch_row();
          if (empty($row)) {
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
              echo "<script type='text/javascript'>alert('$name, you have been added successfully!');</script>";
            } else {
              echo "<script type='text/javascript'>alert('DB failed to add you!.');</script>";
            }
          } else { 
            echo "<script type='text/javascript'>alert('User already exists!.');</script>";
          }
        }
      }
    }

    /// Create new porchfest from information submitted in form
    if (isset($_POST['porchfestName']) && isset($_POST['nickname']) && isset($_POST['description']) && isset($_POST['location']) && isset($_POST['date']) && isset($_POST['deadline']) && $porchfestName != '' && $nickname != '' && $description != '' && $location != '' && $date != '' && $deadline != '') {
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // First, validate the inputted nickname is unique
      $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE Nickname = '%s'",
                      $mysqli->real_escape_string($nickname));;
      $result = $mysqli->query($sql);
      if ($result->num_rows != 0) {
        echo "<script type='text/javascript'>alert('Cannot create a porchfest with the same nickname!')</script>";
      } else {

        $prep = $mysqli->prepare("INSERT INTO porchfests (Name, Nickname, Location, Date, Description, Deadline) 
                                  VALUES (?,?,?,?,?,?)");
        $prep->bind_param("ssssss", $porchfestName, $nickname, $location, $date, $description, $deadline);
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
        
        $userID = $_SESSION['logged_user'];

        // Insert into userstoporchfests table
        $prep = $mysqli->prepare("INSERT INTO userstoporchfests (UserID, PorchfestID) VALUES (?,?)");
        $prep->bind_param("ss", $userID, $porchfestID['PorchfestID']);
        $prep->execute();
      }
    }
  } 
?>
  <div class="container"> 
    <!-- navBar and login -->
    <?php 
      require_once CODE_ROOT . "/php/modules/login.php";
      require_once CODE_ROOT . "/php/modules/navigation.php";?>
    
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

    <!-- Form for submitting account information -->
    <form role="form" class="form-horizontal" id='submit-info-form' method='POST' action="<?php echo NEW_PORCHFEST_URL; ?>">
      <?php 
        if (!isset($_SESSION['logged_user'])) {
          require_once CODE_ROOT . "/php/modules/forms/accountSignupForm.php";
        } 
        echo "<br>";
        create_hyperlink(EXISTING_PORCHFEST_URL, "Already have an existing Porchfest?");
        require_once CODE_ROOT . "/php/modules/forms/newPorchfestForm.php";
      ?>
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