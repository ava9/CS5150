<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Porchfest - Home</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <script src="../js/navbar.es6"></script>

    <script src="../js/loginmodal.es6"></script>

    <!-- Adapted from https://github.com/BlackrockDigital/startbootstrap-business-frontpage -->

</head>

<body>


    <script type="text/javascript">writenav();</script>

    <script type="text/javascript">writeloginmodal();</script>

    <!-- Carousel -->
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
	  <!-- Indicators -->
	  <ol class="carousel-indicators">
	    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
	    <li data-target="#myCarousel" data-slide-to="1"></li>
	    <li data-target="#myCarousel" data-slide-to="2"></li>
	  </ol>

	  <!-- Wrapper for slides -->
	   <div class="carousel-inner" role="listbox">
	    <div class="item active">
	      <img src="../img/band.jpg" alt="Band">
	    </div>

	    <div class="item">
	      <img src="../img/kids.jpg" alt="Kids">
	    </div>

	    <div class="item">
	      <img src="../img/violin.jpg" alt="Violin">
	    </div>
	   </div>

      <!-- Left and right controls -->
      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div> <!-- End of Carousel -->

    <!-- Page Content -->
    <div class="container" id="info" name="info">
    
    <hr>

		<div class="row pageContent">
			<div class="col-md-9">
				<h2>Celebrating community through music on the porches of Fall Creek & Northside!</h2>
				<p> Porchfest began in 2007, inspired by some outdoor ukulele playing and a conversation between neighbors Gretchen Hildreth and Lesley Greene. They came up with the idea for it that day and gathered 20 bands to make it happen in September of that year. The number of bands has increased every year since then, with 185 in 2016. </p>

				<p>The team has grown too. Andy Adelewitz mercifully joined the organizing team in 2013. Lesley’s husband Robbert wrote software to assist in scheduling the bands in 2014. We now get help on the day of Porchfest from several dozen volunteers. </p>

				<p> We have received sponsorship support from Ithaca Neighborhood Housing Services since 2011, which has been extremely helpful, as Lesley and Gretchen were paying out of pocket for most of the expenses before that. We receive generous donations from the community as well that we collect at Thompson Park during Porchfest and online through our website. And let’s not forget to mention the many bands who play each year for their neighbors and visitors. That’s what it’s really all about! </p>
			</div>
			<div class="col-sm-3 contact">
				<h3>Contact Us</h3>
				<address>
					<strong> Porchfest </strong>
					<br>3481 State Street
					<br>Ithaca, NY 14850
					<br>
				</address>
				<address>
					<abbr title="Phone">P: </abbr>(123) 456-7890
					<br>
					<abbr title="Email">E:</abbr> <a href="mailto:#">info@porchfest.org</a>
				</address>
			</div>
		</div>
		<!-- /.row -->

	<hr>

        <div class="row">
            <div class="col-sm-4">
                <img class="img-circle img-responsive img-center" src="http://placehold.it/300x300" alt="">
                <h2>Marketing Box #1</h2>
                <p>These marketing boxes are a great place to put some information. These can contain summaries of what the company does, promotional information, or anything else that is relevant to the company. These will usually be below-the-fold.</p>
            </div>
            <div class="col-sm-4">
                <img class="img-circle img-responsive img-center" src="http://placehold.it/300x300" alt="">
                <h2>Marketing Box #2</h2>
                <p>The images are set to be circular and responsive. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
            </div>
            <div class="col-sm-4">
                <img class="img-circle img-responsive img-center" src="http://placehold.it/300x300" alt="">
                <h2>Marketing Box #3</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
            </div>
        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Porchfest 2017</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="../js/jquery.js"></script>

    <script type="text/javascript">
    	$(document).ready(function() {
    		$('#myCarousel').carousel({
    			interval: 3500
    		})
    	});
    </script>

    <!-- Bootstrap Core JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>

</html>