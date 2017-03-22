<!DOCTYPE html>
<html lang="en">

<!-- BEGIN head -->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PorchFest - Home</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue_grey-purple.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <!-- Bootstrap Core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet">

  <script src="../js/navbar.es6"></script>
  <script src="../js/loginmodal.es6"></script>
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <script src="../js/jquery.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<!-- BEGIN body -->
<body>
  <?php // Database credentials
    require_once "../php/config.php";

    // Create connection
    // add DB_USER and DB_PASSWORD later
    $conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  ?>

  <div class="container"> <!-- Container div -->
    <script type="text/javascript">writeloginmodal();</script>

    <div class="row">
      <script type="text/javascript">writenav();</script>
    </div>
    
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
      <a href="index.php"> Want to create a new Porchfest website? </a>
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


</body>
</html>