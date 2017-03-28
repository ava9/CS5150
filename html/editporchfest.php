<?php session_start(); ?>
<html lang="en">
  <head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <!-- Responsive table js -->
    <script src="../js/responsive-tables.js"></script>

    <!-- Responsive tables CSS -->
    <link rel="stylesheet" href="./css/responsive-tables.css">
    <title>PorchFest - Edit</title>
  </head>

  <body>
    <div id="editalert"></div>

    <?php 
      require_once "../php/config.php";
      require_once "../php/modules/navigation.php";
      require_once "../php/modules/login.php";
      

      // Create connection
      // add DB_USER and DB_PASSWORD later
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    ?>
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
            <button type="button" id="save-timeslot-change" class="btn btn-success" data-dismiss="modal"> Save Changes </button>
            <button type="button" class="btn btn-danger" data-dismiss="modal"> Delete </button>
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
            <table class="responsive table timeslot-table"> <!-- begin table -->
              <tr>
                <th> </th>
                <th> Thu </th>
                <th> Fri </th>
                <th> Sat </th>
                <th> Sun </th>
                <th> Mon </th>
                <th> Tue </th>
                <th> Wed </th>
              </tr>
              <tr>
                <td class = "time"> 08:00 </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> -- </td>
              </tr>
              <tr>
                <td class = "time"> 09:00 </td>
                <td> -- </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"> </span> </td>
                <td> -- </td>
                <td> -- </td>
                <td> -- </td>
                <td> -- </td>
              </tr>
              <tr>
                <td class = "time"> 10:00 </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> -- </td>
              </tr>
              <tr>
                <td class = "time"> 11:00 </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
                <td> -- </td>
                <td> -- </td>
                <td> -- </td>
                <td> <span class="glyphicon glyphicon-ok"></span> </td>
              </tr>
            </table> <!-- end table -->
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

                    echo '<label> Porchfest Deadline Time (format as XX:XXpm or XX:XXam) </label>';
                    $deadline = date_create($porchfest['Deadline']);
                    $date = date_format($deadline, 'G:ia');
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
                  <th> Time Slots </th>
                  <th> Scheduled </th>
                  <th> Manage </th>
                </tr>
                <?php 
                  $sql = "SELECT * FROM `bandstoporchfests` INNER JOIN bands ON bands.BandID = bandstoporchfests.BandID  WHERE PorchfestID = 1 ORDER BY bands.Name";

                  $result = $conn->query($sql);

                  while($band = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td><a href="#"">' . $band['Name'] . '</a></td>';
                    echo '<td>' . $band['Description'] . '</td>';
                    echo '<td> List of members </td>';
                    echo '<td> <a data-target="#timeslotModal" data-toggle="modal"> Time Slots </a> </td>';
                    echo '<td>' . (is_null($band['TimeslotID']) ? 'No' : 'Yes') . '</td>';
                    echo '<td> <a href="#"> Edit </a> </td>';
                  }

                ?>

              </table> <!-- end table -->
            </div> <!-- end table-container div -->
          </div> <!-- end bands div -->

          <div class="tab-pane fade in active" id="timeslots"> <!-- begin timeslots div -->
            <div id="col-xs-12 timeslotheaders">
              Existing Timeslots
            </div>

            <?php
              $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '1' ORDER BY StartTime;";

              echo '<div id="timeslot-labels">';

              $result = $conn->query($sql);

              while($timeslot = $result->fetch_assoc()) {
                $start_time = date_create($timeslot['StartTime']);
                $end_time = date_create($timeslot['EndTime']);

                echo '<div class="col-xs-6 col-sm-3 timeslot-label"><span id="' . date_format($start_time, 'Y.m.d g:i A') . " - " . date_format($end_time, 'Y.m.d g:i A') . '" class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';

              }

              echo '</div>';

            ?>

            <div id="col-xs-12 timeslotheaders">
              Add Timeslot
            </div>

            <form role="form" class="form-horizontal" action="">
              <div id="addtimeslot" class="form-group">
                <label for="timeslot" class="control-label"> Start Time </label>
                  <input type="datetime-local">
                <label for="timeslot" class="control-label"> End Time </label>
                  <input type="datetime-local">
              </div>

              <button type="submit" class="btn btn-primary btn-sm"> Add Timeslot </button>

            </form>
             
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
    $('body').click(function() {
      $("#editalert").html('');
    });

    $('#save-timeslot-change').click(function() {
      var start = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[0].trim();
      var end = $('#edit-timeslot-modal').find('.modal-header').attr('id').split('-')[1].trim();
      var formData = {
          timeslotstart          : $('input[name=timeslot-start]').val(),
          timeslotend            : $('input[name=timeslot-end]').val(),
          start                  : start,
          end                    : end
      };

      $.ajax({
        url: "../php/ajax.php",
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
      var start_string = $(this).attr('id').split('-')[0].trim();
      var start = start_string.substring(start_string.indexOf(' '), start_string.length).trim();
      var end_string = $(this).attr('id').split('-')[1].trim();
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
        url: "../php/ajax.php",
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
        url: "../php/ajax.php",
        type: "GET",
        data: {bandname: $("#search").val()},
        success: function(result){
          $("#bandstable").html(result);
        }});
    });
  </script>

  </body>
</html>