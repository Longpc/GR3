<?php


	###############################  INCLUDES  ################################

	include('config/general.php');			// general settings
	include('config/sources.php');			// tainted variables and functions
	include('config/tokens.php');			// tokens for lexical analysis pharse
	include('config/securing.php');			// securing functions define
	include('config/sinks.php');			// sensitive sinks
	
	include('lib/constructer.php'); 		// classes	and construc
	include('lib/filer.php');				// read files from dirs and subdirs
	include('lib/tokenizer.php');			// prepare and fix token list
	include('lib/analyzer.php');			// string analyzers
	include('lib/scanner.php');				// provides class for scan
	include('lib/printer.php');				// print output scan result
		
	###############################  MAIN  ####################################
	
	$start = microtime(TRUE);
	
	$output = array();
	$info = array();
	$scanned_files = array();
	
	if(!empty($_POST['loc']))
	{		
		$location = realpath($_POST['loc']);
		
		if(is_dir($location))
		{
			$scan_subdirs = isset($_POST['subdirs']) ? $_POST['subdirs'] : false;
			$files = read_recursiv($location, $scan_subdirs);
			
			if(count($files) > WARNFILES && !isset($_POST['ignore_warning']))
				die('warning number of file:'.count($files));
		}	
		else if(is_file($location) && in_array(substr($location, strrpos($location, '.')), $FILETYPES))
		{
			$files[0] = $location;
		}
		else
		{
			$files = array();
		}
		
	
		// SCAN
			$user_functions = array();
			$user_functions_offset = array();
			$user_input = array();
			
			$file_sinks_count = array();
			$count_xss=$count_sqli=0;
			$scan_functions = array();
			
			switch($_POST['vector']) 
			{
				case 'xss':		$scan_functions = $F_XSS;
								break;
				case 'sql_inj': $scan_functions = $F_DATABASE;		break;
				//No more case for SQL Inj..vv.vv.vv
				}
			}	
			$source_functions = Sources::$F_OTHER_INPUT;	
			$overall_time = 0;
			$timeleft = 0;
			$file_amount = count($files);		
			for($fit=0; $fit<$file_amount; $fit++)
			{
				// for scanning display
				$thisfile_start = microtime(TRUE);
				$file_scanning = $files[$fit];
				
				echo ($fit) . '|' . $file_amount . '|' . $file_scanning . '|' . $timeleft . '|' . "\n";
				@ob_flush();
				flush();
	
				// scan
				$scan = new Scanner($file_scanning, $scan_functions, $source_functions);
				$scan->parse();
				$scanned_files[$file_scanning] = $scan->inc_map;
				
				$overall_time += microtime(TRUE) - $thisfile_start;
				// timeleft = average_time_per_file * file_amount_left
				$timeleft = round(($overall_time/($fit+1)) * ($file_amount - $fit+1),2);
			}
			#die("done");
			echo "STATS_DONE.\n";
			@ob_flush();
			flush();
			$elapsed = microtime(TRUE) - $start;

	################################  RESULT  #################################	
?>	
<!--For View Source COde-->
<div id="window1" name="window" style="width:600px; height:250px;">
	<div class="windowtitlebar">
		<div id="windowtitle1" onClick="toTop(1)" onmousedown="dragstart(1)" class="windowtitle"></div>
		<input id="maxbutton1" type="button" class="maxbutton" value="Z" onClick="maxWindow(1, 800)" title="maximize" />
		<input type="button" class="closebutton" value="x" onClick="closeWindow(1)" title="close" />
	</div>
	<div style="position:relative;width:100%;">
	<div id="scrolldiv">
		<!--<div id="scrollwindow"></div>-->
		<div id="scrollcode"></div>
	</div>
	<div id="windowcontent1" class="windowcontent" onscroll="scroller()"></div>
	<div style="clear:left;"></div>
	</div>
	
	<div id="return" class="return" onClick="returnLastCode()">&crarr; return</div>
</div>
<!--For view help content-->
<div id="window2" name="window" style="width:600px; height:600px;">
	<div class="windowtitlebar">
		<div id="windowtitle2" onClick="toTop(2)" onmousedown="dragstart(2)" class="windowtitle"></div>
		<input type="button" class="closebutton" value="x" onClick="closeWindow(2)" title="close" />
	</div>
	<div id="windowcontent2" class="windowcontent"></div>
</div>
<!--For view user functions-->
<div id="window3" name="window" style="width:300px; height:400px;">
	<div class="funclisttitlebar">
		<div id="windowtitle3" onClick="toTop(3)" onmousedown="dragstart(3)" class="funclisttitle">
		user defined functions and calls
		</div>
		<input type="button" class="closebutton" value="x" onClick="closeWindow(3)" title="close" />
	</div>
	<div id="windowcontent3" class="funclistcontent">
		<?php
			createFunctionList($user_functions_offset);		
		?>
	</div>	
	
</div>
<!--For view all input var 's name-->
<div id="window4" name="window" style="width:300px; height:400px;">
	<div class="funclisttitlebar">
		<div id="windowtitle4" onClick="toTop(4)" onmousedown="dragstart(4)" class="funclisttitle">
		user input
		</div>
		<input type="button" class="closebutton" value="x" onClick="closeWindow(4)" title="close" />
	</div>
	<div id="windowcontent4" class="funclistcontent">
		<?php
			createUserinputList($user_input);		
		?>
	</div>
</div>
<!--For view scanned files as tree-->
<div id="window5" name="window" style="width:300px; height:400px;">
	<div class="funclisttitlebar">
		<div id="windowtitle4" onClick="toTop(5)" onmousedown="dragstart(5)" class="funclisttitle">
		scanned files and includes
		</div>
		<input type="button" class="closebutton" value="x" onClick="closeWindow(5)" title="close" />
	</div>
	<div id="windowcontent5" class="funclistcontent">
		<?php
			createFileList($scanned_files, $file_sinks_count);		
		?>
	</div>
</div>		
<!--For view scan result dialog-->
<div id="stats" class="stats">
	<table  width="100%">
		<tr>
			<th align="left" style="font-size:22px;padding-left:10px;color:#FFF">Result</th>
			<th align="left"><input class="button" type="button" value="x" onClick="document.getElementById('stats').style.display='none';" title="close" /></th>
		</tr>
	</table>	
	<hr />	
	<table class="textcolor" width="100%">	
<?php 
	// output stats
		$count_all=$count_xss+$count_sqli;
		if($count_all > 0)
		{
			echo '<tr><td nowrap title="show only vulnerabilities of this category">',"Cross - Site Scripting: ",'</td><td nowrap>',$count_xss,'</td></tr>';
		} else
		{
			echo '<tr><td colspan="2" width="160">No vulnerabilities found.</td></tr>';
		}

	echo '</table><hr /><table class="textcolor" width="100%">',
		'<tr><td>Scanned files:</td><td nowrap colspan="2">',count($files),'</td></tr>';

		echo '<tr><td>User-defined functions:</td><td nowrap>'.(count($user_functions_offset)-(count($user_functions_offset)>0?1:0)).'</td></tr>',
		'</table><hr />';	
		?>
		<table class="textcolor" width="100%">
		<tr><td nowrap width="160">Scan time:</td><td nowrap><span id="scantime"><?php printf("%.03f seconds", $elapsed); ?></span></td></tr>
	</table>		

</div>

<?php 
	// scan result
	@printoutput($output, $_POST['treestyle']); 
?>