<?php session_start(); ?>
<html>
  <head>
    <?php require_once "../php/modules/stdHead.php" ?>
    <!-- Responsive table js -->
    <script src="../js/responsive-tables.js"></script>

    <!-- Responsive tables CSS -->
    <link rel="stylesheet" href="./css/responsive-tables.css">
    <title>PorchFest - Edit</title>
  </head>

  <body>
    <?php 
      require_once "../php/config.php";

      // Create connection
      // add DB_USER and DB_PASSWORD later
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    ?>
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>

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
          <div class="tab-pane fade in active" id="manageporchfest"> <!-- begin manageporchfest div -->
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

          <div class="tab-pane fade" id="timeslots"> <!-- begin timeslots div -->
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

                echo '<div class="col-xs-6 col-sm-3 timeslot-label"><span class="label label-primary">' . date_format($start_time, 'g:i A') . " - " . date_format($end_time, 'g:i A')  . ' </span></div>';
                
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
          console.log(result);
        },
        error: function(result) {
          console.log(result);
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