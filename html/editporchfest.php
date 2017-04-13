<?php session_start(); ?>
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
      require_once "../php/modules/navigation.php";
      require_once "../php/modules/login.php";
      

      // Create connection
      // add DB_USER and DB_PASSWORD later
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

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

    <!-- Modal -->
    <div id="timeslotModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"> Time Slots </h4>
          </div>
          <div class="modal-body">
            <?php 
              $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID='1' ORDER BY StartTime";
              $result = $conn->query($sql);
              while($timeslot = $result->fetch_assoc()) {
                $sql2 = "SELECT * FROM bandavailabletimes
                         WHERE BandID = '1' AND TimeslotID = '" . $timeslot['TimeslotID'] . "'";
                $result2 = $conn->query($sql2);
                $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
                $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
                $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
                if ($result2->num_rows > 0) {
                  echo $starttime . "-" . $endtime . " on " . $day . "<br>";
                }
              }
            ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"> Close </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container"> <!-- begin container div -->
      <div class="row"> <!-- begin row 1 div -->
        <div class="col-sm-2"> <!-- begin col 1 div -->
            <nav class="nav-sidebar">
                <ul class="nav">
                  <li class="active"><a href="#manageporchfest" data-toggle="tab"> Manage Porchfest </a></li>
                  <li><a href="#bands" data-toggle="tab"> Manage Bands </a></li>
                  <li><a href="#timeslots" data-toggle="tab"> Manage Time Slots </a></li>
                  <li><a href="#schedule" data-toggle="tab"> Schedule </a></li>
                  <li><a href="#publish" data-toggle="tab"> Publish </a></li>
                  <li class="nav-divider"></li>
                </ul>
            </nav>
        </div> <!-- end col 1 div -->

        <div class="col-sm-10"> <!-- begin col 2 div -->
          <div class="tab-pane fade" id="manageporchfest"> <!-- begin manageporchfest div -->
            <div id="porchfestinfo"> <!-- begin porchfestinfo div -->
              <div class="input-group"> <!-- begin input-group div -->
                <form action="editporchfest.php" method="POST" id="porchfestmanagesubmit">
                  <?php 
                    $sql = "SELECT * FROM `porchfests` WHERE PorchfestID = 1";

                    $result = $conn->query($sql);

                    $porchfest = $result->fetch_assoc();

                    echo '<label> Porchfest Name </label>';
                    echo '<input type="text" name="porchfestname" class="form-control" value="' . $porchfest['Name'] . '" placeholder="Porchfest Name">';
                    echo '<br />';

                    echo '<label> Porchfest Location </label>';
                    echo '<input type="text" name="porchfestlocation" class="form-control" value="' . $porchfest['Location'] . '" placeholder="Porchfest Location">';
                    echo '<br />';

                    echo '<label> Porchfest Date </label>';
                    echo '<input type="date" name="porchfestdate" class="form-control" value="' . $porchfest['Date'] . '" placeholder="Porchfest Date">';
                    echo '<br />';

                    echo '<label> Porchfest Description </label>';
                    echo '<textarea rows="5" id="porchfestdescription" class="form-control" placeholder="Porchfest Description">' . $porchfest['Description'] .  '</textarea>';
                    echo '<br />';

                    echo '<label> Porchfest Deadline Time (24 hr clock format) </label>';
                    $deadline = date_create($porchfest['Deadline']);
                    $date = date_format($deadline, 'H:i');
                    echo '<input type="text" name="porchfesttime" class="form-control" value="' . $date . '" placeholder="Porchfest Deadline Time">';
                    echo '<br />';

                    echo '<label> Porchfest Deadline Date </label>';
                    $day = new DateTime(date_format($deadline, 'Y-m-d')); // to drop the time
                    echo '<input type="date" name="porchfestdeadlineday" class="form-control" value="' . $day->format('Y-m-d') . '" placeholder="Porchfest Deadline Date">';
                    echo '<br />';
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


                  $result = $conn->query("SELECT Members FROM bands WHERE PorchfestID = 1");

                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = 1 ORDER BY bands.Name";

                  $result = $conn->query($sql);

                  while($band = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $band['Name'] . '</td>';
                    echo '<td>' . $band['Description'] . '</td>';
                    echo '<td> List of members </td>';
                    echo '<td> <a data-target="#timeslotModal" data-toggle="modal"> Time Slots </a> </td>';
                    echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
                    echo '<td> <a href="../editband.php"> Edit </a> </td>';
                    echo '<td> <a href="' . email_href($conn, $band['Name']) . '" target="_blank"> Email </a> </td>';
                  }

                ?>

              </table> <!-- end table -->
            </div> <!-- end table-container div -->
          </div> <!-- end bands div -->

          <div class="tab-pane fade in active" id="timeslots"> <!-- begin timeslots div -->
            <div class="col-xs-12" id="timeslotheaders">
              Existing Timeslots
            </div>

            <?php
              $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '1' ORDER BY StartTime;";

              $result = $conn->query($sql);

              while($timeslot = $result->fetch_assoc()) {
                $start_time = date_create($timeslot['StartTime']);
                $end_time = date_create($timeslot['EndTime']);

                echo '<div class="col-xs-6 col-sm-3 timeslot-label"><span id="' . $timeslot['TimeslotID'] . '-' . date_format($start_time, 'Y.m.d g:i A') . " - " . date_format($end_time, 'Y.m.d g:i A') . '" class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';

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
            schedule
          </div> <!-- end schedule div -->
          <div class="tab-pane fade" id="publish"> <!-- begin publish div -->
            publish
          </div> <!-- end publish div -->

        </div> <!-- end col 2 div -->
      </div> <!-- end row 1 div -->
    </div> <!-- end container div -->


  <script>
    var ajaxurl = "http://localhost/cs5150/php/ajax.php";
    $('body').click(function() {
      $("#editalert").html('');
    });

    $('#delete-timeslot').click(function(){
      console.log('here1');
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

    $('#save-timeslot-change').click(function() {
      var timeslotid = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();
      var start = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[1].trim();
      var end = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[2].trim();
      var formData = {
          timeslotid             : timeslotid,
          timeslotstart          : $('input[name=timeslot-start]').val(),
          timeslotend            : $('input[name=timeslot-end]').val(),
          start                  : start,
          end                    : end
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: formData,
        success: function(result){
          console.log(result);
          if (result == "success") {
            var id = $('#edit-timeslot-modal').find('.modal-header').attr('id');
            console.log($('#' + id).length);
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> Your Porchfest information was updated successfully. </div>');
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
      var start_string = $(this).attr('id').split('-')[1].trim();
      var start = start_string.substring(start_string.indexOf(' '), start_string.length).trim();
      var end_string = $(this).attr('id').split('-')[2].trim();
      var end = end_string.substring(end_string.indexOf(' '), end_string.length).trim();

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
          porchfestdeadlineday   : $('input[name=porchfestdeadlineday]').val()
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: formData,
        success: function(result){
          if (result == "success") {
            $("#editalert").html('<div class="alert alert-success alert-dismissable"> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a> <strong>Success!</strong> The timeslot was updated successfully. </div>');
          } else {
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
      console.log('here');
      $.ajax({
        url: ajaxurl,
        type: "GET",
        data: {bandname: $("#search").val()},
        success: function(result){
          $("#bandstable").html(result);
        }});
    });
  </script>

  </body>
</html>