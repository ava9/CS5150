<?php 
# This page is an about page for our website describing how it came to be
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

    <h4 style="text-align:center;"> We are a group of Cornell students who completed this website as our project for CS5150: Software Engineering. We worked with Robbert Van Renesse, Lesley Greene, Gretchen Hildreth, and Andy Adelewitz to design the website and its features. We built this website to support scheduling and managing instances of Porchfests across various communities. The website is supports the following functionality:</h4>

    <h4>
      <ul>
        <li>Registering as an organizer and creating a new porchfest event with date, time slots, and location</li>
        <li>Registering as a band and signing up to play at an existing porchfest</li> 
        <li>Integrating an existing porchfest website within our framework</li>
        <li>Generating a feasible schedule through the use of our scheduling algorithm</li>
        <li>Generating a map-view of the schedule with pins colored by time slot showing the exact geo-location where a band will be playing</li>
        <li>Manually editing the schedule to move bands from one timeslot to another, if not fully satisfied with automatically generated schedule</li>
        <li>Publishing the schedule for attendees to view</li>
      </ul>
    </h4>

    <p style="padding-top: 50px">-Aditi Jain, Aditya, Agashe, Candy Xiao, Carlos Mendez, Rohit Biswas, Kevin Hui, Alan Wu, Cornell '17 </p>

    <img src="<?php echo IMG_CORNELL; ?>" align="right" style="width:10%;height:10%;">
    

  </div> <!-- end container div -->
</body>
</html>