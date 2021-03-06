<?php 
# This page is where users can see all the porchfests and click to join or view the porchfest.
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
  <!-- Responsive table js -->
  <script src="<?php echo JS_RESPONSIVE_TABLES_LINK;?>"></script>
  <!-- Responsive tables CSS -->
  <link rel="stylesheet" href="<?php echo CSS_RESPONSIVE_TABLES_LINK;?>">
  <title>PorchFest - My Account</title>
</head>

<!-- BEGIN body -->
<body>
<!-- navBar and login -->
<?php require_once CODE_ROOT."/php/modules/navigation.php"; ?>
<?php require_once CODE_ROOT."/php/modules/login.php"; ?>


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
                // Create database connection
                $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                date_default_timezone_set('America/New_York');

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
                  $porchfestDate = new DateTime($porchfest['Date']);
                  if ($porchfestDate->format("Y-m-d") < date("Y-m-d")) {
                    $status = 'past';
                  }
                  $day = date_format(date_create($porchfest['Date']), 'F j, Y');
                  $deadline = date_format(date_create($porchfest['Deadline']), 'g:iA \o\n F j, Y');

                  // Create a link for the porchfest to view information
                  // SPECIAL CASE: If there is a provided link by the porchfest organizer,
                  // create the option to go to either the link or our porchfest viewer.
                  $href = '<a href="view/' . strtolower($porchfest['Nickname']) . '">' . $porchfest['Name'] . '</a>';
                  if ($porchfest['URL'] != '') {
                    $href = '<a href="#" data-toggle="modal" data-target="#htmlModal' . $porchfest['Name'] . '">'
                            . $porchfest['Name'] . '</a>';
                    echo 
                    '<div class="modal fade" id="htmlModal' . $porchfest['Name'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header"><h3>'
                          . $porchfest['Name'] .
                          '</h3></div>
                          <div class="modal-body">
                            <p>
                              This Porchfest has its own website! You can go to it, or view the information
                              about it on our own Porchfest view.
                            </p>
                            <br>
                            <a href="' . $porchfest['URL'] . '">Go to the dedicated website</a>
                            <br>                            
                            <a href="view/' . $porchfest['Name'] . '">Continue to our view</a>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>';
                  }
                  echo '<tr data-status = "' . $status . '">
                        <td>' . $href . '</td>
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


