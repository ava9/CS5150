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
            <input required class="form-control" value=<?php echo '"' . $user['Name'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Email:</label>
          <div class="col-lg-8">
            <input required class="form-control" value=<?php echo '"' . $user['Email'] . '"' ?> type="email">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Mobile:</label>
          <div class="col-lg-8">
            <input required class="form-control" value=<?php echo '"' . $user['ContactInfo'] . '"' ?> type="tel">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">New Password:</label>
          <div class="col-md-8">
            <input required class="form-control" type="password">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Confirm New Password:</label>
          <div class="col-md-8">
            <input required class="form-control" type="password">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label"></label>
          <div class="col-md-8">
            <input class="btn btn-primary" value="Save Changes" type="submit">
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


