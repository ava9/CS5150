<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php 
    require_once "../php/modules/stdHead.php";
    require_once "../php/routing.php";
  ?>
  <title>PorchFest - <?php echo PORCHFEST_NAME_CLEAN ?> </title>
</head>

<!-- BEGIN body -->
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    // add DB_USER and DB_PASSWORD later
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE porchfests.Name = '%s'", PORCHFEST_NAME_CLEAN);
    $result = $conn->query($sql);
    $porchfestID = $result->fetch_assoc()['PorchfestID'];

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
  <!-- navBar and login -->
  <?php require_once "../php/modules/login.php"; ?>
  <?php require_once "../php/modules/navigation.php"; ?>

  <div class="container" id = "singleporchfestcontainer"> <!-- Container div -->
    
    <div class="row">
      <h1 style="text-align:center;"> 
        <?php echo PORCHFEST_NAME_CLEAN; ?> 
      </h1>
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
                    WHERE bandstoporchfests.PorchfestID = '" . $porchfestID . "' ORDER BY StartTime";

            $result = $conn->query($sql);

            $lasttime = '';

            while($band = $result->fetch_assoc()) {
              if ($lasttime != $band['StartTime']) {
                $starttime = date_format(date_create($band['StartTime']), 'g:iA');
                echo '<h3>' . $starttime . '</h3>';
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
          <img src="/cs5150/img/map.png" alt="map" id = "map">
        </div> <!-- end map div -->
      
      </div> <!-- end tab-content div -->
    </div> <!-- end row div -->
  </div> <!-- end container div -->


  <?php 
    $sql = "SELECT bands.BandID, Name, Description, PorchLocation, bandstoporchfests.PorchfestID, bandstoporchfests.TimeslotID, StartTime, EndTime 
            FROM bands 
            INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
            INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID 
            WHERE bandstoporchfests.PorchfestID = '" . $porchfestID . "'";

    $result = $conn->query($sql);


    while($band = $result->fetch_assoc()) {
      $starttime = date_format(date_create($band['StartTime']), 'g:iA');
      $endtime = date_format(date_create($band['EndTime']), 'g:iA');
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
                <p>' . $starttime . "-" . $endtime . ' • ' . $band['PorchLocation'] . '</p>
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