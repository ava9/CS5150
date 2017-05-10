<?php 
# This page is where a user can edit their account information
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
    require_once "config.php";
    require_once CODE_ROOT . "/php/modules/stdHead.php";
  ?>
  <title>PorchFest - My Profile</title>
</head>
<body>
  <div id="editalert"></div>
  <?php
    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM users WHERE UserID = '" . $_SESSION['logged_user'] . "'";

    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Variables for server side validation
    $nameError = $emailError = $mobileError = $passwordError = $oldPasswordError = $confirmPasswordError = ""; 

    // Check if the form was submitted
    if (isset($_POST['submitInfo'])) {
      // Makes sure that required fields are filled out
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
        if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $mobile)) {
          $mobileError = 'Invalid mobile number';
        }
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
      if (empty($_POST['currPassword'])) {
        $oldPasswordError = 'Missing';
      }
      else {
        $currPassword = filter_var($_POST['currPassword'], FILTER_SANITIZE_STRING);
      }
      
    }

    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '' && $currPassword != '') {
        $sql1 = "SELECT Password FROM users WHERE UserID=" . $_SESSION['logged_user'];
        $result = $conn->query($sql1);

        $dbpass = $result->fetch_assoc()['Password'];

        $encpassword = hash("sha256", ($currPassword . SALT));

        $newpass = hash("sha256", ($password . SALT));


        if ($password != $confirmPassword) {
          echo '<script> $("#editalert").html(\'<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> The passwords you entered do not match! Please try again. </div>\'); </script>';
        } elseif(strlen($password) < 5) {
          echo '<script> $("#editalert").html(\'<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Your password must be at least five characters long. Please try again. </div>\'); </script>';
        } elseif($dbpass != $encpassword) {
          echo '<script> $("#editalert").html(\'<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> The password you entered does not match your old password! Please try again. </div>\'); </script>';
        } else {
          echo '<script> $("#editalert").html(\'<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your profile was updated successfully!. </div>\'); </script>';
          $sql = "UPDATE users SET Name='" . $_POST['name'] . "', Email='" . $_POST['email'] . "', Password='" . $newpass . "', ContactInfo='" . $_POST['mobile'] . "' WHERE UserID='" . $_SESSION['logged_user'] . "'";
          $result = $conn->query($sql);
        }
      }
  ?>
<!-- navBar and login -->
<?php 
  require_once CODE_ROOT . "/php/modules/login.php";
  require_once CODE_ROOT . "/php/modules/navigation.php";?>


<div class="container" style="padding-top: 60px; text-align: center;">
  <h1 class="page-header">Edit Profile</h1>
  <div class="row">
    <!-- Form for editing account information with current information from database displayed -->
    <div class="col-md-8 col-sm-6 col-xs-12 col-centered personal-info">
      <h3>Personal Info</h3>
      <form class="form-horizontal row-centered" role="form" method="post">
        <div class="form-group">
          <label class="col-lg-3 control-label">Name:</label>
          <div class="col-lg-8">
            <input class="form-control" data-validation="alphanumeric" data-validation-allowing="-_ " data-validation="length" data-validation-length="min1" name="name" value=<?php echo '"' . $user['Name'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $nameError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Email:</label>
          <div class="col-lg-8">
            <input data-validation="email" required class="form-control" name="email" value=<?php echo '"' . $user['Email'] . '"' ?> type="email"> <?php echo '<span class="error">'; echo $emailError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Mobile (xxx-xxx-xxxx):</label>
          <div class="col-lg-8">
            <input required data-validation="custom" data-validation-regexp="^[0-9]{3}-[0-9]{3}-[0-9]{4}$" data-validation-help="Please format the number as xxx-xxx-xxxx" class="form-control" name="mobile" value=<?php echo '"' . $user['ContactInfo'] . '"' ?> type="tel"> <?php echo '<span class="error">'; echo $mobileError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Current Password:</label>
          <div class="col-md-8">
            <input required data-validation="length" data-validation-length="min5" class="form-control" name="currPassword" type="password"> <?php echo '<span class="error">'; echo $oldPasswordError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">New Password:</label>
          <div class="col-md-8">
            <input required data-validation="length" data-validation-length="min5" class="form-control" name="password" type="password"> <?php echo '<span class="error">'; echo $passwordError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Confirm New Password:</label>
          <div class="col-md-8">
            <input required data-validation="length" data-validation-length="min5" class="form-control" name="confirmPassword" type="password"> <?php echo '<span class="error">'; echo $confirmPasswordError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label"></label>
          <div class="col-md-8">
            <input class="btn btn-primary" name="submitInfo" value="Save Changes" type="submit">
            <span></span>
            <input class="btn btn-default" value="Cancel" type="reset">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<script type='text/javascript'>
  $('body').click(function() {
        $("#editalert").html('');
  });

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
</script>

</body>
</html>


