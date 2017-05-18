<?php

	function str_replace_first($from, $to, $subject)
	{
		$from = '/'.preg_quote($from, '/').'/';

		return preg_replace($from, $to, $subject, 1);
	}
	
	function cmp($s1, $s2){
		
		$string1 = str_replace_first("The ", "", $s1['Name']);
		$string1 = str_replace_first("the ", "", $string1);
		$string1 = str_replace_first("?", "", $string1);
		$string1 = str_replace_first("!", "", $string1);
		$string1 = strtolower($string1);
		//$string1 = str_replace_first('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', "", $string1);
		
		$string2 = str_replace_first("The ", "", $s2['Name']);
		$string2 = str_replace_first("the ", "", $string2);
		$string2 = str_replace_first("?", "", $string2);
		$string2 = str_replace_first("!", "", $string2);
		$string2 = strtolower($string2);

		//$string2 = str_replace_first('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', "", $string2);

		return strcmp($string1, $string2);
	}
?>