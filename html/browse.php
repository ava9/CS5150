<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PorchFest - My Account</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <!-- Navigation bar -->
  <script src="../js/navbar.es6"></script>

  <!-- Login modal -->
  <script src="../js/loginmodal.es6"></script>

  <!-- Join porchfest modal -->
  <script src="../js/joinporchfestmodal.es6"></script>

  <!-- jQuery -->
  <script src="../js/jquery.js"></script>

  <!-- Responsive table js -->
  <script src="responsive-tables.js"></script>

  <!-- Responsive tables CSS -->
  <link rel="stylesheet" href="responsive-tables.css">
</head>

<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  ?>
  <!-- Write the navigation bar. -->
  <script type="text/javascript">writenav();</script>

  <script type="text/javascript">writeloginmodal();</script>

  <script type="text/javascript">joinporchfestmodal();</script>


<div class="container"> <!-- begin container div -->
  <div class="row"> <!-- begin row 1 -->
    <h2 class="text-center" > Browse Porchfests </h2>
  </div> <!-- end row 1 -->
  <div class="row"> <!-- begin row 2 -->
    <div class="panel panel-default"> <!-- begin panel div -->
      <div class="panel-body"> <!-- begin panel-body div -->
        <div class="table-container table-responsive"> <!-- begin table-container div -->
          <div class="col-xs-9 col-xs-offset-3 col-sm-4 col-sm-offset-8 col-md-3 col-md-offset-9"> <!-- begin col 1 div -->
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
                <th> Sign-up Deadline </th>
                <th> Want to Perform </th>
              </tr>
              <?php
                  $sql = "SELECT * FROM porchfests ORDER BY Name";

                  $result = $conn->query($sql);

                  while($porchfest = $result->fetch_assoc()) {
                    $isPublished = 'No';
                    if ($porchfest['Published'] != 0) {
                      $isPublished = 'Yes';
                    }
                    $status = 'upcoming';
                    if (strtotime($porchfest['Date']) < date("Y-m-d")) {
                      $status = 'past';
                    }

                    echo '<tr data-status = "' . $status . '">
                          <td> 
                            <a href="singleporchfest.php">' . $porchfest['Name'] . '</a>
                          </td>
                          <td>' . $porchfest['Date'] . '</td>
                          <td>' . $porchfest['Location'] . '</td>
                          <td>' . $porchfest['Description'] . '</td>
                          <td>' . $porchfest['Deadline'] . '</td>
                          <td>  
                            <a href="bandsignup.php"> Join </a>
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

  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>


