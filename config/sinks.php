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
	// SQL executing functions => (parameters to scan, securing functions)
	$NAME_DATABASE = 'SQL Injection';
	$F_DATABASE = array(
	// Abstraction Layers
		'dba_open'						=> array(array(1), array()),
		'dba_popen'						=> array(array(1), array()), 
		'dba_insert'					=> array(array(1,2), array()),
		'dba_fetch'						=> array(array(1), array()), 
		'dba_delete'					=> array(array(1), array()), 
		'dbx_query'						=> array(array(2), $F_SECURING_SQL), 
		'odbc_do'						=> array(array(2), $F_SECURING_SQL),
		'odbc_exec'						=> array(array(2), $F_SECURING_SQL),
		'odbc_execute'					=> array(array(2), $F_SECURING_SQL),
	// Vendor Specific	
		'db2_exec' 						=> array(array(2), $F_SECURING_SQL),
		'db2_execute'					=> array(array(2), $F_SECURING_SQL),
		'fbsql_db_query'				=> array(array(2), $F_SECURING_SQL),
		'fbsql_query'					=> array(array(1), $F_SECURING_SQL), 
		'ibase_query'					=> array(array(2), $F_SECURING_SQL), 
		'ibase_execute'					=> array(array(1), $F_SECURING_SQL), 
		'ifx_query'						=> array(array(1), $F_SECURING_SQL), 
		'ifx_do'						=> array(array(1), $F_SECURING_SQL),
		'ingres_query'					=> array(array(2), $F_SECURING_SQL),
		'ingres_execute'				=> array(array(2), $F_SECURING_SQL),
		'ingres_unbuffered_query'		=> array(array(2), $F_SECURING_SQL),
		'msql_db_query'					=> array(array(2), $F_SECURING_SQL), 
		'msql_query'					=> array(array(1), $F_SECURING_SQL),
		'msql'							=> array(array(2), $F_SECURING_SQL), 
		'mssql_query'					=> array(array(1), $F_SECURING_SQL), 
		'mssql_execute'					=> array(array(1), $F_SECURING_SQL),
		'mysql_db_query'				=> array(array(2), $F_SECURING_SQL),  
		'mysql_query'					=> array(array(1), $F_SECURING_SQL), 
		'mysql_unbuffered_query'		=> array(array(1), $F_SECURING_SQL), 
		'mysqli_stmt_execute'			=> array(array(1), $F_SECURING_SQL),
		'mysqli_query'					=> array(array(2), $F_SECURING_SQL),
		'mysqli_real_query'				=> array(array(1), $F_SECURING_SQL),
		'mysqli_master_query'			=> array(array(2), $F_SECURING_SQL),
		'oci_execute'					=> array(array(1), array()),
		'ociexecute'					=> array(array(1), array()),
		'ovrimos_exec'					=> array(array(2), $F_SECURING_SQL),
		'ovrimos_execute'				=> array(array(2), $F_SECURING_SQL),
		'ora_do'						=> array(array(2), array()), 
		'ora_exec'						=> array(array(1), array()), 
		'pg_query'						=> array(array(2), $F_SECURING_SQL),
		'pg_send_query'					=> array(array(2), $F_SECURING_SQL),
		'pg_send_query_params'			=> array(array(2), $F_SECURING_SQL),
		'pg_send_prepare'				=> array(array(3), $F_SECURING_SQL),
		'pg_prepare'					=> array(array(3), $F_SECURING_SQL),
		'sqlite_open'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_popen'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_array_query'			=> array(array(1,2), $F_SECURING_SQL),
		'arrayQuery'					=> array(array(1,2), $F_SECURING_SQL),
		'singleQuery'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_query'					=> array(array(1,2), $F_SECURING_SQL),
		'sqlite_exec'					=> array(array(1,2), $F_SECURING_SQL),
		'sqlite_single_query'			=> array(array(2), $F_SECURING_SQL),
		'sqlite_unbuffered_query'		=> array(array(1,2), $F_SECURING_SQL),
		'sybase_query'					=> array(array(1), $F_SECURING_SQL), 
		'sybase_unbuffered_query'		=> array(array(1), $F_SECURING_SQL)
	);

?>	