<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <?php require_once "../php/modules/stdHead.php" ?>
  <title>PorchFest - Home (Existing Porchfest??)</title>
</head>

<!-- BEGIN body -->
<body>
  
  <div class="container"> <!-- Container div -->
    <!-- navBar and login -->
    <?php require_once "../php/modules/login.php"; ?>
    <?php require_once "../php/modules/navigation.php"; ?>
    
    <div class="row">
      <h1 style="text-align:center;"> Create a Porchfest Website </h1>
    </div>

    <p> If you would like to link your existing Porchfest website, please fill out the form below. 
      <br>Filling out this form will create an account that you can log back into to manage your Porchfest.
    </p>

    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#myModal">
      Already have an account?
    </button>

    <form role="form" class="form-horizontal">
      <h4> Account Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Your Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" name="name" placeholder="John Doe" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Your Email</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="email" class="form-control" name="email" placeholder="johndoe@gmail.com" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="password" name="password" class="form-control" placeholder="Password" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Confirm Password </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="password" class="form-control" placeholder="Password" />
                  </div>
              </div>
          </div>
      </div>
      <br>
      <a href="/cs5150/html/"> Want to create a new Porchfest website? </a>
      <h4> Porchfest Information </h4>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Porchfest Name</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" name="porchfestname" placeholder="Ithaca Porchfest" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Existing Porchfest Website URL</label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="url" class="form-control" name="url" placeholder="ithacaporchfest.com" />
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
                      <input type="text" class="form-control" name="description" placeholder="John and Friends plays cool music." />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Location </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="text" class="form-control" name="location" placeholder="Location" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Date </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="date" class="form-control" name="date" placeholder="Date" />
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group">
          <label for="name" class="col-sm-2 control-label">
              Sign-up Deadline </label>
          <div class="col-sm-10">
              <div class="row">
                  <div class="col-md-9">
                      <input type="datetime-local" class="form-control" />
                  </div>
              </div>
          </div>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Submit</button>
    </form>
  </div> <!-- end container div -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0LuERw-moYeLnWy_55RoShmUbQ51Yh-o&libraries=places&callback=initAutocomplete"
        async defer></script>

</body>
</html>