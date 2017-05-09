<!-- URL Links for every website page -->
<?php
	
	require_once __DIR__.'/../../config.php';
	$root_dir = SRCHREF_ROOT;

	// CSS links
	define('CSS_LINK', $root_dir . "css/style.css");
	define('CSS_RESPONSIVE_TABLES_LINK', $root_dir . "css/responsive-tables.css");
	define('CSS_TOKEN_INPUT', $root_dir . "php/modules/token-input-facebook.css");

	// JS links
	define('JQEURY_LINK', $root_dir . "js/jquery.js");
	define('JS_ADDR_LINK', $root_dir . "js/addressautocomplete.js");
	define('JS_TOKENINPUT_LINK', $root_dir . "js/jquery.tokeninput.js");
	define('JS_RESPONSIVE_TABLES_LINK', $root_dir . "js/responsive-tables.js");

	// URL Links
	define('INDEX_URL', $root_dir);
	define('NEW_PORCHFEST_URL', $root_dir . 'newporchfest');
	define('EXISTING_PORCHFEST_URL', $root_dir . 'existingporchfest');
	define('BROWSE_PORCHFEST_URL', $root_dir . 'browse');
	define('MY_PORCHFEST_URL', $root_dir . 'myporchfests');
	define('PROFILE_URL', $root_dir . 'profile');
	define('DASHBOARD_URL', $root_dir . 'dashboard');
	define('EDIT_PORCHFEST_URL', $root_dir . 'edit');

	// PHP files
	define('PHP_EXPORT', $root_dir . 'php/export.php');
	define('PHP_AJAX', $root_dir . 'php/ajax.php');
	define('PHP_BAND_LISTING', $root_dir . 'band-listing.php');

	// IMG/GIF files
	define('GIF_LOADING', $root_dir . 'img/ajax-loader.gif');
	define('IMG_LANDING', $root_dir . 'img/landing.jpg');

	/**
	 * Given a desired url to link to and text to display on the website,
	 * create a <a> HTML tag. Used to link to other pages in HTML code.
	 */
	function create_hyperlink($url, $text) {
		echo sprintf('<a href="%s">%s</a>', $url, $text);
	}

	function tooltip($text, $dir='right') {
		echo 
			'<span class="tool-tip" data-toggle="tooltip" data-placement="' . $dir . '" title="' . $text . '">
			    <i class="glyphicon glyphicon-info-sign"></i>
			</span>';
	}
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<!-- Bootstrap Core CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="<?php echo CSS_LINK; ?>" rel="stylesheet">

<script src="<?php echo JQEURY_LINK; ?>"></script>

<script src="<?php echo JS_ADDR_LINK; ?>"></script>
<!-- Bootstrap Core JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript" src="<?php echo JS_TOKENINPUT_LINK; ?>"></script>

<script>
	$(document).ready(function(){
		$("span.tool-tip").tooltip({'container':'body'});
	});
</script>
