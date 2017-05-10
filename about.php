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
    require_once CODE_ROOT . "/php/modules/stdHead.php";?>
    <title>PorchFest - About</title>
</head>

<!-- BEGIN body -->
<body>
  <div class="container"> 
    <!-- navBar and login -->
    <?php 
      require_once CODE_ROOT . "/php/modules/login.php";
      require_once CODE_ROOT . "/php/modules/navigation.php";?>
      
    <div class="row">
      <h1 style="text-align:center;"> Welcome to our website! </h1>
    </div>

    <h4 style="text-align:center;"> We are a group of Cornell Students who worked on this website for our Software Engineering course. </h4>

    <center>
      <img src="<?php echo IMG_CORNELL; ?>" style="width:30%;height:30%;">
    </center>

  </div> <!-- end container div -->
</body>
</html>