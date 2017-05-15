<!-- Form for creating an account -->
<h4> Account Information </h4>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Your Name</label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="alphanumeric" data-validation-allowing="-_ " data-validation="length" data-validation-length="min1" required type="text" class="form-control" name="name" placeholder="John Doe" /> <?php echo '<span class="error">'; echo $nameError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Your Email</label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input data-validation="email" required type="email" class="form-control" name="email" placeholder="johndoe@gmail.com" /> <?php echo '<span class="error">'; echo $emailError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Mobile</label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required data-validation="custom" data-validation-regexp="^[0-9]{3}-[0-9]{3}-[0-9]{4}$" data-validation-help="Please format the number as xxx-xxx-xxxx" required type="tel" class="form-control" name="mobile" placeholder="(123) 456-7891" /> <?php echo '<span class="error">'; echo $mobileError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Password </label> 
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required data-validation="length" data-validation-length="min5" required type="password" name="password" class="form-control" placeholder="Password" /> <?php echo '<span class="error">'; echo $passwordError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label"> Confirm Password </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-9">
                <input required data-validation="length" data-validation-length="min5" required type="password" name="confirmPassword" class="form-control" placeholder="Password" /> <?php echo '<span class="error">'; echo $confirmPasswordError; echo '</span>'; ?>
            </div>
        </div>
    </div>
</div>
<br>