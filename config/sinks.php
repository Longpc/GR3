<?php
	//defind for
	// cross-site scripting affected functions
	$NAME_XSS = 'Cross-Site Scripting (XSS)';
	$F_XSS = array(
		'echo'							=> array(array(0), $F_SECURING_XSS), 
		'print'							=> array(array(1), $F_SECURING_XSS),
		'print_r'						=> array(array(1), $F_SECURING_XSS),
		'exit'							=> array(array(1), $F_SECURING_XSS),
		'die'							=> array(array(1), $F_SECURING_XSS),
		'printf'						=> array(array(0), $F_SECURING_XSS),
		'vprintf'						=> array(array(0), $F_SECURING_XSS)
	);
	
?>	