<?php 
# This page is where a user can edit their account information
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <title>PorchFest - My Profile</title>
</head>
<body>
  <div id="editalert"></div>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM users WHERE UserID = '" . $_SESSION['logged_user'] . "'";

    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Variables for server side validation
    $nameError = $emailError = $mobileError = $passwordError = $confirmPasswordError = "";

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
    }

    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['password']) && isset($_POST['confirmPassword']) && $name != '' && $email != '' && $mobile != '' && $password != '' && $confirmPassword != '') {
        if ($password != $confirmPassword) {
          echo '<script> $("#editalert").html(\'<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> The passwords you entered do not match! Please try again. </div>\'); </script>';
        } elseif(strlen($password) < 5) {
          echo '<script> $("#editalert").html(\'<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Your password must be at least five characters long. Please try again. </div>\'); </script>';
        } else {
          echo '<script> $("#editalert").html(\'<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your profile was updated successfuly!. </div>\'); </script>';
          $sql = "UPDATE users SET Name='" . $_POST['name'] . "', Email='" . $_POST['email'] . "', Password='" . $_POST['password'] . "', ContactInfo='" . $_POST['mobile'] . "' WHERE UserID='" . $_SESSION['logged_user'] . "'";
          $result = $conn->query($sql);
        }
      }



  ?>
<!-- navBar and login -->
<?php require_once "../php/modules/login.php"; ?>
<?php require_once "../php/modules/navigation.php"; ?>

<div class="container" style="padding-top: 20px; text-align: center;">
  <h1 class="page-header">Dashboard</h1>
    <div class="row" style="font-size: 20px;">
      <div class="col-sm-6" style="background-color:#F2F2F2;">
        <h2>Manage Porchfests</h2>
          </br>
            
            <form method="post" action="">
              <select name="selected_porchfest" id="selected_porchfest" onchange='if(this.value != 0) { this.form.submit(); }'>
                <option value="0">--Manage Porchfests--</option>
              <p>
              <?php   
                // Query to get all porchfests for the current logged in user              
                $sql = "SELECT * 
                        FROM porchfests
                        INNER JOIN userstoporchfests ON userstoporchfests.PorchfestID = porchfests.PorchfestID
                        WHERE UserID = '" . $_SESSION['logged_user'] . "'";

                $result = $conn->query($sql);

                // Add each porchfest where you are the organizer to the table
                while($porchfest = $result->fetch_assoc()) { 
                  echo '<option value="' . strtolower($porchfest['Nickname']) .'">' . ucwords($porchfest['Nickname']) . ' Porchfest' . '</option>';
                }
                echo '</select>';
              ?>
              <?php
              if ( isset($_POST['selected_porchfest']) ) {
                create_hyperlink('edit/' . $selectedPorchfest, '<button type="button" class="glyphicon glyphicon-pencil"/></button>');
                $selectedPorchfest = $_POST["selected_porchfest"];
                echo '<script type="text/javascript">document.getElementById("selected_porchfest").value ="' . $selectedPorchfest . '"</script>';
              }
              ?>
            </form>
          </p>

          <p><?php create_hyperlink(BROWSE_PORCHFEST_URL, 'Browse All Porchfests'); ?></p>
          <p><?php create_hyperlink(MY_PORCHFEST_URL, 'View My Porchfests'); ?></p>
          <p><?php create_hyperlink(INDEX_URL, 'Create New Porchfest');?></p>
        
      </div>
      <div class="col-sm-6" style="background-color:#E6E9E9;">
        <h2>Manage Account</h2>
        </br>
        <p><?php create_hyperlink(PROFILE_URL, 'Edit Profile Information'); ?></p>
      </div>
    </div>
    </div>
  </div>
</div>

</body>
</html>


