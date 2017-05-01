<?php 
# This page is where users can see all the porchfests and click to join or view the porchfest.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <!-- Responsive table js -->
  <script src="../js/responsive-tables.js"></script>
  <!-- Responsive tables CSS -->
  <link rel="stylesheet" href="./css/responsive-tables.css">
  <title>PorchFest - My Account</title>
</head>

<body>
<!-- navBar and login -->
<?php require_once "../php/modules/navigation.php"; ?>
<?php require_once "../php/modules/login.php"; ?>


<div class="container"> <!-- begin container div -->
  <div class="row"> <!-- begin row 1 -->
    <h2 class="text-center" > Browse Porchfests </h2>
  </div> <!-- end row 1 -->
  <div class="row"> <!-- begin row 2 -->
    <div class="panel panel-default"> <!-- begin panel div -->
      <div class="panel-body"> <!-- begin panel-body div -->
        <div class="table-container table-responsive"> <!-- begin table-container div -->
          <div class="col-xs-9 col-xs-offset-3 col-sm-4 col-sm-offset-8 col-md-3 col-md-offset-9"> <!-- begin col 1 div -->
            <div class="btn-group"> <!-- buttons for filtering porchfests -->
              <button type="button" class="btn btn-success btn-filter" data-target="upcoming"> Upcoming </button>
              <button type="button" class="btn btn-warning btn-filter" data-target="past"> Past </button>
              <button type="button" class="btn btn-info btn-filter" data-target="all"> All </button>
            </div>
          </div> <!-- end col 1 div -->
          <div class="col-md-12"> <!-- begin col 2 div -->
            <table class="responsive table">
              <tr data-status= "fixed"> <!-- headings for table -->
                <th> Name </th>
                <th> Date </th>
                <th> Location </th>
                <th> Description </th>
                <th> Sign-up Deadline </th>
                <th> Want to Perform </th>
              </tr>
              <?php
                require_once "../php/config.php";
                // Create database connection
                $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

                // Select all porchfests
                $sql = "SELECT * FROM porchfests ORDER BY Name";
                $result = $conn->query($sql);

                // Display table data for porchfests
                while($porchfest = $result->fetch_assoc()) {
                  $isPublished = 'No';
                  if ($porchfest['Published'] != 0) {
                    $isPublished = 'Yes';
                  }
                  // Check if the porchfest is in the future or past and label correctly
                  $status = 'upcoming';
                  if (strtotime($porchfest['Date']) < date("Y-m-d")) {
                    $status = 'past';
                  }
                  $day = date_format(date_create($porchfest['Date']), 'F j, Y');
                  $deadline = date_format(date_create($porchfest['Deadline']), 'g:iA \o\n F j, Y');

                  $href = '"view/' . strtolower($porchfest['Nickname']);
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
                        <td>' . $deadline . '</td>
                        <td>  
                          <a href="bandsignup/' . strtolower($porchfest['Nickname']) . '"> Join </a>
                        </td>
                      </tr>';
                }
              ?>
            </table>
          </div> <!-- end col 2 div -->
        </div> <!-- end table-container div -->
      </div> <!-- end panel-body div -->
    </div> <!-- begin panel div -->
  </div> <!-- begin row 2 -->
</div> <!-- end container div -->

  <!-- JavaScript to make the filtering of the porchfests work -->
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

     });
  </script>

</body>
</html>


