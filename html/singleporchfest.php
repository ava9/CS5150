<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PorchFest - My Account</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-purple.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <script src="../js/navbar.es6"></script>
  <script src="../js/loginmodal.es6"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script src="../js/jquery.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<!-- BEGIN body -->
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    // add DB_USER and DB_PASSWORD later
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $alph = range('a', 'z');

    echo '<ul id="scrollto">';

    foreach ($alph as $letter) {
      echo '<li><a href="#' . $letter . '">' . strtoupper($letter) . '</a></li>';
    }

    echo '</ul>';

  ?>

  <div class="container" id = "singleporchfestcontainer"> <!-- Container div -->
    <script type="text/javascript">writeloginmodal();</script>

    <div class="row">
      <script type="text/javascript">writenav();</script>
    </div>
    
    <div class="row">
      <h1 style="text-align:center;"> Ithaca Porchfest </h1>
    </div>

    
    <div class="row"> <!-- begin row div -->
      <ul class="nav nav-pills">
        <li class="active"><a href="#name" data-toggle="tab"> Performers </a></li>
        <li><a href="#date" data-toggle="tab"> Schedule </a></li>
        <li><a href="#map" data-toggle="tab"> Map </a></li>
      </ul>
    </div> <!-- end row div -->

    <div class="row"> <!-- begin row div -->
      <div class="tab-content"> <!-- begin tab-content div -->

        <div class="tab-pane fade in active" id="name"> <!-- begin name div -->
          <?php 
            $sql = "SELECT BandID, Name, Description FROM bands ORDER BY Name;";

            $result = $conn->query($sql);

            $lastletter = '';

            while($band = $result->fetch_assoc()) {
              if ($lastletter != substr($band['Name'], 0, 1)) {
                if ($lastletter != '') {
                  echo '</div>';
                }
                echo '<div id = "' . strtolower(substr($band['Name'], 0, 1)) . '">';
                $lastletter = substr($band['Name'], 0, 1);
              }
              echo '<span class="band" data-toggle="modal" data-target="#bandModal' . $band['BandID'] . '">' . $band['Name'] . '</span>';
            }

            echo '</div>';

          ?>
         
        </div> <!-- end name div -->

        <div class="tab-pane fade in" id="date"> <!-- begin date div -->
          <?php 
            $sql = "SELECT bands.BandID, Name, bandstoporchfests.PorchfestID, bandstoporchfests.TimeslotID, StartTime, EndTime 
                    FROM bands 
                    INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
                    INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID 
                    WHERE bandstoporchfests.PorchfestID = '1' ORDER BY StartTime";

            $result = $conn->query($sql);

            $lasttime = '';

            while($band = $result->fetch_assoc()) {
              if ($lasttime != $band['StartTime']) {
                echo '<h3>' . $band['StartTime'] . '</h3>';
                echo '<button type="button" class="btn btn-link" data-toggle="modal" data-target="#bandModal' . $band['BandID'] . '">
                        <h4>' . $band['Name'] .'<h4>
                        </button>
                      <br>';
                $lasttime = $band['StartTime'];
              }
            }
          ?>
        </div> <!-- end date div -->

        <div class="tab-pane fade in" id="map"> <!-- begin map div -->
          <img src="../img/map.png" alt="map" id = "map">
        </div> <!-- end map div -->
      
      </div> <!-- end tab-content div -->
    </div> <!-- end row div -->
  </div> <!-- end container div -->


  <?php 
    $sql = "SELECT bands.BandID, Name, Description, PorchLocation, bandstoporchfests.PorchfestID, bandstoporchfests.TimeslotID, StartTime, EndTime 
            FROM bands 
            INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
            INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID 
            WHERE bandstoporchfests.PorchfestID = '1'";

    $result = $conn->query($sql);


    while($band = $result->fetch_assoc()) {
      echo 
        '<div class="modal fade" id="bandModal' . $band['BandID'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              <div class="modal-header"><h3>'
              . $band['Name'] .
              '</h3></div>
              <div class="modal-body">
                <p>' . $band['StartTime'] . ' • ' . $band['PorchLocation'] . '</p>
                <p>' . $band['Description'] . '</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>';
    }
  ?>

</body>
</html>