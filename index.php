<?php
include 'config/general.php';

?><html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/long.css" />
	<?php

	foreach($stylesheets as $stylesheet)
	{
		echo "\t<link type=\"text/css\" href=\"css/$stylesheet.css\" rel=\"";
		if($stylesheet != $default_stylesheet) echo "alternate ";
		echo "stylesheet\" title=\"$stylesheet\" />\n";
	}
	?>
	<script src="js/script.js"></script>
	<title>GR3 - Scanner for WEB vulnerabilities in PHP scripts</title>
</head>
<body>

<div class="menu">
	<div style="float:left; width:100%;">
    <input type="hidden" id="subdirs" value="1" /> <!--Change to 1 for search in sub directory or 0 for not-->	
    <input type="hidden" id="css" value="phps"/>
    <input type="hidden" id="treestyle" value="2" />
	<table width="100%">
	<tr><td width="75%" nowrap>
	<table class="menutable" width="50%" style="float:left;">
		<tr>
			<td align="left" nowrap><b>Folder or file 's path:</b></td>
			<td colspan="3" nowrap><input type="text" size=80 id="location" value="<?php echo BASEDIR; ?>" title="Enter your file 's path here and click Scan for start check">
			</td>
			
		</tr>
		<tr>
			<td align="left" nowrap><b>Vulnerability type:</b></td>
			<td colspan="2" nowrap>
				<select id="vector" style="width:100%" title="select vulnerability type to scan">
					<?php 
					
						$vectors = array(
							'xss' 			=> '- Cross-Site Scripting (XSS)',
							'sql_inj'		=> '- SQL Injection'
						);
						
						foreach($vectors as $vector=>$description)
						{
							echo "<option value=\"$vector\" ";
							if($vector == $default_vector) echo 'selected';
							echo ">$description</option>\n";
						}
					?>
				</select>
			</td>
			<td nowrap><input type="button" value="SCAN" style="width:100%" class="Button" onClick="scan(false);" title="start scan" /></td>
		</tr>			
		</table>
		<div id="options" style="margin-top:-10px; display:none; text-align:center;" >
			<input type="button" class="Button" style="width:50px" value="Files" onClick="openWindow(5);eval(document.getElementById('filegraph_code').innerHTML);" title="show list of scanned files" />
			<input type="button" class="Button" style="width:80px" value="User input" onClick="openWindow(4)" title="show list of user input" />
			
			<input type="button" class="Button" style="width:80px" value="Functions" onClick="openWindow(3);eval(document.getElementById('functiongraph_code').innerHTML);" title="show list of user-defined functions" />
            
            <input type="button" class="Button" style="width:80px" value="Save" onClick="" title="Save Scan Result to database" />
            <!--TODO-->
            <input type="button" class="Button" style="width:80px" value="Add Vul" onClick="" title="Add new sinks anf secu function of new vul" />
            <!--TODO-->
		</div>
	</td>
</tr>
	</table>
	</div>
	
	<div style="clear:left;"></div>
</div>
<div class="scanning" id="scanning"></div>
</div>

<div id="result"></div>

</body>
</html>