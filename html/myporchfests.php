<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <!-- Responsive table js -->
  <script src="../js/responsive-tables.js"></script>

  <!-- Responsive tables CSS -->
  <link rel="stylesheet" href="css/responsive-tables.css">
</head>
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  ?>

  <?php require_once "../php/modules/login.php"; ?>
  <?php require_once "../php/modules/navigation.php"; ?>


<div class="container"> <!-- begin container div -->
  <div class="row"> <!-- begin row 1 -->
    <h2 class="text-center" > My Porchfests </h2>
  </div> <!-- end row 1 -->
  <div class="row"> <!-- begin row 2 -->
    <div class="panel panel-default"> <!-- begin panel div -->
      <div class="panel-body"> <!-- begin panel-body div -->
        <div class="table-container table-responsive"> <!-- begin table-container div -->

          <!-- Trigger the modal with a button -->
          <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="btn-group">
              <a href = "http://localhost/cs5150/html/"><button type="button" class="btn btn-primary">Create Porchfest</button></a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-md-3 col-md-offset-6"> <!-- begin col 1 div -->
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-filter" data-target="upcoming"> Upcoming </button>
              <button type="button" class="btn btn-warning btn-filter" data-target="past"> Past </button>
              <button type="button" class="btn btn-info btn-filter" data-target="all"> All </button>
            </div>
          </div> <!-- end col 1 div -->
          <div class="col-md-12"> <!-- begin col 2 div -->
            <table class="responsive table">
              <tr data-status= "fixed">
                <th> Name </th>
                <th> Date </th>
                <th> Location </th>
                <th> Description </th>
                <th> Role </th>
                <th> Sign-up Deadline </th>
                <th> Published </th>
                <th> Manage </th>
              </tr>
              <?php                 
                  $sql = "SELECT * 
                          FROM porchfests
                          INNER JOIN userstoporchfests ON userstoporchfests.PorchfestID = porchfests.PorchfestID
                          WHERE UserID = '" . $_SESSION['logged_user'] . "'";

                  $result = $conn->query($sql);

                  // Add each porchfest where you are the organizer to the table
                  while($porchfest = $result->fetch_assoc()) {
                    // Set published 
                    $isPublished = 'No';
                    if ($porchfest['Published'] != 0) {
                      $isPublished = 'Yes';
                    }

                    // Set status for past/upcoming filtering
                    $status = 'upcoming';
                    if (strtotime($porchfest['Date']) < date("Y-m-d")) {
                      $status = 'past';
                    }
                    $day = date_format(date_create($porchfest['Date']), 'F j, Y');
                    $deadline = date_format(date_create($porchfest['Deadline']), 'g:iA \o\n F j, Y');

                    // Set the URL to link to. Should one not exist (not provided), then link to
                    // website's single porchfest view
                    $href = '"singleporchfest/' . strtolower($porchfest['Nickname']);
                    if ($porchfest['URL'] != '') {
                      $href = '"' . $porchfest['URL'];
                    }
                    // HTML code for table row
                    echo '<tr data-status = "' . $status . '">
                          <td> 
                            <a href=' . $href . '">' . $porchfest['Name'] . '</a>
                          </td>
                          <td>' . $day . '</td>
                          <td>' . $porchfest['Location'] . '</td>
                          <td>' . $porchfest['Description'] . '</td>
                          <td> Organizer </td>
                          <td>' . $deadline . '</td>
                          <td>' . $isPublished . '</td>
                          <td> <a href="edit/' . strtolower($porchfest['Name']) . '"> Edit Porchfest </a> </td>
                        </tr>';
                  }

                  $sql = "SELECT * 
                          FROM porchfests
                          INNER JOIN bandstoporchfests ON bandstoporchfests.PorchfestID = porchfests.PorchfestID
                          INNER JOIN userstobands ON userstobands.BandID = bandstoporchfests.BandID
                          WHERE UserID = '" . $_SESSION['logged_user'] . "'";

                  $result = $conn->query($sql);

                  // Add each porchfest where you are a performer to the table
                  while($porchfest = $result->fetch_assoc()) {
                    $sql2 = "SELECT * FROM bands where BandID = '" . $porchfest['BandID'] . "'";
                    $result2 = $conn->query($sql2);
                    $bandname = $result2->fetch_assoc()['Name'];

                    // Set published 
                    $isPublished = 'No';
                    if ($porchfest['Published'] != 0) {
                      $isPublished = 'Yes';
                    }

                    // Set status for past/upcoming filtering
                    $status = 'upcoming';
                    if (strtotime($porchfest['Date']) < date("Y-m-d")) {
                      $status = 'past';
                    }
                    $day = date_format(date_create($porchfest['Date']), 'F j, Y');
                    $deadline = date_format(date_create($porchfest['Deadline']), 'g:iA \o\n F j, Y');

                    // Set the URL to link to. Should one not exist (not provided), then link to
                    // website's single porchfest view
                    $href = '"singleporchfest/' . strtolower($porchfest['Name']);
                    if ($porchfest['URL'] != '') {
                      $href = '"' . $porchfest['URL'];
                    }
                    echo '<tr data-status = "' . $status . '">
                          <td> 
                            <a href=' . $href . '">' . $porchfest['Name'] . '</a>
                          </td>
                          <td>' . $day . '</td>
                          <td>' . $porchfest['Location'] . '</td>
                          <td>' . $porchfest['Description'] . '</td>
                          <td> Performer (' . $bandname . ') </td>
                          <td>' . $deadline . '</td>
                          <td>' . $isPublished . '</td>
                          <td> <a href="edit/' . strtolower($porchfest['Name']) . '/' . $bandname . '"> Edit Band </a> </td>
                        </tr>';
                  }
              ?>
            </table>
          </div> <!-- end col 2 div -->
        </div> <!-- end table-container div -->
      </div> <!-- end panel-body div -->
    </div> <!-- end panel div -->
  </div> <!-- end row 2 -->
</div> <!-- end container div -->


<script type="text/javascript">
$(document).ready(function () {

  $('.star').on('click', function () {
      $(this).toggleClass('star-checked');
    });

    $('.ckbox label').on('click', function () {
      $(this).parents('tr').toggleClass('selected');
    });

    $('.btn-filter').on('click', function () {
      var $target = $(this).data('target');
      if ($target != 'all') {
        $('.table tr').css('display', 'none');
        $('.table tr[data-status="' + $target + '"]').fadeIn('slow');
        $('.table tr[data-status="fixed"]').fadeIn('slow');
      } else {
        $('.table tr').css('display', 'none').fadeIn('slow');
      }
    });

 }); </script>

</body>
</html>


