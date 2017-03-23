<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PorchFest - Edit</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

      <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Navigation bar -->
    <script src="../js/navbar.es6"></script>

    <!-- Login modal -->
    <script src="../js/loginmodal.es6"></script>

    <!-- Responsive table js -->
    <script src="responsive-tables.js"></script>

    <!-- Responsive tables CSS -->
    <link rel="stylesheet" href="responsive-tables.css">
  </head>

  <body>
    <?php 
      require_once "../php/config.php";

      // Create connection
      // add DB_USER and DB_PASSWORD later
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    ?>


    <script type="text/javascript">writenav();</script>

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
                    <li class="active"><a href="#bands" data-toggle="tab"> Manage Bands </a></li>
                    <li><a href="#timeslots" data-toggle="tab"> Manage Time Slots </a></li>
                    <li><a href="#schedule" data-toggle="tab"> Schedule </a></li>
                    <li><a href="#publish" data-toggle="tab"> Publish </a></li>
                    <li class="nav-divider"></li>
                    <li><a href="#date" data-toggle="tab"> Search </a></li>
                </ul>
            </nav>
        </div> <!-- end col 1 div -->

        <div class="col-sm-10"> <!-- begin col 2 div -->
          <div class="tab-pane fade" id="bands"> <!-- begin bands div -->
            <div class="col-xs-12">
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
            Add/Edit Timeslots for your Porchfest

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

            <form role="form" class="form-horizontal" action="">
              <div id="addtimeslot" class="form-group">
                <label for="timeslot" class="control-label"> Start Time </label>
                  <input type="datetime-local">
                <label for="timeslot" class="control-label"> End Time </label>
                  <input type="datetime-local">
              </div>

              <button type="submit" class="btn btn-primary btn-sm"> Add Timeslot</button>

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
    $("#search").keyup(function(){
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