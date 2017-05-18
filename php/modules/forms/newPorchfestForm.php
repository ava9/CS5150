<!-- Form for submitting porchfest information -->
<h4> Porchfest Information </h4>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Porchfest Name</label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="length" data-validation-length="min1" required type="text" name="porchfestName" class="form-control" placeholder="Ithaca Porchfest" /> <?php echo '<span class="error">'; echo $porchfestNameError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">
        Nickname <?php tooltip("This is the name that will appear in the url for attendees/musicians. For example, for the Ithaca Porchfest with nickname “ithaca”, the url will appear as porchfest.life/view/ithaca. THIS FIELD CANNOT CHANGE ONCE INPUTTED.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="length" data-validation-length="min1" required type="text" class="form-control" name="nickname" placeholder="Ithaca Porchfest" /> <?php echo '<span class="error">'; echo $nicknameError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">
        Description </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required type="text" class="form-control" name="description" placeholder="John and Friends plays cool music." /> <?php echo '<span class="error">'; echo $descriptionError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Location <?php tooltip("The city or town in which the Porchfest event will be held. When typing the location, please select from the resulting dropdown so that the corresponding pin can be placed on a Google map.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required id="autocomplete" name="location" class="form-control" placeholder="Enter your address" onFocus="geolocate()" type="text"></input> <?php echo '<span class="error">'; echo $locationError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Date <?php tooltip("Date when the Porchfest will be held.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="date" required type="date" name="date" class="form-control" placeholder="Date" /> <?php echo '<span class="error">'; echo $dateError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> 
      Sign-up Deadline <?php tooltip("Day by which bands must register to play at this Porchfest event. Default time for the deadline in 11:59pm on this day.") ?> </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="date" required type="date" name="deadline" lass="form-control" placeholder="Deadline" /> <?php echo '<span class="error">'; echo $deadlineError; echo '</span>'; ?>
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