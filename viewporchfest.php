<?php 
# This page is where attendees can view information about a porchfest
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php 
    require_once 'config.php';
    require_once CODE_ROOT . "/php/modules/stdHead.php";
    require_once CODE_ROOT . "/php/modules/routing.php";
    require_once CODE_ROOT . "/php/modules/login.php";
    require_once CODE_ROOT . "/php/modules/navigation.php";
  ?>
  <title>PorchFest - <?php echo PORCHFEST_NAME ?> </title>
</head>

<!-- BEGIN body -->
<body>
  <?php // Database credentials

    // Create connection
    // add DB_USER and DB_PASSWORD later
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Check whether this porchfest was published (1 for yes, 0 for no)
    $sql = sprintf("SELECT Published FROM porchfests WHERE PorchfestID='%s'", 
                    $conn->real_escape_string(PORCHFEST_ID));
    $result = $conn->query($sql);
    
    if (!$result) {
      throw new Exception("Query failed", 1);
    }
    $scheduled = $result->fetch_assoc()['Published'];


    // Create the side bar for letter sorting
    $alph = range('a', 'z');
    echo '<ul id="scrollto">';

    foreach ($alph as $letter) {
      echo '<li><a href="#' . $letter . '">' . strtoupper($letter) . '</a></li>';
    }
    echo '</ul>';
  ?>

  <!-- Responsive table js -->
  <script src="<?php echo JS_RESPONSIVE_TABLES_LINK;?>"></script>
  <!-- Responsive tables CSS -->
  <link rel="stylesheet" href="<?php echo CSS_RESPONSIVE_TABLES_LINK;?>">

  <style>
    h4 {
      text-align: center;
    }
  </style>

  <div class="container" id = "singleporchfestcontainer"> <!-- Container div -->
    
    <div class="row">
      <h1 style="text-align:center;"> 
        <?php echo PORCHFEST_NAME; ?> 
      </h1>
    </div>

    
    <div class="row"> <!-- begin row div -->
      <ul class="nav nav-pills">
        <li class="active"><a href="#name" data-toggle="tab"> Performers </a></li>
        <li><a href="#date" data-toggle="tab"> Schedule </a></li>
      </ul>
    </div> <!-- end row div -->

    <div class="row"> <!-- begin row div -->
      <div class="tab-content"> <!-- begin tab-content div -->

        <div class="tab-pane fade in active" id="name"> <!-- begin performers div -->
          <?php 
            if ($scheduled == 1) {
              // Displays bands in alphabetical order
              $sql = sprintf("SELECT bands.BandID, bands.Name, bands.Description FROM bands 
                      INNER JOIN bandstoporchfests WHERE bands.BandID = bandstoporchfests.BandID 
                      AND bandstoporchfests.PorchfestID = '%s'
                      ORDER BY Name", $conn->real_escape_string(PORCHFEST_ID));
              $result = $conn->query($sql);

              $lastletter = '';

              if ($result->num_rows == 0) {
                echo "Looks like no bands have signed up yet. Check back later!";
              }
              else {
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
              }
            } else {
              echo '<h4> The performers have not been finalized. Check back later to see the bands! </h4>';
            }

          ?>
         
        </div> <!-- end name div -->

        <div class="tab-pane fade in" id="date"> <!-- begin schedule div -->
          <?php 
            if ($scheduled == 1) {
              // Query to get the band information 
              $sql = sprintf("SELECT bands.BandID, Name, bandstoporchfests.PorchfestID, 
                      bandstoporchfests.TimeslotID, StartTime, EndTime 
                      FROM bands 
                      INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
                      INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID 
                      WHERE bandstoporchfests.PorchfestID = '%s' ORDER BY StartTime ASC, Name ASC", 
                      $conn->real_escape_string(PORCHFEST_ID));

              $result = $conn->query($sql);

              $lasttime = '';


              // Orders bands by scheduled timeslot and displays
              while($band = $result->fetch_assoc()) {
                if ($lasttime != $band['StartTime']) {
                  $starttime = date_format(date_create($band['StartTime']), 'g:iA');
                  echo '<h3>' . $starttime . '</h3>';
                  $lasttime = $band['StartTime'];
                }
                echo '<button type="button" class="btn btn-link" data-toggle="modal" data-target="#bandModal' . $band['BandID'] . '">
                          <h4>' . $band['Name'] .'<h4>
                          </button>
                        <br>';
              }
            } else {
              echo '<h4> The schedule has not been finalized. Check back later to see when the bands are playing! </h4>';
            }
          ?>
        </div> <!-- end date div -->
      </div> <!-- end tab-content div -->
    </div> <!-- end row div -->
  </div> <!-- end container div -->


  <?php 
    // Query to get all band information
    $sql = sprintf("SELECT bands.BandID, Name, Description, PorchLocation, 
            URL, bandstoporchfests.PorchfestID, bandstoporchfests.TimeslotID, StartTime, EndTime 
            FROM bands 
            INNER JOIN bandstoporchfests ON bands.BandID = bandstoporchfests.BandID 
            INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID 
            WHERE bandstoporchfests.PorchfestID = '%s'", $conn->real_escape_string(PORCHFEST_ID));

    $result = $conn->query($sql);

    // Creates the modal for each band so that when clicking on the band a modal will appear with band information
    while($band = $result->fetch_assoc()) {
      $starttime = date_format(date_create($band['StartTime']), 'g:iA');
      $endtime = date_format(date_create($band['EndTime']), 'g:iA');
      echo 
        '<div class="modal fade" id="bandModal' . $band['BandID'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header"><h3>'
              . $band['Name'] .
              '</h3></div>
              <div class="modal-body">
                <p>' . $starttime . "-" . $endtime . ' • ' . $band['PorchLocation'] . '</p>
                <a href="' . $band['URL'] . '">' . $band['URL'] . '</a>
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