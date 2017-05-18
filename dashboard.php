<?php 
# This page is where a user can edit their account information
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
  <title>PorchFest - My Profile</title>
</head>

<!-- BEGIN body -->
<body>
  <div id="editalert"></div>
<?php
  // navBar and login and routing
  require_once CODE_ROOT . "/php/modules/login.php";
  require_once CODE_ROOT . "/php/modules/navigation.php";
  require_once CODE_ROOT . "/php/modules/routing.php"; ?>

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
                // Create DB connection 
                $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);  

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
                $selectedPorchfest = $_POST["selected_porchfest"];
                create_hyperlink('edit/' . $selectedPorchfest, '<button type="button" class="glyphicon glyphicon-pencil"/></button>');
                echo '<script type="text/javascript">document.getElementById("selected_porchfest").value ="' . $selectedPorchfest . '"</script>';
              }
              ?>
            </form>
          </p>

          <p><?php create_hyperlink(BROWSE_PORCHFEST_URL, 'Browse All Porchfests'); ?></p>
          <p><?php create_hyperlink(MY_PORCHFEST_URL, 'View My Porchfests'); ?></p>
          <p><?php create_hyperlink(NEW_PORCHFEST_URL, 'Create New Porchfest');?></p>

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


