<!-- Form for editing account information with current information from database displayed -->
<h3>Personal Info</h3>
<form class="form-horizontal row-centered" role="form" method="post">
  <div class="form-group">
    <label class="col-lg-3 control-label">Name:</label>
    <div class="col-lg-8">
      <input class="form-control" data-validation="alphanumeric" data-validation-allowing="-_ " data-validation="length" data-validation-length="min1" name="name" value=<?php echo '"' . $user['Name'] . '"' ?> type="text"> <?php echo '<span class="error">'; echo $nameError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Email:</label>
    <div class="col-lg-8">
      <input data-validation="email" required class="form-control" name="email" value=<?php echo '"' . $user['Email'] . '"' ?> type="email"> <?php echo '<span class="error">'; echo $emailError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-lg-3 control-label">Mobile (xxx-xxx-xxxx):</label>
    <div class="col-lg-8">
      <input required data-validation="custom" data-validation-regexp="^[0-9]{3}-[0-9]{3}-[0-9]{4}$" data-validation-help="Please format the number as xxx-xxx-xxxx" class="form-control" name="mobile" value=<?php echo '"' . $user['ContactInfo'] . '"' ?> type="tel"> <?php echo '<span class="error">'; echo $mobileError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Current Password:</label>
    <div class="col-md-8">
      <input required data-validation="length" data-validation-length="min5" class="form-control" name="currPassword" type="password"> <?php echo '<span class="error">'; echo $oldPasswordError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">New Password:</label>
    <div class="col-md-8">
      <input required data-validation="length" data-validation-length="min5" class="form-control" name="password" type="password"> <?php echo '<span class="error">'; echo $passwordError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Confirm New Password:</label>
    <div class="col-md-8">
      <input required data-validation="length" data-validation-length="min5" class="form-control" name="confirmPassword" type="password"> <?php echo '<span class="error">'; echo $confirmPasswordError; echo '</span>'; ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"></label>
    <div class="col-md-8">
      <input class="btn btn-primary" name="submitInfo" value="Save Changes" type="submit">
      <span></span>
      <input class="btn btn-default" value="Cancel" type="reset">
    </div>
  </div>
</form>