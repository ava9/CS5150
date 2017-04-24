<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <!-- Responsive table js -->
    <script src="http://localhost/cs5150/js/responsive-tables.js"></script>

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

      $sql = sprintf("SELECT PorchfestID FROM porchfests WHERE porchfests.Name = '%s'", PORCHFEST_NAME);
      $result = $conn->query($sql);
      $porchfestID = $result->fetch_assoc()['PorchfestID'];

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
                    echo '<input data-validation="length" data-validation-length="min4"type="text" name="porchfestname" class="form-control" value="' . $porchfest['Name'] . '" placeholder="Porchfest Name">';
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
            <div class="col-xs-offset-6 col-xs-6 col-sm-offset-9 col-sm-3">
              <input id="search" name="search" type="text" placeholder="Search..."/>
            </div>

            <div class="table-container table-responsive bands-table" id="bandstable"> <!-- begin table-container div -->
              <table class="responsive table"> <!-- begin table -->
                <tr data-status= "fixed">
                  <th> Name </th>
                  <th> Description </th>
                  <th> Members </th>
                  <th> Timeslots </th>
                  <th> Scheduled </th>
                  <th> Manage </th>
                  <th> Contact </th>
                </tr>
                <?php 
                  // Given a band name and SQL connection, get the registered emails of the band and 
                  // return a mailto link to them

                  function email_href($conn, $name) {
                    $result = $conn->query("SELECT Members FROM bands WHERE Name = '" . $name . "'");
                    $band = $result->fetch_assoc();
                    $members = explode(',', $band['Members']);

                    $recipient = $members[0];
                    $cc = '';
                    unset($members[0]);
                    foreach ($members as $key => $value) {
                      $cc = $cc . $value . ',';
                    }

                    $subject = sprintf("[Porchfest] %s", $name);
                    return sprintf("mailto:%s?cc=%s&subject=%s", $recipient, $cc, $subject);
                  }


                  $result = $conn->query("SELECT Members FROM bands WHERE PorchfestID = '" . $porchfestID . "'");

                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";

                  $result = $conn->query($sql);
                  

                  while($band = $result->fetch_assoc()) {
                    $bandname = $band['Name'];
                    // Modify the band name such that it looks good in the URL.
                    // All spaces (' ') become '-' and all '-' become '--'.
                    $urlbandname = str_replace(" ", "-", str_replace("-", "--", $bandname));

                    echo '<tr>';
                    echo '<td>' . $band['Name'] . '</td>';
                    echo '<td>' . $band['Description'] . '</td>';
                    echo '<td> List of members </td>';
                    echo '<td> <a data-target="#timeslotModal' . $band['BandID'] . '" data-toggle="modal"> Time Slots </a> </td>';
                    echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
                    echo '<td> <a href="http://localhost/cs5150/html/edit/' . PORCHFEST_NICKNAME . '/' . 
                                $urlbandname . '"> Edit </a> </td>';
                    echo '<td> <a href="' . email_href($conn, $bandname) . '" target="_blank"> Email </a> </td>'; 
                  }
                ?>

              </table> <!-- end table -->
            </div> <!-- end table-container div -->
          </div> <!-- end bands div -->

          <div class="tab-pane fade" id="timeslots"> <!-- begin timeslots div -->
            <div class="col-xs-12" id="timeslotheaders">
              Existing Timeslots
            </div>

            <?php
              $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $porchfestID . "' ORDER BY StartTime;";

              $result = $conn->query($sql);

              while($timeslot = $result->fetch_assoc()) {
                $start_time = date_create($timeslot['StartTime']);
                $end_time = date_create($timeslot['EndTime']);

                echo '<div class="col-xs-6 col-sm-3 timeslot-label"><span id="' . $timeslot['TimeslotID'] . '-' . date_format($start_time, 'Y.m.d-g:iA') . "-" . date_format($end_time, 'Y.m.d-g:iA') . '" class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';
              }

            ?>

            <div class="col-xs-12" id="timeslotheaders">
                Add a Timeslot
            </div>

            <div class="col-xs-12">
              <form role="form" id="createtimeslot" class="form-horizontal" action="">
                <div class="col-xs-6">
                  <label for="timeslot"> Start Time </label>
                    <input type="time">
                </div>
                <div class="col-xs-6">
                  <label for="timeslot"> End Time </label>
                    <input type="time">
                </div>
                <div class="col-xs-offset-9 col-xs-3 timeslot-button">
                  <button type="submit" class="btn btn-primary btn-sm"> Add Timeslot </button>
                </div>

              </form>
            </div>

            
             
          </div> <!-- end timeslots div -->
          <div class="tab-pane fade" id="schedule"> <!-- begin schedule div -->
            <div class="table-container table-responsive bands-table" id="bandstable"> <!-- begin table-container div -->
              <table class="responsive table"> <!-- begin table -->
                <tr data-status= "fixed">
                  <th> Name </th>
                  <th> Timeslots </th>
                  <th> Conflicts </th>
                </tr>
                <?php 
                  $result = $conn->query("SELECT Members FROM bands WHERE PorchfestID = '" . $porchfestID . "'");

                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = '" . $porchfestID . "' ORDER BY bands.Name";

                  $result = $conn->query($sql);

                  while($band = $result->fetch_assoc()) {
                    echo '<tr>';
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

                ?>

              </table> <!-- end table -->
            </div> <!-- end table-container div -->
          </div> <!-- end schedule div -->

          <div class="tab-pane fade" id="export"> <!-- begin export div -->
            <form role="form" class="form-horizontal" id='submit-info-form' method='POST' action='/cs5150/php/export.php'>
              <!-- TODO PRETTY THIS -->
              <input type = "hidden" name = "porchfestid" value = <?php echo $porchfestID ?> />
              <input type = "hidden" name = "mediatype" value = "csv" /> 
              <div class="form-group">
                <label class="col-sm-2"></label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-md-9">
                          <button type="submit" name="submitInfo" class="btn btn-primary btn-sm"> Export </button>
                        </div>
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
    
    $("#manageporchfest").css("pointer-events", "auto");
    $(".tab-pane:not(" + "#manageporchfest" + ")").css({pointerEvents: "none"});

    $.validate({
      lang: 'en',
      modules : 'date'
    });

    function enable(id) {
      $(id).css({pointerEvents: "auto"});
      $(".tab-pane:not(" + id + ")").css({pointerEvents: "none"});
    }

    var ajaxurl = "http://localhost/cs5150/php/ajax.php";
    var porchfestid = "<?php echo $porchfestID; ?>";

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
            $('#conflicts-' + bandid).html("This timeslot is taken by another band.");
          } else {
            $('#conflicts-' + bandid).html("No conflicts");
          }
        },
        error: function(result) {
          console.log('error');
        }
      });

    });

    $('body').click(function() {
      $("#editalert").html('');
    });

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

    $('#delete-timeslot').click(function(){
      var tid = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {tid: tid},
        success: function(result){
          if (result == "success") {
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The timeslot was deleted successfully. </div>');
          } else {
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

    $('#save-timeslot-change').click(function() {
      var timeslotid = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();
      var start = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[2].trim();
      var end = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[4].trim();

      var year = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[1].trim();

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

    $('.label').click(function() {
      var start = $(this).attr('id').split('-')[2].trim();
      var end = $(this).attr('id').split('-')[4].trim();

      $('#edit-timeslot-modal').find('.modal-header').html(start + ' - ' + end);
      $('#edit-timeslot-modal').find('.modal-header').attr('id', $(this).attr('id'));

      $('#edit-timeslot-modal').find('.modal-body').html('<form id="timeslot-form"><input type="text" name="timeslot-start" class="form-control" value="' + start + '" placeholder="Start Time"><input type="text" name="timeslot-end" class="form-control" value="' + end + '" placeholder="End Time"></form>');
      $('#edit-timeslot-modal').modal('show');
    });

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

    $("#search").keyup(function(){
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: {bandname: $("#search").val(), porchfestid: porchfestid},
        success: function(result){
          $("#bandstable").html(result);
        }});
    });
  </script>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

  </body>
</html>