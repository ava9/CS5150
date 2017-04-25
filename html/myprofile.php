<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <title>PorchFest - My Profile</title>
</head>
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM users WHERE UserID = '" . $_SESSION['logged_user'] . "'";

    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = "";

    if (isset($_POST['submitInfo'])) {
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

    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '') {
        if ($password != $confirmPassword) {
          echo "<script type='text/javascript'>alert('Passwords do not match!');</script>";
        } else {
          $sql = "UPDATE users SET Name='" . $_POST['name'] . "', Email='" . $_POST['email'] . "', Password='" . $_POST['password'] . "', ContactInfo='" . $_POST['mobile'] . "' WHERE UserID='" . $_SESSION['logged_user'] . "'";
          $result = $conn->query($sql);
        }
      }

  ?>
<!-- navBar and login -->
<?php require_once "../php/modules/login.php"; ?>
<?php require_once "../php/modules/navigation.php"; ?>

<div class="container" style="padding-top: 60px; text-align: center;">
  <h1 class="page-header">Edit Profile</h1>
  <div class="row">
    <!-- edit form column -->
    <div class="col-md-8 col-sm-6 col-xs-12 col-centered personal-info">
      <h3>Personal Info</h3>
      <form class="form-horizontal row-centered" role="form">
        <div class="form-group">
          <label class="col-lg-3 control-label">Name:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $user['Name'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $nameError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Email:</label>
          <div class="col-lg-8">
            <input required class="form-control" value=<?php echo '"' . $user['Email'] . '"' ?> type="email"> <?php echo '<span class="error">'; echo $emailError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Mobile:</label>
          <div class="col-lg-8">
            <input required class="form-control" value=<?php echo '"' . $user['ContactInfo'] . '"' ?> type="tel"> <?php echo '<span class="error">'; echo $mobileError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">New Password:</label>
          <div class="col-md-8">
            <input required class="form-control" type="password"> <?php echo '<span class="error">'; echo $passwordError; echo '</span>'; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Confirm New Password:</label>
          <div class="col-md-8">
            <input required class="form-control" type="password"> <?php echo '<span class="error">'; echo $confirmPasswordError; echo '</span>'; ?>
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


