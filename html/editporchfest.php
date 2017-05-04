<?php 
# This page is where an organizer can edit their porchfest information and do scheduling.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <!-- Responsive table js -->
    <script src="/cs5150/js/responsive-tables.js"></script>

    <script>
    // update when clickable tab elements with click. Used as an onclick function for the tabs.
    function enable(id) {
      $(id).css({pointerEvents: "auto"});
      $(".tab-pane:not(" + id + ")").css({pointerEvents: "none"});
    }
    </script>
    <!-- Responsive tables CSS -->
    <link rel="stylesheet" href="../css/responsive-tables.css">
    
    <title>PorchFest - Edit</title>
  </head>

  <body>
    <?php 
      require_once "../php/config.php";
      require_once "../php/routing.php";
      require_once "../php/modules/navigation.php";
      require_once "../php/modules/login.php";

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

      // returns an associative array. Each element in the array represents a band's conflicts.
      // The key is the band's bandID, the rest will be the id's of the conflicting bands
      // if they exist.
      // i.e. [bandid1 => [conflict1, conflict2, ...], bandid2 => [conflict1, ...], ...]
      function findConflicts($conn, $porchfestID) {
        $bandConflicts = array();

        $sql = "SELECT BandID FROM bandstoporchfests WHERE PorchfestID=" . $porchfestID;

        $result = $conn->query($sql);

        if (!$result) {
          throw new Exception('Query failed');
        }

        while ($bands = $result->fetch_assoc()) {
          $bandID = $bands['BandID'];

          $sql = sprintf("SELECT BandID2 FROM bandconflicts WHERE BandID1 = '%s'
                          UNION
                          SELECT BandID1 FROM bandconflicts WHERE BandID2 = '%s'", $bandID, $bandID);

          $result2 = $conn->query($sql);

          if (!$result2) {
            throw new Exception('Query failed');
          }

          $conflicts = array();

          while ($c = $result2->fetch_assoc()) {
            $conflictbandID = -1;
            try {
              $conflictbandID = $c['BandID2'];
            } catch (Exception $e) {
              $conflictbandID = $c['BandID1'];
            }
            array_push($conflicts, $conflictbandID);
          }

          $bandConflicts[$bandID] =  $conflicts;

        }

        return $bandConflicts;
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
                    echo '<textarea rows="5" id="porchfestdescription" class="form-control" placeholder="Porchfest Description">' . $porchfest['Description'] .  '</textarea>';
                    echo '<br />';
                    echo '</p>';

                    echo '<p>';
                    echo '<label> Porchfest Deadline Time (24 hr clock format) </label>';
                    $deadline = date_create($porchfest['Deadline']);
                    $date = date_format($deadline, 'H:i');
                    echo '<input data-validation="time" type="text" name="porchfesttime" class="form-control" value="' . $date . '" placeholder="Porchfest Deadline Time">';
                    echo '<br />';
                    echo '</p>';

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
                echo '<div class="btn-group" data-toggle="buttons">';

                create_filters($porchfestID, $conn);
                echo '<button id="email-bands-button" class="btn btn-primary btn-sm"> Email Selected Timeslots!</button>';
                echo '</div>';
              ?>
            <div class="col-xs-6 col-xs-offset-6 col-sm-4 col-sm-offset-8">
              <input id="search" name="search" type="text" placeholder="Search..."/>
            </div>

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
                    echo '<td> <a href="http://localhost/cs5150/html/edit/' . PORCHFEST_NICKNAME . '/' . 
                                $urlbandname . '"> Edit </a> </td>';
                    echo '<td> <a href="' . email_href($band['Name'], $band['Members']) . '" target="_blank"> Email </a> </td>'; 
                  }
                ?>

              </table> <!-- end table -->
            </div> <!-- end table-container div -->
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
                <form role="form" id="createtimeslot" class="form-horizontal" action="">
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
                echo '<div id="scheduletab-button">';
                echo '<button id="schedule-button" class="btn btn-primary btn-sm"> Schedule it!</button>';
                echo '</div>';
              } else {
                echo '<div id="scheduletab-conflictstable">';
                echo '<div class="btn-group" data-toggle="buttons">';
                
                create_filters($porchfestID, $conn);

                echo '</div>';

                echo '<div class="table-container table-responsive bands-table" id="bandstable"> <!-- begin table-container div -->
              <table class="responsive table"> <!-- begin table -->
                <tr class="fixed" data-status= "fixed">
                  <th> Name </th>
                  <th> Timeslots </th>
                  <th> Conflicts </th>
                </tr>';

                  $bandConflicts = findConflicts($conn, $porchfestID);

                  $result = $conn->query("SELECT Members FROM bands WHERE PorchfestID = '" . $porchfestID . "'");

                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";

                  $result = $conn->query($sql);

                  while($band = $result->fetch_assoc()) {
                    echo '<tr class="' . (is_null($band['TimeslotID']) ? '' : $band['TimeslotID']) . '">';
                    echo '<td>' . $band['Name'] . '</td>';
                    $sql2 = 'SELECT * FROM `porchfesttimeslots` INNER JOIN bandavailabletimes ON porchfesttimeslots.TimeslotID = bandavailabletimes.TimeslotID WHERE bandavailabletimes.bandID=' . $band['BandID'];

                    echo '<td><select class="timesdropdown" id="times-' . $band['BandID'] . ' ">';

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

                    echo '<td id="conflicts-' . $band['BandID'] .'"> No conflicts </td>';
                    
                  }

                  echo '</table> <!-- end table -->
                  </div> <!-- end table-container div -->';

                  echo '</div>';

              }
            ?>          
          </div> <!-- end schedule div -->

          <div class="tab-pane fade" id="export"> <!-- begin export div -->
            <form role="form" class="form-horizontal" id='submit-info-form' method='POST' action='/cs5150/php/export.php' target="_blank">
              <!-- TODO PRETTY THIS -->
              <input type = "hidden" name = "porchfestid" value = <?php echo $porchfestID ?> />
              <input type = "hidden" name = "mediatype" value = "csv" /> 
              <div class="form-group">
                <label class="col-sm-2"></label>
                <div class="col-sm-10">
                  <div class="col-md-9">
                    <button type="submit" name="exportCSV" class="btn btn-primary btn-sm"> Export CSV</button>
                    <button type="submit" name="exportKML" class="btn btn-primary btn-sm"> Export KML </button>
                  </div>
                </div>
              </div>
            </form>
          </div> <!-- end export div -->

          <div class="tab-pane fade" id="publish"> <!-- begin publish div -->
            <?php
              $sql = "SELECT Published from porchfests WHERE PorchfestID='" . $porchfestID . "'";
              $result = $conn->query($sql);
              $published = $result->fetch_assoc()['Published'];

              if (!$published) {
                echo '<button type="button" id="publishbutton" name="publishbutton" class="btn btn-default"> Publish </button>';
              }
              else {
                echo '<button type="button" id="publishbutton" name="publishbutton" class="btn btn-default"> Unpublish </button>';
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
    $("#manageporchfest").css("pointer-events", "auto");
    $(".tab-pane:not(" + "#manageporchfest" + ")").css({pointerEvents: "none"});

    // enable form validation
    $.validate({
      lang: 'en',
      modules : 'date'
    });

    // filter buttons for the Manage Bands tab.
    var bfilter = '#bandstable tr:not(.fixed)';
    // array of timeslotIDs, mapped to boolean
    // that indicates whether it is currently selected or not
    // used for emailing
    var timeslotIDs = {'mass_email': true}

    $('.filters').change(function() {
      // if the button is toggled, aka want to filter by this.
      var id = $(this).parent().attr('id');
      if($(this).parent().hasClass('active')) {
        $('#bandstable tr.' + id).show();
        bfilter = bfilter + ':not(tr.' + id + ')';
      } else {
        // need to remove this filter
        bfilter = bfilter.replace(':not(tr.' + id + ')', '');
      }

      $(bfilter).hide();

      if (bfilter == '#bandstable tr:not(.fixed)') {
        $('tr').show();
      }
      
      if (timeslotIDs[id] == null) {
        timeslotIDs[id] = true;
      }
      else {
        timeslotIDs[id] = !timeslotIDs[id];
      }
    });

    // filter buttons for the Schedule tab.
    var sfilter = '#schedule tr:not(.fixed)';

    $('.filters').change(function() {
      // if the button is toggled, aka want to filter by this.
      var id = $(this).parent().attr('id');
      if($(this).parent().hasClass('active')) {
        $('#schedule tr.' + id).show();
        sfilter = sfilter + ':not(tr.' + id + ')';
      } else {
        // need to remove this filter
        sfilter = sfilter.replace(':not(tr.' + id + ')', '');
      }

      $(sfilter).hide();

      if (sfilter == '#schedule tr:not(.fixed)') {
        $('tr').show();
      }
    });


    var ajaxurl = "/cs5150/php/ajax.php"; // the path to the ajax file.
    var porchfestid = "<?php echo $porchfestID; ?>";

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

    // ajax call for the Schedule tab, to determine if bands are conflicting.
    $('.timesdropdown').change(function() {
      var bandid = $(this).attr('id').split('-')[1];
      var timeslotid = $(this).val();
      var timeSlotInfo = {
        timeslotid   : timeslotid,
        porchfestid  : porchfestid,
        bandid       : bandid
      };

      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: timeSlotInfo,
        success: function(result){
          if (result == "overlap") {
            $('#conflicts-' + bandid).html("This timeslot is taken by a conflicting band.");
          } else {
            $('#conflicts-' + bandid).html("No conflicts");
          }
        },
        error: function(result) {
          console.log('error');
        }
      });

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
            } 
            else {
              $('#publishbutton').html("Publish");
            }
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your Porchfest was published/unpublished successfully. </div>');
          } else {
            $("#editalert").html('<div class="alert alert-danger alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Oops!</strong> Something went wrong, your request could not be submitted. Please try again. </div>');
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

    // submit the form on Manage Porchfest.
    $("#porchfestmanagesubmit").submit(function(event){
      var formData = {
          porchfestname          : $('input[name=porchfestname]').val(),
          porchfestlocation      : $('input[name=porchfestlocation]').val(),
          porchfestdate          : $('input[name=porchfestdate]').val(),
          porchfestdescription   : $('#porchfestdescription').val(),
          porchfesttime          : $('input[name=porchfesttime]').val(),
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

    // the search bar in Manage Bands. AJAX call with the given input, displays data from the DB.
    $("#search").keyup(function(){
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: {bandname: $("#search").val(), porchfestid: porchfestid},
        success: function(result){
          $("#bandstable").html(result);

          // reapply the filters to the search results.
          $(bfilter).hide();

          if (bfilter == '#bandstable tr:not(.fixed)') {
            $('tr').show();
          }
        }});
    });

    $('#schedule-button').click(function(){
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {porchfestid: porchfestid, schedule: 1},
        success: function(result){
          console.log('Scheduled!');
          console.log(result);
        },
        error: function(result) {
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