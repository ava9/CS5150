<!-- Form for signing up a band -->
<h4> Band Information </h4>
<!-- Form for submitting band information -->
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Band Name <?php tooltip("Name as it will appear on the Porchfest website and schedule.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="length" data-validation-length="min1" required name="bandname" type="text" class="form-control" placeholder="John and Friends" /> <?php echo '<span class="error">'; echo $bandnameError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Description <?php tooltip("1-2 sentence description of your band/music/genre.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required name="banddescription" type="text" class="form-control" placeholder="John and Friends plays cool music." /> <?php echo '<span class="error">'; echo $descriptionError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">
        Band Website URL <?php tooltip("If you have a website for your band, paste the url here so people can view it.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="url" type="url" class="form-control" name="bandURL" placeholder="http://www.porchfest.org" data-validation-optional="true" /> <?php echo '<span class="error">'; echo $urlError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Porch Location <?php tooltip("The address where your band will play during this Porchfest event. When typing the address, please select from the resulting dropdown so that the location pin can be placed on a Google map.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required name="porchlocation" id="autocomplete" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input> <?php echo '<span class="error">'; echo $locationError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Available Times </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
    <?php
      // Create connection
      $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Get the available timeslots for the porchfest
      $sql = "SELECT * FROM porchfesttimeslots WHERE PorchfestID = '" . $porchfestID . "' ORDER BY StartTime;";

      $result = $conn->query($sql);
      while($timeslot = $result->fetch_assoc()) {
        $starttime = date_format(date_create($timeslot['StartTime']), 'g:iA');
        $endtime = date_format(date_create($timeslot['EndTime']), 'g:iA');
        $day = date_format(date_create($timeslot['StartTime']), 'F j, Y');
        echo "<input name='available[]' type='checkbox' value='" . $timeslot['TimeslotID'] . "' />" . " " . $starttime . 
              "-" . $endtime . " on " . $day . "<br>";
      }
    ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Band Member Emails <?php tooltip("Please provide all email addresses of the band members as points of contact for the organizer of this Porchfest event.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input name="bandmembers" type="text" class="form-control" placeholder="member1@gmail.com, member2@gmail.com, member3@gmail.com" />
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Conflicting Bands <?php tooltip("Please select all bands that conflict with your band. When you type the conflicting band name, it will either appear in the dropdown below and you can select it, or you will have the option to “add/register” the conflicting band. 
      Conflicts are those bands that you cannot play at the same time as, i.e. because they will play at the same location or you share a member. If you do not see the conflicting band in the dropdown, then that means that band has not signed up yet and you must make sure they
      list you as a conflict when they register their band.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input name="bandconflicts" id="conflict-input" type="text" class="form-control" placeholder="Band1,Band2,Band3" />
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Comments </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input name="bandcomment" type="text" class="form-control" placeholder="Any additional comments" />
            </div>
        </div>
    </div>
</div>
<div class="form-group">
  <label class="col-sm-2"></label>
  <div class="col-sm-10">
      <div class="row">
          <div class="col-md-9">
            <button type="submit" name="submitInfo" class="btn btn-primary btn-sm"> Submit </button>
          </div>
      </div>
  </div>
</div>  