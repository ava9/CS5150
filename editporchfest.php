<?php 
# This page is where an organizer can edit their porchfest information and do scheduling.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php 
    require_once "config.php";
    require_once CODE_ROOT . "/php/modules/stdHead.php";
  ?>
    <!-- Responsive table js -->
    <script src="<?php echo JS_RESPONSIVE_TABLES_LINK; ?>"></script>

    <script>
    // update when clickable tab elements with click. Used as an onclick function for the tabs.
    function enable(id) {
      $(id).css({pointerEvents: "auto"});
      $(".tab-pane:not(" + id + ")").css({pointerEvents: "none"});
    }
    </script>
    <!-- Responsive tables CSS -->
    <link rel="stylesheet" href="<?php echo CSS_RESPONSIVE_TABLES_LINK; ?>">
    
    <title>PorchFest - Edit</title>
  </head>

  <body>
    <?php 
      require_once CODE_ROOT . "/php/routing.php";
      require_once CODE_ROOT . "/php/modules/login.php";
      require_once CODE_ROOT . "/php/modules/navigation.php";

      // Create connection
      // add DB_USER and DB_PASSWORD later
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Get porchfestID from url
      $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE porchfests.Name = '%s'", PORCHFEST_NAME);
      $result = $conn->query($sql);
      $porchfestID = $result->fetch_assoc()['PorchfestID'];

      $result = $conn->query("SELECT Scheduled FROM porchfests WHERE PorchfestID = '" . $porchfestID . "'");

      $scheduled = $result->fetch_assoc()['Scheduled'] == '1';

      function create_filters($pid, $conn) {
        $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $pid . "' ORDER BY StartTime";

        $result = $conn->query($sql);

        while($timeslot = $result->fetch_assoc()) {
          $start = date_create($timeslot['StartTime']);
          $end = date_create($timeslot['EndTime']);

          echo '<label class="btn btn-primary" id="' . $timeslot['TimeslotID'] . '"><input class="filters" type="checkbox" autocomplete="off">' . date_format($start, 'g:iA') . '-' . date_format($end, 'g:iA') . '</label>';
        }

      }

      // returns an associative array. Each element points to another associative
      // array. One entry in this associative array is the 'current' conflicts,
      // bands that are currently assigned to the same timeslot,
      // and the other is the 'potential' conflicts.
      // Also contains original tid to check which bands need to be updated when saving
      // changes, as well as a string containing the band's name.
      // i.e. [bandid1 => [current=> [bandid2], potential=> [bandid2, bandid4], current, original], ...]
      function findConflictingBands($conn, $porchfestID) {
        $bandConflicts = array();
        $bandConflicts["conflictCounter"] = 0;

        $sql = "SELECT BandID, TimeslotID FROM bandstoporchfests WHERE PorchfestID=" . $porchfestID;

        $result = $conn->query($sql);

        if (!$result) {
          throw new Exception('Query failed');
        }

        while ($bands = $result->fetch_assoc()) {
          $bandID = $bands['BandID'];


          // this query gets the potential conflicts two bands have.
          $sql = sprintf("SELECT BandID2 FROM bandconflicts WHERE BandID1 = '%s'
                          UNION
                          SELECT BandID1 FROM bandconflicts WHERE BandID2 = '%s'", $bandID, $bandID);

          $result2 = $conn->query($sql);

          if (!$result2) {
            throw new Exception('Query failed');
          }

          $conflicting_bands = array();

          while ($c = $result2->fetch_assoc()) {
            $conflictbandID = -1;
            try {
              $conflictbandID = $c['BandID2'];
            } catch (Exception $e) {
              $conflictbandID = $c['BandID1'];
            }
            array_push($conflicting_bands, $conflictbandID);
          }

          // this query checks to see if two bands are conflicting given their assigned timeslots.
          $sql = sprintf("SELECT bandconflicts.BandID1, bandconflicts.BandID2 FROM bandconflicts
              INNER JOIN bandstoporchfests AS C1
              ON BandID1 = C1.BandID
              INNER JOIN bandstoporchfests AS C2
              on BandID2 = C2.BandID
              WHERE C1.TimeslotID = C2.TimeslotID
              AND (BandID1 = %s
              OR BandID2 = %s)", $bandID, $bandID);

          $result3 = $conn->query($sql);

          if (!$result3) {
            throw new Exception('Query failed');
          }

          $current_conflicts = array();

          while ($b = $result3->fetch_assoc()) {
            $bandConflicts["conflictCounter"]++;
            $id = ($b['BandID1'] == $bandID) ? $b['BandID2'] : $b['BandID1'];
            array_push($current_conflicts, $id);
          }

          $sql = sprintf("SELECT Name FROM bands WHERE BandID='%s'", $bandID);

          $result4 = $conn->query($sql);

          if (!$result4) {
            throw new Exception('Query failed');
          }

          $name = $result4->fetch_assoc()['Name'];

          $bandConflicts[$bandID] =  array("name" => $name, "potential" => $conflicting_bands, "current" => $current_conflicts, "original" => $bands['TimeslotID'], "tid" => $bands['TimeslotID']);

        }

        return $bandConflicts;
      }

      function getBandNames($bands, $conflicts) {
        $names = array();
        foreach($bands as $band) {
          array_push($names, $conflicts[$band]["name"]);
        }

        return $names;
      }


      function getConflicts($conflicts, $bandID) {
        $result = array();
        if (sizeof($conflicts[$bandID]["current"]) > 0) {
          array_push($result, 'This band conflicts with: ' . implode(", ", getBandNames($conflicts[$bandID]["current"], $conflicts)));
          array_push($result, 'This band conflicts with: ' . implode(", ", $conflicts[$bandID]["current"]));
        } else {
          array_push($result, 'No conflicts');
          array_push($result, 'No conflicts');
        }

        return $result;
      } 

    ?>

    <div id="editalert"></div>

    <!-- Modal -->
    <div id="edit-timeslot-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"> </h4>s
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" id="save-timeslot-change" class="btn btn-success" data-dismiss="modal"> Save </button>
            <button type="button" id="delete-timeslot" class="btn btn-danger" data-dismiss="modal"> Delete </button>
            <button type="button" class="btn btn-default" data-dismiss="modal"> Cancel </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container"> <!-- begin container div -->
      <div class="row"> <!-- begin row 1 div -->
        <div class="col-sm-2"> <!-- begin col 1 div -->
            <nav class="nav-sidebar">
                <ul class="nav">
                  <li class="active"><a href="#manageporchfest" data-toggle="tab" onclick="enable('#manageporchfest')"> Manage Porchfest </a></li>
                  <li><a href="#bands" data-toggle="tab" onclick="enable('#bands');"> Manage Bands </a></li>
                  <li><a href="#timeslots" data-toggle="tab" onclick="enable('#timeslots');"> Manage Time Slots </a></li>
                  <li><a href="#schedule" data-toggle="tab" onclick="enable('#schedule');"> Schedule </a></li>
                  <li><a href="#export" data-toggle="tab" onclick="enable('#export');"> Export </a></li>
                  <li><a href="#publish" data-toggle="tab" onclick="enable('#publish');"> Publish </a></li>
                  <li class="nav-divider"></li>
                </ul>
            </nav>
        </div> <!-- end col 1 div -->

        <div class="col-sm-10"> <!-- begin col 2 div -->
          <div class="tab-pane fade in active" id="manageporchfest"> <!-- begin manageporchfest div -->
            <div id="porchfestinfo"> <!-- begin porchfestinfo div -->
              <div class="input-group"> <!-- begin input-group div -->   
                <form action="editporchfest.php" method="POST" id="porchfestmanagesubmit">
                  <?php 
                    $sql = "SELECT * FROM `porchfests` WHERE PorchfestID = '" . $porchfestID . "'";

                    $result = $conn->query($sql);

                    $porchfest = $result->fetch_assoc();

                    echo '<p>';
                    echo '<label> Porchfest Name </label>';
                    echo '<input data-validation="length" data-validation-length="min4" type="text" name="porchfestname" class="form-control" value="' . $porchfest['Name'] . '" placeholder="Porchfest Name">';
                    echo '<br />';
                    echo '</p>';

                    echo '<p>';
                    echo '<label> Porchfest Location </label>';
                    echo '<input id="autocomplete" onFocus="geolocate()" type="text" name="porchfestlocation" class="form-control" value="' . $porchfest['Location'] . '" placeholder="Porchfest Location">';
                    echo '<br />';
                    echo '</p>';

                    echo '<p>';
                    echo '<label> Porchfest Date </label>';
                    echo '<input data-validation="date" data-validation-format="yyyy-mm-dd" type="date" name="porchfestdate" class="form-control" value="' . $porchfest['Date'] . '" placeholder="Porchfest Date">';
                    echo '<br />';
                    echo '</p>';

                    echo '<p>';
                    echo '<label> Porchfest Description </label>';
                    echo '<textarea rows="5" cols="50" id="porchfestdescription" class="form-control" placeholder="Porchfest Description">' . $porchfest['Description'] .  '</textarea>';
                    echo '<br />';
                    echo '</p>';

                    $deadline = date_create($porchfest['Deadline']);
                    echo '<p>';
                    echo '<label> Porchfest Deadline Date </label>';
                    $day = new DateTime(date_format($deadline, 'Y-m-d')); // to drop the time
                    echo '<input data-validation="date" data-validation-format="yyyy-mm-dd" type="date" name="porchfestdeadlineday" class="form-control" value="' . $day->format('Y-m-d') . '" placeholder="Porchfest Deadline Date">';
                    echo '<br />';
                    echo '</p>';

                    echo '<button type="submit" class="btn btn-primary"> Submit </button>';
                  ?>
                </form>
              </div> <!-- end input-group div -->
            </div>  <!-- end porchfestinfo div -->
          </div> <!-- end manageporchfest div -->
          <div class="tab-pane fade" id="bands"> <!-- begin bands div -->
            
              <?php
                echo '<div class="col-xs-12">';
                echo '<div class="btn-group" data-toggle="buttons">';
                    create_filters($porchfestID, $conn);
                echo '</div>';
                echo '</div>';
                
              ?>

              <div class="col-xs-12" id="functionalitybtns">
                <div class="col-xs-6 col-md-8">
                  <button id="email-all" class="btn btn-primary btn-sm"> Email All Bands </button>
                  <button id="email-bands-button" class="btn btn-primary btn-sm"> Email Selected Timeslots!</button>
                </div>
                
                <div class="col-xs-6 col-md-4">
                  <input id="bandssearch" name="search" type="text" placeholder="Search..."/>
                </div>
              </div>
            <div class="col-xs-12">
              <div class="table-container table-responsive bands-table" id="bandstable"> <!-- begin table-container div -->
                <table class="responsive table"> <!-- begin table -->
                  <tr class='fixed' data-status= "fixed">
                    <th> Name </th>
                    <th> Description </th>
                    <th> Timeslots </th>
                    <th> Scheduled </th>
                    <th> Manage </th>
                    <th> Contact </th>
                  </tr>
                  <?php 
                    // Given a band name and SQL connection, get the registered emails of the band and 
                    // return a mailto link to them

                    function email_href($bName, $bMembers) {
                      $members = explode(',', $bMembers);

                      $recipient = $members[0];
                      $cc = '';
                      unset($members[0]);
                      foreach ($members as $key => $value) {
                        $cc = $cc . $value . ',';
                      }

                      $subject = sprintf("[Porchfest] %s", $bName);
                      return sprintf("mailto:%s?cc=%s&subject=%s", $recipient, $cc, $subject);
                    }

                    $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";

                    $result = $conn->query($sql);
                    
                    while($band = $result->fetch_assoc()) {
                      $bandname = $band['Name'];
                      // Modify the band name such that it looks good in the URL.
                      // All spaces (' ') become '-' and all '-' become '--'.
                      $urlbandname = str_replace(" ", "-", str_replace("-", "--", $bandname));

                      echo '<tr class="' . (is_null($band['TimeslotID']) ? '' : $band['TimeslotID']) . '">';
                      echo '<td>' . $band['Name'] . '</td>';
                      echo '<td>' . $band['Description'] . '</td>';
                      echo '<td> <a data-target="#timeslotModal' . $band['BandID'] . '" data-toggle="modal"> Time Slots </a> </td>';
                      echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
                      echo '<td> <a href="' . EDIT_PORCHFEST_URL  . '/' . PORCHFEST_NICKNAME . '/' . 
                                  urlencode($urlbandname) . '"> Edit </a> </td>';
                      echo '<td> <a href="' . email_href($band['Name'], $band['Members']) . '" target="_blank"> Email </a> </td>'; 
                    }
                  ?>

                </table> <!-- end table -->
              </div> <!-- end table-container div -->
            </div>
          </div> <!-- end bands div -->

          <div class="tab-pane fade" id="timeslots"> <!-- begin timeslots div -->
            <div id="timeslottab-form">
              <div class="col-xs-12" id="timeslotheaders">
                Existing Timeslots. Click on any of the timeslots below to edit or delete it.
              </div>

              <div id="existingslots">
                <?php
                  $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $porchfestID . "' ORDER BY StartTime;";

                  $result = $conn->query($sql);

                  while($timeslot = $result->fetch_assoc()) {
                    $start_time = date_create($timeslot['StartTime']);
                    $end_time = date_create($timeslot['EndTime']);

                    echo '<div class="col-xs-6 col-sm-3 timeslot-label"><span id="' . $timeslot['TimeslotID'] . '-' . date_format($start_time, 'Y.m.d-g:iA') . "-" . date_format($end_time, 'Y.m.d-g:iA') . '" class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';
                  }

                ?>
              </div>

              <div class="col-xs-12" id="timeslotheaders">
                  Add a Timeslot.
              </div>

              <div class="col-xs-12">
                <form role="form" id="createtimeslot" class="form-horizontal" action="editporchfest.php">
                  <div class="col-xs-6">
                    <label for="timeslot" > Start Time (24 hr clock format) </label>
                      <input name="newtimeslotstart" placeholder="HH:MM" type="text" data-validation="time" data-validation-optional="true" data-validation-help="Format as XX:XX">
                  </div>
                  <div class="col-xs-6">
                    <label for="timeslot"> End Time (24 hr clock format)</label>
                      <input name="newtimeslotend" placeholder="HH:MM" type="text" data-validation="time" data-validation-optional="true" data-validation-help="Format as XX:XX">
                  </div>
                  <div class="col-xs-offset-8 col-xs-4 timeslot-button">
                    <button type="submit" class="btn btn-primary btn-sm"> Add Timeslot </button>
                  </div>

                </form>
              </div>
            </div>
          </div> <!-- end timeslots div -->

          <div class="tab-pane fade" id="schedule"> <!-- begin schedule div -->
            <?php
              $result = $conn->query("SELECT Scheduled FROM porchfests WHERE PorchfestID = '" . $porchfestID . "'");

              if (!$scheduled) {
                $conflicts = findConflictingBands($conn, $porchfestID);
                echo '<div id="scheduletab-button">';
                echo '<button id="schedule-button" class="btn btn-primary btn-sm"> Schedule it!</button>';
                echo '</div>';
              } else {
                $conflicts = findConflictingBands($conn, $porchfestID);
                echo '<div id="scheduletab-conflictstable">';
                echo '<div class="col-xs-12">';
                  echo '<div class="btn-group" id="filterid" data-toggle="buttons">';
                    create_filters($porchfestID, $conn);
                  echo '</div>';
                echo '</div>';

                echo '<div class="col-xs-12" id="functionalitybtns">';
                  echo '<div class="col-xs-6 col-sm-6 col-md-6">
                          <button id="save-changes-button" class="btn btn-primary btn-sm"> Save changes </button>
                        </div>';                          
                  echo '<div class="col-xs-6 col-sm-6 col-md-6">
                          <input id="schedulesearch" name="search" type="text" placeholder="Search..."/>
                        </div>';
                echo '</div>';

                echo '<div class="col-xs-12">';
                echo '<div class="table-container table-responsive bands-table" id="scheduletable"> <!-- begin table-container div -->
              <table class="responsive table"> <!-- begin table -->
                <tr class="fixed" data-status= "fixed">
                  <th> Name </th>
                  <th> Timeslots </th>
                  <th> Conflicts </th>
                </tr>';
                  

                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";

                  $result = $conn->query($sql);

                  while($band = $result->fetch_assoc()) {
                    $conflictList = getConflicts($conflicts, $band['BandID']);

                    echo '<tr id="' . 'band-' . $band['BandID'] . '" class="' . (is_null($band['TimeslotID']) ? '' : $band['TimeslotID']) . ' ' . ($conflictList[0] != 'No conflicts' ? 'hasconflict' : '') . '">';
                    echo '<td>' . $band['Name'] . '</td>';
                    $sql2 = 'SELECT * FROM `porchfesttimeslots` INNER JOIN bandavailabletimes ON porchfesttimeslots.TimeslotID = bandavailabletimes.TimeslotID WHERE bandavailabletimes.bandID=' . $band['BandID'];

                    echo '<td><select class="timesdropdown" id="times-' . $band['BandID'] . '">';

                    $sql3 = "SELECT * FROM `bandstoporchfests` INNER JOIN porchfesttimeslots ON bandstoporchfests.TimeslotID = porchfesttimeslots.TimeslotID WHERE bandstoporchfests.bandID=" . $band['BandID'];

                    $result3 = $conn->query($sql3);

                    if ($result3->num_rows > 0) {
                      $assigned = $result3->fetch_assoc();

                      var_dump($assigned);
                      $start_time_assigned = date_create($assigned['StartTime']);
                      $end_time_assigned = date_create($assigned['EndTime']);

                      echo '<option value="' . $assigned['TimeslotID'] . '">' . date_format($start_time_assigned, 'g:i A') . "-" . date_format($end_time_assigned, 'g:i A') . '</option>';
                    }
                    
                    $result2 = $conn->query($sql2);
                    while ($timeslots = $result2->fetch_assoc()) {
                      $start_time = date_create($timeslots['StartTime']);
                      $end_time = date_create($timeslots['EndTime']);

                      if (!($start_time == $start_time_assigned && $end_time == $end_time_assigned)) {
                        echo '<option value="' . $timeslots['TimeslotID'] . '">' . date_format($start_time, 'g:i A') . "-" . date_format($end_time, 'g:i A') . '</option>';
                      }
                    }
                    echo '</td></select>';

                    

                    echo '<td>';
                    echo '<p id="conflict-names-' . $band['BandID'] . '"> ' . $conflictList[0]  . ' <p>';
                    echo '<input type="hidden" id="conflicts-' . $band['BandID'] .'" value="' . $conflictList[1] . '">';
                    echo '</td>';

                    
                  }

                  echo '</table> <!-- end table -->
                  </div> <!-- end table-container div -->';
                  echo '</div>';

                  echo '</div>';

              }
            ?>          
          </div> <!-- end schedule div -->

          <div class="tab-pane fade" id="export"> <!-- begin export div -->
          <?php
              $sql = "SELECT Scheduled from porchfests WHERE PorchfestID='" . $porchfestID . "'";
              $result = $conn->query($sql);
              $scheduled = $result->fetch_assoc()['Scheduled'];
              if (!$scheduled) {
                ?>
                <div class="col-md-1">
                </div>
                <div class="col-md-9">
                <p>Your Porchfest hasn't been scheduled yet! Please see the schedule tab to schedule your Porchfest. </p>
                </div>
            <?php
              } else {
            ?>
              <form role="form" class="form-horizontal" id='submit-info-form' method='POST' action='<?php echo PHP_EXPORT; ?>' target="_blank">
                <input type = "hidden" name = "porchfestid" value = <?php echo PORCHFEST_ID; ?> />
                <input type = "hidden" name = "PORCHFEST_NICKNAME" value = <?php echo PORCHFEST_NICKNAME; ?> />
                <div class="form-group">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-9">
                      <p>Export CSV - Press this button to export the current schedule to a .csv file. The headers for the file are "Band Name, Location, Start Time, End Time"</p>
                      <button type="submit" name="exportCSV" class="btn btn-primary btn-sm"> Export CSV</button>
                      <p></p>
                      <p>Export KML - Press this button to export the current schedule to a .kml file. You can then go <a href="https://mymaps.google.com"> here </a>to upload the kml file to generate a Google Map View of the schedule. In the Google Map View, the bands are sorted by the time slot they are playing at. The bands with a red pin are within 25 meters of another band. The bands with a yellow pin are within 25 to 50 meters of another band.</p>
                      <button type="submit" name="exportKML" class="btn btn-primary btn-sm"> Export KML </button>
                    </div>
                </div>
              </form>
            <?php } ?>
          </div> <!-- end export div -->

          <div class="tab-pane fade" id="publish"> <!-- begin publish div -->
            <?php
              $sql = "SELECT Published from porchfests WHERE PorchfestID='" . $porchfestID . "'";
              $result = $conn->query($sql);
              $published = $result->fetch_assoc()['Published'];
              if (!$published) {
                echo '<button type="button" id="publishbutton" name="publishbutton" class="btn btn-default">Publish</button>';
              }
              else {
                echo '<button type="button" id="publishbutton" name="publishbutton" class="btn btn-default">Unpublish</button>';
              }
            ?>
          </div> <!-- end publish div -->

        </div> <!-- end col 2 div -->
      </div> <!-- end row 1 div -->
    </div> <!-- end container div -->
  <?php 
    $bandquery = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";
    $bandresults = $conn->query($bandquery);

    while($band = $bandresults->fetch_assoc()) {
      echo '<div id="timeslotModal' . $band['BandID'] . '" class="modal fade">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title"> Time Slots </h4>
                </div>
                <div class="modal-body">';
                    $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID='" . $porchfestID . "' ORDER BY StartTime";
                    $result = $conn->query($sql);
                    while($timeslot = $result->fetch_assoc()) {
                      $sql2 = "SELECT * FROM bandavailabletimes
                               WHERE BandID='" . $band['BandID'] . "' AND TimeslotID = '" . $timeslot['TimeslotID'] . "'";
                      $result2 = $conn->query($sql2);
                      $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
                      $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
                      $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
                      if ($result2->num_rows > 0) {
                        echo $starttime . "-" . $endtime . " on " . $day . "<br>";
                      }
                    }
                echo 
                '</div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal"> Close </button>
                </div>
              </div>
            </div>
          </div>';  
    }
  ?>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
  <script>
    // initialize only the first tab's elements as clickable, disable everything else.
    var ajaxurl = "<?php echo PHP_AJAX; ?>"; // the path to the ajax file.
    var porchfestid = "<?php echo PORCHFEST_ID; ?>";
    var porchfestname = "<?php echo PORCHFEST_NAME; ?>"

    $("#manageporchfest").css("pointer-events", "auto");
    $(".tab-pane:not(" + "#manageporchfest" + ")").css({pointerEvents: "none"});

    // enable form validation
    $.validate({
      lang: 'en',
      modules : 'date'
    });

    function filterByTimeslot(obj, fid, filter) {
      // if the button is toggled, aka want to filter by this.
      var id = $(obj).parent().attr('id');
      if($(obj).parent().hasClass('active')) {
        $(fid + ' tr.' + id).show();
        // $('#schedule tr.' + id).show();
        filter = filter + ':not(tr.' + id + ')';
      } else {
        // need to remove this filter
        filter = filter.replace(':not(tr.' + id + ')', '');
      }

      $(filter).hide();

      if (filter == fid + ' tr:not(.fixed)') {
        $('tr').show();
        return fid + ' tr:not(.fixed)';
      }

      return filter;
    }

    // filter buttons for the Manage Bands tab.
    var bfilter = '#bandstable tr:not(.fixed)';

    $('#email-bands-button').prop("disabled", true);


    // array of timeslotIDs, mapped to boolean
    // that indicates whether it is currently selected or not
    // used for emailing
    var timeslotIDs = {
      'mass_email': true, 
      'porchfestid': porchfestid,
      'porchfestname': porchfestname
    };

    $('#bands .filters').change(function() {
      // if the button is toggled, aka want to filter by this.
      var x = filterByTimeslot($(this), '#bandstable', bfilter);
      var id = $(this).parent().attr('id');

      bfilter = x;


      if (bfilter == '#bandstable tr:not(.fixed)') {
        $('#email-bands-button').prop("disabled", true);
      } else {
        $('#email-bands-button').prop("disabled", false);
      }
      
      if (timeslotIDs[id] == null) {
        timeslotIDs[id] = true;
      }
      else {
        timeslotIDs[id] = !timeslotIDs[id];
      }
    });

    
    // filter buttons for the Schedule tab.
    var sfilter = '#scheduletable tr:not(.fixed)';

    $('#schedule .filters').change(function() {
      var x = filterByTimeslot($(this), '#scheduletable', sfilter);

      sfilter = x;
    });

    // ajax call for the Manage Bands tab, emails all performers (BCC)
    $('#email-all').click(function() {
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: {"all_email": true, "porchfestid": porchfestid, "porchfestname": porchfestname},
        success: function(result) {
          console.log(result);
          window.open(result, '_blank');
        },
        error: function(result) {
          console.log(error);
        }
      });
    });

    // ajax call for the Bands tab, to email a selected group of timeslots.
    $('#email-bands-button').click(function() {
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: timeslotIDs,
        success: function(result) {
          console.log(result);
          window.open(result, '_blank');
        },
        error: function(result) {
          console.log(error);
        }
      });
    });

    function getBandNames(conflicts, bands) {
      var names = [];
      for (var i = 0; i < bands.length; i++) {
        names.push(conflicts[bands[i]]["name"]);
      }

      return names;
    }

    var conflicts = <?php echo json_encode($conflicts); ?>;

    function updateOldConflictingBand(conflictingid, id) {
      console.log(id);
      var iconflicts = conflicts[conflictingid]["current"];

      if (iconflicts.length == 1) {
        // the conflicting band only conflicted with the current band.
        $('#conflicts-' + conflictingid).val('No conflicts');
        $('#conflict-names-' + conflictingid).text('No conflicts');
        conflicts[conflictingid]["current"] = [];
        $('#band-' + conflictingid).removeClass('hasconflict');
      } else {
        // the conflicting band has other conflicts.
        console.log(iconflicts);
        var index = iconflicts.indexOf('' + id + '');
        iconflicts.splice(index, 1);
        conflicts[conflictingid]["current"] = iconflicts;
        $('#conflicts-' + conflictingid).val("This bands conflicts with: " + iconflicts.join(", "));
        $('#conflict-names-' + conflictingid).text("This bands conflicts with: " + getBandNames(conflicts, iconflicts).join(", "));
        $('#band-' + conflictingid).addClass('hasconflict');
        console.log(iconflicts);
      }
    }

    function updateNewConflictingBand(conflictingid, id) {
      var iconflicts = conflicts[conflictingid]["current"];
      
      iconflicts.push(id);
      
      conflicts[conflictingid]["current"] = iconflicts;
      $('#conflicts-' + conflictingid).val("This bands conflicts with: " + iconflicts.join(", "));
      $('#conflict-names-' + conflictingid).text("This bands conflicts with: " + getBandNames(conflicts, iconflicts).join(", "));
      $('#band-' + conflictingid).addClass('hasconflict');
    }

    // ajax call for the Schedule tab, to determine if bands are conflicting.
    $('#scheduletable').on('change', '.timesdropdown', function() {
      console.log('here');
      var newtid = $(this).val();
      var id = $(this).attr("id").split('-')[1].trim();

      conflicts[id]["tid"] = newtid;

      var new_conflicts = [];
      if (conflicts[id]["potential"].length > 0) {
        for(var i = 0; i < conflicts[id]["potential"].length; i++) {
          var otid = $('#times-' + conflicts[id]["potential"][i]).val()
          
          if (otid == newtid) {
            // there is a conflict, because one of the potentials has the same timeslot id.
            var conflictingid = conflicts[id]["potential"][i];
            updateNewConflictingBand(conflictingid, id);
            new_conflicts.push(conflictingid);
          }
        }

        // update the conflicting bands.
        for (var i = 0; i < conflicts[id]["current"].length; i++) {
          updateOldConflictingBand(conflicts[id]["current"][i], id);
        }

        conflicts["conflictCounter"] = conflicts["conflictCounter"] - (conflicts[id]["current"].length * 2);

        conflicts[id]["current"] = new_conflicts;

        conflicts["conflictCounter"] = conflicts["conflictCounter"] + (conflicts[id]["current"].length * 2)

        if (conflicts[id]["current"].length > 0) {
          $('#conflicts-' + id).val("This bands conflicts with: " + conflicts[id]["current"].join(", "));
          $('#conflict-names-' + id).text("This bands conflicts with: " + getBandNames(conflicts, conflicts[id]["current"]).join(", "));
          $('#band-' + id).addClass('hasconflict');
        } else {
          $('#conflicts-' + id).val('No conflicts');
          $('#conflict-names-' + id).text('No conflicts');
          $('#band-' + id).removeClass('hasconflict');
        }
      }
    });

    function resolveConflict(status) {
      $('#warningModal').remove();
      $('.modal-backdrop.fade.in').remove();
      $('body').removeClass('modal-open');
      $("#editalert").html('');

      if (status) {
        saveAssignedTimeChanges();
      }
    }


    function conflictWarning() {
      console.log('here2');
      var warningmodal = '<!-- Modal --><div class="modal fade" id="warningModal" role="dialog"><div class="modal-dialog"><!-- Modal content--><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"> Your schedule has conflicts! </h4></div><div class="modal-body"><p> The schedule you are trying to save has some conflicts. If you want to continue, press accept. Otherwise, press cancel. Your changes will not be saved until you accept!</p></div><div class="modal-footer"><button type="button" class="btn btn-success" onclick="resolveConflict(true);" data-dismiss="modal" data-target="#warningModal">Accept</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="resolveConflict(false);" data-target="#warningModal">Cancel</button></div></div></div></div>';
      $("#editalert").html(warningmodal);

      $('#warningModal').modal('toggle');
    }


    function saveAssignedTimeChanges() {
      var json = JSON.stringify(conflicts);

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {json: json},
        success: function(result) {
          if (result == 'success') {
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The schedule was updated successfully. </div>');
          } else {
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
            console.log(result);
          }
        },
        error: function(result) {
          console.log(error);
        }
      });
    }

    $('#save-changes-button').click(function() {
      console.log(conflicts["conflictCounter"]);
      if (conflicts["conflictCounter"] == 0) {
        saveAssignedTimeChanges();
      } else {
        console.log('here1');
        conflictWarning();
      }

    });

    // get rid of edit alert when clicking anywhere on the page.
    $('body').click(function() {
      $("#editalert").html('');
    });

    // ajax call for publishing.
    $('#publishbutton').click(function() {
      var publishbutton = true;

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {publishbutton: publishbutton, porchfestid: porchfestid},
        success: function(result){
          if (result == "success") {
            if ($('#publishbutton').html() == "Publish") {
              $('#publishbutton').html("Unpublish");
              $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your Porchfest was Published successfully. </div>');
            } else {
              $('#publishbutton').html("Publish");
              $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your Porchfest was Unpublished successfully. </div>');
            }
          } else {
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
            console.log(result);
          }
        },
        error: function(result) {
          console.log('error');
        }
      });
    });

    // ajax call for deleting a timeslot in the modal.
    $('#delete-timeslot').click(function(){
      var spid = $('#edit-timeslot-modal').find('.modal-header').attr('id');
      var tid = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {tid: tid},
        success: function(result){
          if (result == "success") {
            $('span[id="' + spid + '"]').remove();
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The timeslot was deleted successfully. </div>');
          } else {
            console.log(result);
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          }
        },
        error: function(result) {
          console.log('error');
        }
      });
    });

    function addSpace(s) {
      return s.substring(0, s.length-2) + " " + s.substring(s.length-2);
    }


    // ajax calls to update a timeslot's time (can't change date) on the DB.
    $('#save-timeslot-change').click(function() {
      var timeslotid = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();
      var year = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[1].trim();
      var start = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[2].trim();
      var end = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[4].trim();

      var formData = {
          timeslotid             : timeslotid,
          timeslotstart          : $('input[name=timeslot-start]').val(),
          timeslotend            : $('input[name=timeslot-end]').val(),
          start                  : year + " " + start,
          end                    : year + " " + end,
          porchfestid            : porchfestid
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: formData,
        success: function(result){
          console.log(result);
          if (result == "success") {
            $("span:contains('" + addSpace(start) + " - " + addSpace(end) + "')").html(addSpace(formData['timeslotstart']) + " - " + addSpace(formData['timeslotend']));
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The timeslot was updated successfully </div>');
          } else {
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          }
        },
        error: function(result) {
          console.log(result);
        }
      });
    });

    function createTimeSlotModal() {
      var start = $(this).attr('id').split('-')[2].trim();
      var end = $(this).attr('id').split('-')[4].trim();

      $('#edit-timeslot-modal').find('.modal-header').html(start + ' - ' + end);
      $('#edit-timeslot-modal').find('.modal-header').attr('id', $(this).attr('id'));

      

      $('#edit-timeslot-modal').find('.modal-body').html('<form id="timeslot-form"><input type="text" data-validation="custom" data-validation-regexp="((1[0-2]|0?[1-9]):([0-5][0-9])([AP][M]))" data-validation-help="Format as XX:XXPM/AM" name="timeslot-start" class="form-control" value="' + start + '" placeholder="Start Time"><input type="text" data-validation="custom" data-validation-regexp="((1[0-2]|0?[1-9]):([0-5][0-9])([AP][M]))" data-validation-help="Format as XX:XXPM/AM" name="timeslot-end" class="form-control" value="' + end + '" placeholder="End Time"></form>');
      $('#edit-timeslot-modal').modal('show');

      $.validate({
        lang: 'en',
        modules : 'date'
      });
    }

    // dynamically generates a modal when clicking one of the timeslot labels
    $('.label').click(createTimeSlotModal);

    $("#createtimeslot").submit(function(event){
      var formData = {
        porchfestid : porchfestid,
        newstart    : $('input[name=newtimeslotstart]').val(),
        newend      : $('input[name=newtimeslotend]').val()
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: formData,
        success: function(result){
          if (result != "fail") {
            $("#existingslots").append(result);
            $('.label').last().click(createTimeSlotModal);
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The new timeslot has been added. </div>');
          } else {
            console.log(result);
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          }
        },
        error: function(result) {
          console.log(result);
        }
      });

      event.preventDefault();
    });

    function conflictingFirst() {
      var c = $('.hasconflict');
      var s = c.clone();
      c.hide();
      $('#scheduletable table tr:first').after(s);

      return [c, s];
    }

    function undoConflictingFirst(l) {
      l[1].hide();
      l[0].show();
    }

    // submit the form on Manage Porchfest.
    $("#porchfestmanagesubmit").submit(function(event){
      var formData = {
          porchfestname          : $('input[name=porchfestname]').val(),
          porchfestlocation      : $('input[name=porchfestlocation]').val(),
          porchfestdate          : $('input[name=porchfestdate]').val(),
          porchfestdescription   : $('#porchfestdescription').val(),
          porchfestdeadlineday   : $('input[name=porchfestdeadlineday]').val(),
          porchfestid            : porchfestid
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: formData,
        success: function(result){
          if (result == "success") {
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your Porchfest information was updated successfully. </div>');
          } else {
            console.log(result);
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          }
        },
        error: function(result) {
          console.log('error');
        }
      });
      event.preventDefault();
    });

    // the search bar in Schedule tab. AJAX call with the given input, displays data from the DB.
    $("#schedulesearch").keyup(function(){
      var c = JSON.stringify(conflicts);
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {bname: $("#schedulesearch").val(), conflicts: c, poid: porchfestid},
        success: function(result){
          $("#scheduletable").html(result);

          // reapply the filters to the search results.
          $(sfilter).hide();

          if (sfilter == '#scheduletable tr:not(.fixed)') {
            $('tr').show();
          }
        }
      });
    });


    // the search bar in Manage Bands. AJAX call with the given input, displays data from the DB.
    $("#bandssearch").keyup(function(){
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: {bandname: $("#bandssearch").val(), porchfestid: porchfestid},
        success: function(result){
          $("#bandstable").html(result);

          // reapply the filters to the search results.
          $(bfilter).hide();

          if (bfilter == '#bandstable tr:not(.fixed)') {
            $('tr').show();
          }
        }
      });
    });

    $('#schedule-button').click(function(){
      var loader_img = '<img src="<?php echo GIF_LOADING; ?>" alt="Loading" />';
      $('#schedule-button').hide();
      $('#scheduletab-button').append(loader_img);

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {porchfestid: porchfestid, schedule: 1},
        success: function(result){
          if (result == "success") {
            $('#scheduletab-button img').hide();
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The schedule was generated successfully. <a href="" onclick="location.reload()"> Refresh </a> the page to see the new schedule. </div>');
          } else {
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          }
          // console.log('Scheduled!');
          // console.log(result);
        },
        error: function(result) {
          $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
          console.log('error');
          console.log(result);
        }
      });
    });
  </script>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

  </body>
</html>