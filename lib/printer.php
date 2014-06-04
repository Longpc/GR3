<?php
	
	// add parsing error to output
	function addError($message, $tokens, $line_nr, $filename)
	{
		$GLOBALS['info'][] = '<font color="red">Parsing error occured. </font>';
	}
	
	// tokens to string for comments
	function tokenstostring($tokens)
	{
		$output = '';
		for($i=0;$i<count($tokens);$i++)
		{
			$token = $tokens[$i];
			if (is_string($token))
			{	
				if($token === ',' || $token === ';')
					$output .= "$token ";
				else if(in_array($token, Tokens::$S_SPACE_WRAP) || in_array($token, Tokens::$S_ARITHMETIC))
					$output .= " $token ";
				else	
					$output .= $token;
			}	
			else if(in_array($token[0], Tokens::$T_SPACE_WRAP) || in_array($token[0], Tokens::$T_OPERATOR) || in_array($token[0], Tokens::$T_ASSIGNMENT))
				$output .= " {$token[1]} ";
			else
				$output .= $token[1];
		}
		return $output;
	}
	
	// prepare output to style with CSS
	function highlightline($tokens=array(), $comment='', $line_nr, $title=false, $udftitle=false, $tainted_vars=array())
	{
		$reference = true;
		$output = "<span class=\"linenr\">$line_nr:</span>&nbsp;";
		if($title)
		{
			$output.='<a class="link" href="'.PHPDOC.$title.'" title="open php documentation" target=_blank>';
			$output.="$title</a>&nbsp;";
		} 
		else if($udftitle)
		{
			$output.='<a class="link" style="text-decoration:none;" href="#'.$udftitle.'_declare" title="jump to declaration">&uArr;</a>&nbsp;';
		}
		
		$var_count = 0;
		
		for($i=0;$i<count($tokens);$i++)
		{
			$token = $tokens[$i];
			if (is_string($token))
			{		
				if($token === ',' || $token === ';')
					$output .= "<span class=\"phps-code\">$token&nbsp;</span>";
				else if(in_array($token, Tokens::$S_SPACE_WRAP) || in_array($token, Tokens::$S_ARITHMETIC))
					$output .= '<span class="phps-code">&nbsp;'.$token.'&nbsp;</span>';
				else
					$output .= '<span class="phps-code">'.htmlentities($token, ENT_QUOTES, 'utf-8').'</span>';
					
			} 
			else if (is_array($token) 
			&& $token[0] !== T_OPEN_TAG
			&& $token[0] !== T_CLOSE_TAG) 
			{
				
				if(in_array($token[0], Tokens::$T_SPACE_WRAP) || in_array($token[0], Tokens::$T_OPERATOR) || in_array($token[0], Tokens::$T_ASSIGNMENT))
				{
					$output.= '&nbsp;<span class="phps-'.str_replace('_', '-', strtolower(token_name($token[0])))."\">{$token[1]}</span>&nbsp;";
				}	
				else
				{
					if($token[0] === T_FUNCTION)
					{
						$reference = false;
						$funcname = $tokens[$i+1][0] === T_STRING ? $tokens[$i+1][1] : $tokens[$i+2][1];
						$output .= '<A NAME="'.$funcname.'_declare" class="jumplink"></A>';
						$output .= '<a class="link" style="text-decoration:none;" href="#'.$funcname.'_call" title="jump to call">&dArr;</a>&nbsp;';
					}	
					
					$text = htmlentities($token[1], ENT_QUOTES, 'utf-8');
					$text = str_replace(array(' ', "\n"), '&nbsp;', $text);

					if($token[0] === T_FUNCTION)
						$text.='&nbsp;';
						
					if($token[0] === T_STRING && $reference 
					&& isset($GLOBALS['user_functions_offset'][strtolower($text)]))
					{				
						$text = @'<span onmouseover="getFuncCode(this,\''.addslashes($GLOBALS['user_functions_offset'][strtolower($text)][0]).'\',\''.$GLOBALS['user_functions_offset'][strtolower($text)][1].'\',\''.$GLOBALS['user_functions_offset'][strtolower($text)][2].'\')" style="text-decoration:underline" class="phps-'.str_replace('_', '-', strtolower(token_name($token[0])))."\">$text</span>\n";
					}	
					else 
					{
						$span = '<span ';
					
						if($token[0] === T_VARIABLE)
						{
							$var_count++;
							$cssname = str_replace('$', '', $token[1]);
							$span.= 'style="cursor:pointer;" name="phps-var-'.$cssname.'" onClick="markVariable(\''.$cssname.'\')" ';
							$span.= 'onmouseout="markVariable(\''.$cssname.'\')" ';
						}	
						
						if($token[0] === T_VARIABLE && @in_array($var_count, $tainted_vars))
							$span.= "class=\"phps-tainted-var\">$text</span>";	
						else
							$span.= 'class="phps-'.str_replace('_', '-', strtolower(token_name($token[0])))."\">$text</span>";
							
						$text = $span;	
						
						// rebuild array keys
						if(isset($token[3]))
						{
							foreach($token[3] as $key)
							{
								if($key != '*')
								{
									$text .= '<span class="phps-code">[</span>';
									if(!is_array($key))
									{
										if(is_numeric($key))
											$text .= '<span class="phps-t-lnumber">' . $key . '</span>';
										else
											$text .= '<span class="phps-t-constant-encapsed-string">\'' . htmlentities($key, ENT_QUOTES, 'utf-8') . '\'</span>';
									} else
									{
										foreach($key as $token)
										{
											if(is_array($token))
											{
												$text .= '<span ';
												
												if($token[0] === T_VARIABLE)
												{
													$cssname = str_replace('$', '', $token[1]);
													$text.= 'style="cursor:pointer;" name="phps-var-'.$cssname.'" onClick="markVariable(\''.$cssname.'\')" ';
													$text.= 'onmouseout="markVariable(\''.$cssname.'\')" ';
												}	
												
												$text .= 'class="phps-'.str_replace('_', '-', strtolower(token_name($token[0]))).'">'.htmlentities($token[1], ENT_QUOTES, 'utf-8').'</span>';
											}	
											else
												$text .= "<span class=\"phps-code\">{$token}</span>";
										}
									}
									$text .= '<span class="phps-code">]</span>';
								}
							}
						}
					}
					$output .= $text;
					if(is_array($token) && (in_array($token[0], Tokens::$T_INCLUDES) || in_array($token[0], Tokens::$T_XSS) || $token[0] === 'T_EVAL'))
						$output .= '&nbsp;';
				}		
			}
		}
		
		if(!empty($comment))
			$output .= '&nbsp;<span class="phps-t-comment">// '.htmlentities($comment, ENT_QUOTES, 'utf-8').'</span>';

		return $output;
	}
	
	// detect vulnerability type given by the PVF name
	// note: same names are used in help.php!
	function getVulnNodeTitle($func_name)
	{
		if(isset($GLOBALS['F_XSS'][$func_name])) 
		{	$vulnname = $GLOBALS['NAME_XSS'];  }	 			
		else 
			$vulnname = "unknown";
		return $vulnname;	
	}
	
	// detect vulnerability 
	// note: same names are used in help.php!
	function increaseVulnCounter($func_name)
	{
		if(isset($GLOBALS['F_XSS'][$func_name])) 
		{	$GLOBALS['count_xss']++; }	
		
	}	
		
	
	// traced parameter output top-down and print it in output block
	function traverseTopDown($tree, $start=true, $lines=array()) 
	{
		if($start) echo '<ul>';
	
		foreach ($tree->children as $child) 
		{
			$lines = traverseTopDown($child, false, $lines);
		}
		
		// do not display a line twice
		// problem: different lines in different files with equal line number
		if(!isset($lines[$tree->line]))
		{
			echo '<li';
			//TODO highlight line have function tainted
			echo '>',$tree->value,'</li>',"\n";
			// add to array to ignore next time
			$lines[$tree->line] = 1;
		}	
			
		if($start) echo '</ul>';
		
		return $lines;
	}	

	
	// check for vulns found in file
	function fileHasVulns($blocks)
	{
		foreach($blocks as $block)
		{
			if($block->vuln)
				return true;
		}
		return false;
	}	
	
	// print the scanresult
	function printoutput($output, $treestyle=1)
	{
		if(!empty($output))
		{
			$nr=0;
			reset($output);
			do
			{				
				if(key($output) != "" && !empty($output[key($output)]) && fileHasVulns($output[key($output)]))
				{		
					echo '<div class="filebox">',
					'<span class="filename">File: ',key($output),'</span><br>',
					'<div id="',key($output),'"><br>';
	
					foreach($output[key($output)] as $vulnBlock)
					{	
						if($vulnBlock->vuln)	
						{
							$nr++;
							echo '<div class="vulnblock">',
							'<div id="pic',$vulnBlock->category,$nr,'" class="minusico" name="pic',$vulnBlock->category,'" style="margin-top:5px" title="minimize"',
							' onClick="hide(\'',$vulnBlock->category,$nr,'\')"></div><div class="vulnblocktitle">',$vulnBlock->category,'</div>',
							'</div><div name="allcats"><div class="vulnblock" style="border-top:0px" name="',$vulnBlock->category,'" id="',$vulnBlock->category,$nr,'">';
							
							if($treestyle == 2)
								krsort($vulnBlock->treenodes);
							
							foreach($vulnBlock->treenodes as $tree)
							{
								// if(empty($tree->funcdepend) || $tree->foundcallee )
								{	
									echo '<div class="codebox"><table border=0>',"\n",
									'<tr><td valign="top" nowrap>',"\n",
									'<div class="fileico" title="review code" ',
									'onClick="openCodeViewer(this,\'',
									addslashes($tree->filename), '\',\'',
									implode(',', $tree->lines), '\');"></div>'."\n";
									if(isset($GLOBALS['scan_functions'][$tree->name]))
									{
										// help button
										echo '<div class="help" title="get help" onClick="openHelp(this,\'',
										$vulnBlock->category,'\',\'',$tree->name,'\',\'',
										(int)!empty($tree->get),'\',\'',
										(int)!empty($tree->post),'\',\'',
										(int)!empty($tree->cookie),'\',\'',
										(int)!empty($tree->files),'\',\'',
										(int)!empty($tree->cookie),'\')"></div>',"\n";
									}
									
									//var_dump($tree); //TODO DIE
									// $tree->title
									echo '</td><td><span class="vulntitle">',$tree->title,'</span>',
									'<div class="code" id="',key($output),$tree->lines[0],'">',"\n";

									 if($treestyle == 2)
										traverseTopDown($tree);

										echo '<ul><li>',"\n";
									//dependenciesTraverse($tree);
									echo '</li></ul>',"\n",	'</div>',"\n", '</td></tr></table></div>',"\n";
								}
							}	
							
							if(!empty($vulnBlock->alternatives))
							{
								echo '<div class="codebox"><table><tr><td><ul><li><span class="vulntitle">Vulnerability is also triggered in:</span>';
								foreach($vulnBlock->alternatives as $alternative)
								{
									echo '<ul><li>'.$alternative.'</li></ul>';
								}
								echo '</li></ul></td></table></div>';
							}
							
							echo '</div></div><div style="height:20px"></div>',"\n";
						}	
					}

					echo '</div></div><hr>',"\n";
				}	
				else if(count($output) == 1)
				{
					echo '<div style="margin-left:30px;color:#000000">Nothing vulnerable found.</div>';
				}
			}
			while(next($output));
		}
		else if(count($GLOBALS['scanned_files']) > 0)
		{
			echo '<div style="margin-left:30px;color:#000000">Nothing vulnerable found. </div>';
		}
		else
		{
			echo '<div style="margin-left:30px;color:#000000">Nothing to scan. Please check your path/file name.</div>';
		}
		
	}
	
	// build list of available functions
	function createFunctionList($user_functions_offset)
	{
		if(!empty($user_functions_offset))
		{
			
			echo '<div id="functionlistdiv"><table><tr><th align="left">declaration</th><th align="left">calls</th></tr>';
			foreach($user_functions_offset as $func_name => $info)
			{
				if($func_name !== '__main__')
				echo '<tr><td><div id="fol_',$func_name,'" class="funclistline" title="',$info[0],'" ',
				'onClick="openCodeViewer(3, \'',addslashes($info[0]),'\', \'',($info[1]+1),
				',',(!empty($info[2]) ? $info[2]+1 : 0),'\')">',$func_name,'</div></td><td>';
								
				$calls = array();
				if(isset($info[3]))
				{
					foreach($info[3] as $call)
					{
						$calls[] = '<span class="funclistline" title="'.$call[0].
						'" onClick="openCodeViewer(3, \''.addslashes($call[0]).'\', \''.$call[1].'\')">'.$call[1].'</span>';
					}
				}
				echo implode(',',array_unique($calls)).'</td></tr>';
				
				
			}
			echo '</table></div>',"\n<div id='functiongraph_code' style='display:none'>$js</div>\n";
		} else
		{
			echo "<div id='functiongraph_code' style='display:none'>document.getElementById('windowcontent3').innerHTML='No user defined functions found.'</div>\n";
		}
	}
	
	// build list of all entry points (user input)
	function createUserinputList($user_input)
	{
		//TODO OK
		if(!empty($user_input))
		{
			ksort($user_input);
			echo '<table><tr><th align="left">type[parameter]</th><th align="left">line</th></tr>';
			foreach($user_input as $input_name => $file)
			{
				$finds = array();
				foreach($file as $file_name => $lines)
				{
					foreach($lines as $line)
					{
						$finds[] = '<span class="funclistline" title="'.$file_name.'" onClick="openCodeViewer(4, \''.addslashes($file_name)."', '$line')\">$line</span>\n";
					}
				}
				echo "<tr><td nowrap>$input_name</td><td nowrap>",implode(',',array_unique($finds)),'</td></tr>';

			}
			echo '</table>';
		} else
		{
			echo 'No userinput found.';
		}
	}
	
	// build list of all scanned files
	function createFileList($files, $file_sinks)
	{
		if(!empty($files))
		{
			
	
			
			// build file list 
			echo '<div id="filelistdiv"><table>';
			foreach($files as $file => $includes)
			{				
				$file = realpath($file);

				$filename = is_dir($_POST['loc']) ? str_replace(realpath($_POST['loc']), '', $file) : str_replace(realpath(str_replace(basename($_POST['loc']),'', $_POST['loc'])),'',$file);
				$varname = preg_replace('/[^A-Za-z0-9]/', '', $filename); 

				if(empty($includes))
				{
					echo '<tr><td><div class="funclistline" title="',$file,'" ',
					'onClick="openCodeViewer(3, \'',addslashes($file),'\', \'0\')">',$filename,'</div></td></tr>',"\n";
				}	
				else
				{
					$parent = $varname;
					echo '<tr><td><div class="funclistline" title="',$file,'" ',
					'onClick="openCodeViewer(3, \'',addslashes($file),'\', \'0\')">',$filename,'</div><ul style="margin-top:0px;">',"\n";
					foreach($includes as $include)
					{
						$include = realpath($include);
	
						$includename = is_dir($_POST['loc']) ? str_replace(realpath($_POST['loc']), '', $include) : str_replace(realpath(str_replace(basename($_POST['loc']),'', $_POST['loc'])),'',$include);
						$incvarname = preg_replace('/[^A-Za-z0-9]/', '', $includename); 
	
						echo '<li><div class="funclistline" title="',$include,'" ',
						'onClick="openCodeViewer(3, \'',addslashes($include),'\', \'0\')">',$includename,'</div></li>',"\n";
					}
					echo '</ul></td></tr>',"\n";
				}	

			}
			
			echo '</table></div>',"\n<div id='filegraph_code' style='display:none'>$js</div>\n";
		}
	}
	
	function statsRow($nr, $name, $amount, $all)
	{
		echo '<tr><td nowrap color =\'#FFFFFF\' title="show only vulnerabilities">',$name,':</td><td nowrap>',$amount,'</td></tr>';
	}
	
?>	