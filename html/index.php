<?php 
# This page is for users who do not have an existing porchfest and want to create one
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <title>PorchFest - Home</title>
</head>

<!-- BEGIN body -->
<body>
  <div class="container"> 
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>
    
    <div class="row">
      <h1 style="text-align:center;"> Welcome to the Porchfest Community! </h1>
    </div>

    <h4 style="text-align:center;"> Porchfests are community events in which musicians play for free for their neighbors. 
      An organizer can "create" a new PorchFest, specifying the time slots and area in which the PorchFest will be held and 
      ultimately schedule the bands. Musicians register information about their bands and the locations of their porches. 
      Attendees can find out which bands are playing when and where. </h4>

    <div style="text-align:center;">
      <h4> <?php create_hyperlink(NEW_PORCHFEST_URL, 'Create your Porchfest website here!'); ?>
      <br> 
      Already have a Porchfest website? <?php create_hyperlink(EXISTING_PORCHFEST_URL, 'Click here!'); ?> </h4>    
    </div>

    <center>
      <img src="<?php echo IMG_LANDING; ?>" style="width:50%;height:50%;">
    </center>

  </div> <!-- end container div -->
</body>
</html>