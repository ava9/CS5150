<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<!-- Bootstrap Core CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="/cs5150/html/css/style.css" rel="stylesheet">

<script src="/cs5150/js/jquery.js"></script>

<!-- TODO: I put this in the standard header by accident, need to find out where this applies. -->
<script src="/cs5150/js/addressautocomplete.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/cs5150/js/jquery.tokeninput.js"></script>

<!-- URL Links for every website page -->
<?php
	$root_dir = '/cs5150/html/';

	define('INDEX_URL', $root_dir);
	define('EXISTING_PORCHFEST_URL', $root_dir . 'existingporchfest');
	define('BROWSE_PORCHFEST_URL', $root_dir . 'browse');
	define('MY_PORCHFEST_URL', $root_dir . 'myporchfests');
	define('PROFILE_URL', $root_dir . 'profile');

	/**
	 * Given a desired url to link to and text to display on the website,
	 * create a <a> HTML tag. Used to link to other pages in HTML code.
	 */
	function create_hyperlink($url, $text) {
		echo sprintf('<a href="%s">%s</a>', $url, $text);
	}

?>