<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PorchFest - My Settings</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-purple.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <script src="../js/navbar.es6"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script src="../js/jquery.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM users WHERE UserID = '1'";

    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

  ?>
<script type="text/javascript">writenav();</script>
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
            <input class="form-control" value=<?php echo '"' . $user['Name'] . '"' ?> type="text">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Email:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $user['Email'] . '"' ?> type="email">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Mobile:</label>
          <div class="col-lg-8">
            <input class="form-control" value=<?php echo '"' . $user['ContactInfo'] . '"' ?> type="tel">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">New Password:</label>
          <div class="col-md-8">
            <input class="form-control" type="password">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Confirm New Password:</label>
          <div class="col-md-8">
            <input class="form-control" type="password">
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

</body>
</html>


