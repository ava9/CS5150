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
            <a class="navbar-brand" href="/cs5150/html/" id="navbrand"> Porchfest </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="/cs5150/html/browse"> Browse Porchfests </a>
                </li>
                <?php 
                    // user is logged in
                    if (isset($_SESSION['logged_user'])) {
                ?>
                        <li class='dropdown'>
                                <a href='#'' class='dropdown-toggle' data-toggle='dropdown'> My Account </a>
                                <ul class='dropdown-menu'>
                                    <li><a href='/cs5150/html/myporchfests'> My Porchfests </a></li>
                                    <li><a href='/cs5150/html/profile'> My Profile </a></li>
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