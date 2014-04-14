<?php

include '../config/general.php';
include '../config/securing.php';
include '../config/sinks.php';
include '../config/tokens.php';
include '../config/sources.php';
include '../config/help.php';
include '../lib/printer.php';

$function = htmlentities($_GET['function'], ENT_QUOTES, 'utf-8');
$type = htmlentities($_GET['type'], ENT_QUOTES, 'utf-8');
$type = explode(" (", $type);
$type = $type[0];
?>

<div style="padding-left:30px;padding-right:30px">
<h2><?php echo $type; ?></h2>
<h3>vulnerability concept:</h3>

<table class="textcolor">
<tr>
	<th class="helptitle">source</th>
	<th></th>
	<th class="helptitle">function</th>
	<th></th>
	<th class="helptitle">vulnerability</th>
</tr>
<tr>
<td align="left" class="helpbox">
<ul style="margin-left:-25px">
<?php
if($_GET['get'] || (empty($_GET['get']) && empty($_GET['post']) && empty($_GET['cookie']) && empty($_GET['files']) && empty($_GET['server']))) 	
	echo '<li class="userinput"><a href="'.PHPDOC.'reserved.variables.get" target="_blank">$_GET</a></li>';
if($_GET['post'])	
	echo '<li class="userinput"><a href="'.PHPDOC.'reserved.variables.post" target="_blank">$_POST</a></li>';;
if($_GET['cookie'])	
	echo '<li class="userinput"><a href="'.PHPDOC.'reserved.variables.cookie" target="_blank">$_COOKIE</a></li>';
if($_GET['files']) 	
	echo '<li class="userinput"><a href="'.PHPDOC.'reserved.variables.files" target="_blank">$_FILES</a></li>';
if($_GET['server'])	
	echo '<li class="userinput"><a href="'.PHPDOC.'reserved.variables.server" target="_blank">$_SERVER</a></li>';
?>
</ul>
</td>
<td align="center" valign="center"><h1>+</h1></td>
<td align="center" class="helpbox">
	<?php echo '<a class="link" href="'.PHPDOC.$function.'" target="_blank">'.$function.'()</a>'; ?>
</td>
<td align="center" valign="center"><h1>=</h1></td>
<td align="center" class="helpbox">
<?php echo $type; ?>
</td>
</tr>
</table>

<h3>Vulnerability description:</h3>
<p><?php echo 'An attacker might execute arbitrary HTML/JavaScript Code in the clients browser context with this security vulnerability. User tainted data is embedded into the HTML output by the application and rendered by the users browser, thus allowing an attacker to embed and render malicious code. Preparing a malicious link will lead to an execution of this malicious code in another users browser context when clicking the link. This can lead to local website defacement, phishing or cookie stealing and session hijacking.'; ?></p>
<p><?php if(!empty($HELP['link'])) echo "More information about $type can be found <a href=\"{https://www.owasp.org/index.php/XSS}\">here</a>."; ?></p>

<h3>Vulnerable example code:</h3>
<pre><?php echo highlightline(token_get_all('<?php print("Hello " . $_GET["name"]); ?>'), '', 1); ?></pre>

<h3>Proof of concept:</h3>
<p><?php echo htmlentities('/index.php?name=<script>alert(1)</script>'); ?></p>

<h3>Patch:</h3>
<p><?php echo htmlentities('Encode all user tainted data with PHP buildin functions before embedding the data into the output. Make sure to set the parameter ENT_QUOTES to avoid an eventhandler injections to existing HTML attributes and specify the correct charset.'); ?></p>
<pre><?php echo highlightline(token_get_all('<?php print("Hello " . htmlentities($_GET["name"], ENT_QUOTES, "utf-8"); ?>'), '', 1); ?></pre>

<h3>Related securing functions:</h3>
<ul>
<a class="link" href="http://php.net/htmlentities" title="open php documentation" target="_blank">htmlentities</a><br />
<a class="link" href="http://php.net/htmlspecialchars" title="open php documentation" target="_blank">htmlspecialchars</a><br />
</ul>
</div>