<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php" id="navbrand"> Porchfest </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="browse.php"> Browse Porchfests </a>
                </li>
                <?php 
                    if (isset($_POST['logout'])) {
                    // logout button pressed
                        unset($_SESSION['logged_user']);
                        unset($_POST['logout']);
                    }

                    if (isset($_POST['login'])) {
                    // logged_user not set, but email and password were entered

                        $email = $_POST['email'];
                        $sanitized_username = filter_var($email, FILTER_SANITIZE_STRING);
                        require_once('../php/config.php');
                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                        // future hashing
                        // $password = hash("md5", ($_POST['password'] . SALT));
                        $password = $_POST['password'];
                        $result = $mysqli->query("SELECT password FROM users WHERE email = '$email'");
                        $row = $result->fetch_row();
                        if ($row[0] == $password) {
                             $_SESSION['logged_user'] = $sanitized_username;
                            unset($_POST['login']);
                        } else {
                            //return alert

                            // Want to alert in a different way??? Check
                            echo "<script type='text/javascript'>alert('Login failed. Try again.');</script>";
                        }

       
                    } 
                    // user is logged in
                    if (isset($_SESSION['logged_user'])) {
                ?>
                        <li class='dropdown'>
                                <a href='#'' class='dropdown-toggle' data-toggle='dropdown'> My Account </a>
                                <ul class='dropdown-menu'>
                                    <li><a href='myporchfests.php'> My Porchfests </a></li>
                                    <li><a href='myprofile.php'> My Profile </a></li>
                                </ul>
                        </li>
                        
                        <li><form role="form" id='logout-button' method='POST' action = "#">
                            <button type='submit' class='btn btn-link navbar-btn' name="logout" >
                            Logout
                            </button>
                        </form></li>
                        
                <?php
                    } else {
                ?>
                        <!-- Button trigger modal -->
                        <li><button type='button' class='btn btn-link navbar-btn loginButton' data-toggle='modal' data-target='#myModal'>
                          Login
                        </button></li>

                <?php
                    }
                ?>
<!--                 <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> My Account </a>
                        <ul class="dropdown-menu">
                            <li><a href="myporchfests.php"> My Porchfests </a></li>
                            <li><a href="myprofile.php"> My Profile </a></li>
                        </ul>
                </li> -->
                <!-- Button trigger modal -->
                <!-- <button type="button" class="btn btn-link navbar-btn loginButton" data-toggle="modal" data-target="#myModal">
                  Login
                </button> -->
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>