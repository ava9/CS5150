function writenav() {
  var navbarhtml = `<!-- Navigation -->
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
                <a class="navbar-brand" href="index.html">Porchfest</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="browse.html"> Browse Porchfests </a>
                    </li>
                    <li>
                        <a href="myaccount.html"> My Account </a>
                    </li>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-link navbar-btn loginButton" data-toggle="modal" data-target="#myModal">
                      Login/Register
                    </button>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>`;
	document.write(navbarhtml);
}