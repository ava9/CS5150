<?php
    require_once("stdHead.php");
?>

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
            <?php
                echo '<a class="navbar-brand" href="' . INDEX_URL . '" id="navbrand"> Porchfest </a>';
            ?>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-inverse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-inverse navbar-nav navbar-right">
                <li>
                    <?php create_hyperlink(BROWSE_PORCHFEST_URL, 'Browse Porchfests'); ?>
                </li>
                <?php 
                    // user is logged in
                    if (isset($_SESSION['logged_user'])) {
                ?>
                        <li class='dropdown'>
                                <a href='#' class='dropdown-toggle' data-toggle='dropdown'> My Account </a>
                                <ul class='dropdown-menu'>
                                    <li><?php create_hyperlink(MY_PORCHFEST_URL, 'My Porchfests'); ?></li>
                                    <li><?php create_hyperlink(PROFILE_URL, 'My Profile'); ?></li>
                                </ul>
                        </li>
                        
                        <li><form role="form" id='logout-button' method='POST' action = "/cs5150/html/">
                            <button type='submit' class='btn btn-link navbar-btn loginButton' name="logout" >
                            Logout
                            </button>
                        </form></li>
                        
                <?php
                    } else {
                ?>
                        <!-- Button trigger modal -->
                        <li><button type='button' class='btn btn-link navbar-btn loginButton' data-toggle='modal' data-target='#loginModal'>
                          Login
                        </button></li>

                <?php
                    }
                ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>