<?php
	
	// securing functions for  vulnerability
	$F_SECURING_STRING = array(
		'intval',
		'floatval',
		'doubleval',
		'filter_input',
		'urlencode',
		'rawurlencode',
		'round',
		'floor',
		'strlen',
		'hexdec',
		'strrpos',
		'strpos',
		'md5',
		'sha1',
		'crypt',
		'crc32',
		'hash',
		'base64_encode',
		'ord',
		'sizeof',
		'count',
		'bin2hex',
		'levenshtein',
		'abs',
		'bindec',
		'decbin',
		'hexdec',
		'rand',
		'max',
		'min'
	);
	
	// functions that insecures the string again 
	$F_INSECURING_STRING = array(
		'rawurldecode',
		'urldecode',
		'base64_decode',
		'html_entity_decode',
		'str_rot13',
		'chr'
	);

	// securing functions for XSS
	$F_SECURING_XSS = array(
		'htmlentities',
		'htmlspecialchars'
	);	
	
	
	// all specific securings
	$F_SECURES_ALL = array_merge(
		$F_SECURING_XSS, 
		$F_SECURING_SQL
	);	
	
	// securing functions that work only when embedded in quotes
	$F_QUOTE_ANALYSIS = $F_SECURING_SQL;
		
?>	