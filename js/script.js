
/* SCAN */

function scanAnimation(height, idprefix)
{
	var div = document.getElementById(idprefix+'ned');
	div.style.height = height+"px";
}
function hide(tag)
{
	if(document.getElementById(tag).style.display != "none")
	{
		document.getElementById(tag).style.display="none";
		document.getElementById("pic"+tag).className='plusico';
	}
	else
	{
		document.getElementById(tag).style.display="block";
		document.getElementById("pic"+tag).className='minusico';
	}
}

function handleResponse(idprefix) {
	if (client.readyState != 4 && client.readyState != 3)
		return;
	if (client.readyState == 3 && client.status != 200)
		return;
	if (client.readyState == 4 && client.status != 200) {
		return;
	}

	if (client.responseText === null)
		return;

	while (prevDataLength != client.responseText.length) {
		if (client.readyState == 4  && prevDataLength == client.responseText.length)
			break;
			
		prevDataLength = client.responseText.length;

		var lines = client.responseText.split('\n');
		var newline = lines[lines.length-2];

		if(newline == 'STATS_DONE.') {				
			console.log("done");
			stats_done = true;
			return;	
		} else if(newline != undefined)
		{
			data = newline.split('|');
			if(data[0] != undefined && data[1] != undefined && data[2] != undefined && data[3] != undefined)
			{
				document.getElementById(idprefix+"file").innerHTML = data[2];
				procent = Math.round((data[0]/data[1])*100);
				
				scanAnimation((procent * 75)/100, idprefix)
				
				document.getElementById(idprefix+"progress").innerHTML = '<span style="font-size:20px">' + procent + '%</span><br />(' + data[0] + '/' + data[1] + ')';
				document.getElementById(idprefix+"timeleft").innerHTML = 'appr. timeleft: ' + ( (Math.round(data[3]/60) > 1) ? (Math.round(data[3]/60) + ' min') : (Math.round(data[3]) + ' sec') );
			} else
			{
				stats_done = true;
			}
		}
	}	

	if (client.readyState == 4 && prevDataLength == client.responseText.length) {
		return;
	}	

}


function scan(ignore_warning)
{
	var location = encodeURIComponent(document.getElementById("location").value);
	var subdirs = document.getElementById("subdirs").value;
	//var	verbosity = document.getElementById("verbosity").value;
	var vector = document.getElementById("vector").value;
	var treestyle = document.getElementById("treestyle").value;
	var stylesheet = document.getElementById("css").value;
	
	var params = "loc="+location+"&subdirs="+subdirs+"&vector="+vector+"&treestyle="+treestyle+"&stylesheet="+stylesheet;

	if(ignore_warning)
		params+="&ignore_warning=1";
	prevDataLength = 0;
	nextLine = '';
	
	var a = true;
	stats_done = false;
	client = new XMLHttpRequest();
	client.onreadystatechange = function () 
	{ 
		if(this.readyState == 3 && !stats_done)
			handleResponse('scan');
		else if(this.readyState == 4 && this.status == 200 && a) 
		{
			if(!this.responseText.match(/^\s*warning:/))
			{
				document.getElementById("scanning").style.display="none";
				document.getElementById("options").style.display="";
				
				nostats = this.responseText.split("STATS_DONE.\n");
				if(nostats[1])
					result = nostats[1];
				else
					result = nostats[0];
				
				document.getElementById("result").innerHTML=(result);
				//generateDiagram();
			}
			else
			{
				var amount = this.responseText.split(':')[1];
				var warning = "<div class=\"warning\">";
				warning+="<h2>warning</h2>";
				warning+="<p>You are about to scan " + amount + " files. ";
				warning+="Depending on the amount of codelines and includes this may take a while.";
				warning+="<p>Do you want to continue anyway?</p>";	
				warning+="<input type=\"button\" class=\"Button\" value=\"continue\" onClick=\"scan(true);\"/>&nbsp;";
				warning+="<input type=\"button\" class=\"Button\" value=\"cancel\" onClick=\"document.getElementById('scanning').style.display='none';\"/>";
				warning+="</div>";
				document.getElementById("scanning").style.backgroundImage="none";
				document.getElementById("scanning").innerHTML=warning;
			}
			a=false;
		} 
		else if (this.readyState == 4 && this.status != 200) 
		{
			var warning = "<div class=\"warning\">";
			warning+="<h2>Network error (HTTP "+this.status+")</h2>";
			if(this.status == 0)
				warning+="<p>Could not access <i>main.php</i>. Make sure your webserver is running.</p>";
			else if(this.status == 404)
				warning+="<p>Could not access <i>main.php</i>. Make sure you copied all files.</p>";
			else if(this.status == 500)	
				warning+="<p>Scan aborted. Try to scan only one entry file at once or increase the <i>set_time_limit()</i> in </i>config/general.php</i>.</p>";
			warning+="</div>";
			document.getElementById("scanning").style.backgroundImage="none";
			document.getElementById("scanning").innerHTML=warning;
		}
	}
	client.open("POST", "main.php", true);
	client.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	client.setRequestHeader("Content-length", params.length);
	client.setRequestHeader("Connection", "close");
	client.send(params);
}
/* MANAGE WINDOWS */

function closeFuncCode()
{
	document.getElementById("funccode").style.display = "none";
}

function closeWindow(id)
{
	document.getElementById("window"+id).style.display="none";
}

var lastheight = "200px";
var lastwidth = "400px";
function maxWindow(id, newwidth)
{
	lastheight = document.getElementById("window"+id).style.height;
	lastwidth = document.getElementById("window"+id).style.width;
	document.getElementById("window"+id).style.height = 400;
	document.getElementById("window"+id).style.width = newwidth+"px";
	if(id==1)
	{
		document.getElementById("windowcontent1").style.width = newwidth-84 + "px";
		scroller();
	}	
}

function minWindow(id, oldwidth)
{
	document.getElementById("window"+id).style.height = lastheight;
	document.getElementById("window"+id).style.width = lastwidth;
}

function toTop(wid)
{
	var windows = document.getElementsByName("window");
	for(var i=0; i<windows.length; i++)
	{
		if(windows[i].id == "window"+wid)
			windows[i].style.zIndex = 3;
		else
			windows[i].style.zIndex = 1;
	}
}



function showlist(type)
{
	document.getElementById(type+'canvas').style.display="none";
	document.getElementById(type+'listdiv').style.display="block";
	document.getElementById(type+'listbutton').style.background="white";
	document.getElementById(type+'listbutton').style.color="black";
	document.getElementById(type+'graphbutton').style.background="#454545";
	document.getElementById(type+'graphbutton').style.color="white";
}

function scroller() 
{
	var content = document.getElementById('windowcontent1');
	var win = document.getElementById('scrollwindow');
	var code1 = document.getElementById('scrollcode');
	try {
		var code2 = document.getElementById('codetable');
		if(code2.clientHeight<code1.clientHeight)
			var code = code2;
		else
			var code = code1;
	} catch(e)
	{
		code = code1;
	}
	
	win.style.height=(0.1 * content.clientHeight) + 'px';
	code1.scrollTop=((content.scrollTop / (content.scrollHeight-content.clientHeight)) * ((code.scrollHeight-code.clientHeight)));
	win.style.top=((content.scrollTop / (content.scrollHeight-content.clientHeight)) * (code.clientHeight-win.clientHeight)) + 'px';
}

/* LOAD WINDOWS */

function openWindow(id)
{
	var style = document.getElementById("window"+id).style;

	if(style.display == "" || style.display == "none") {
		style.display = "block";
		style.zIndex = 3;
	}	
	else {
		style.display = "none";
	}	
}
	
function getFuncCode(hoveritem, file, start, end)
{
	var codediv = document.getElementById("funccode");
	codediv.style.display="block"; 
	codediv.style.zIndex = 3;
	
	if(file.length > 50)
		title = '...'+file.substr(file.length-50,50);
	else
		title = file;
	document.getElementById("funccodetitle").innerHTML=title;
	
	var tmp = hoveritem.offsetParent;
	codediv.style.top = tmp.offsetParent.offsetTop; 
	codediv.style.left = hoveritem.offsetLeft;
	
	var a = true;
	var client = new XMLHttpRequest();
	client.onreadystatechange = function () 
	{ 
		if(this.readyState == 4 && this.status == 200 && a) 
		{
			document.getElementById("funccodecontent").innerHTML=(this.responseText);
			a=false;
		} 
		else if (this.readyState == 4 && this.status != 200) 
		{
			alert("Network error ("+this.status+").");
		}
	}
	client.open("GET", "windows/function.php?file="+file+"&start="+start+"&end="+end);
	client.send();
}

function openHelp(hoveritem, type, thefunction, get, post, cookie, files, server)
{
	var title = 'Help - ';
	if(type.length > 50)
		title+= type.substr(0,80)+'...';
	else
		title+=type;
	
	var mywindow = document.getElementById("window2");	
	mywindow.style.display="block";
	
	if(hoveritem != 3 && hoveritem != 4)
		var tmp = hoveritem.offsetParent;
	else	
		var tmp = document.getElementById("windowtitle"+hoveritem);
		
	mywindow.style.top = tmp.offsetParent.offsetTop - 100; 
	mywindow.style.right = 200; 
	
	document.getElementById("windowtitle2").innerHTML=title;
	
	var a = true;
	var client = new XMLHttpRequest();
	client.onreadystatechange = function () 
	{ 
		if(this.readyState == 4 && this.status == 200 && a) 
		{
			document.getElementById("windowcontent2").innerHTML=(this.responseText);
					
			document.getElementById("windowcontent2").scrollIntoView();
		
			document.body.scrollTop = tmp.offsetParent.offsetTop - 200;
			
			a=false;
		} 
		else if (this.readyState == 4 && this.status != 200) 
		{
			alert("Network error ("+this.status+").");
		}
	}
	client.open("GET", 
		"windows/help.php?type="+type+"&function="+thefunction+"&get="+get+"&post="+post+"&cookie="+cookie+"&files="+files+"&server="+server);
	client.send();
}

function openHotpatch(hoveritem, file, get, post, cookie, files, server)
{
	var title = 'HotPatcher - ';
	if(file.length > 50)
		title+= '...'+file.substr(file.length-50,50);
	else
		title+= file;
		
	var mywindow = document.getElementById("window2");	
	mywindow.style.display="block";
	
	var tmp = hoveritem.offsetParent;
	
	mywindow.style.top = tmp.offsetParent.offsetTop - 100; 
	mywindow.style.right = 200; 
	
	document.getElementById("windowtitle2").innerHTML=title;
	
	var a = true;
	var client = new XMLHttpRequest();
	client.onreadystatechange = function () 
	{ 
		if(this.readyState == 4 && this.status == 200 && a) 
		{
			document.getElementById("windowcontent2").innerHTML=(this.responseText);
					
			document.getElementById("windowcontent2").scrollIntoView();
		
			document.body.scrollTop = tmp.offsetParent.offsetTop - 200;
			
			a=false;
		} 
		else if (this.readyState == 4 && this.status != 200) 
		{
			alert("Network error ("+this.status+").");
		}
	}
	client.open("GET", 
		"windows/hotpatch.php?file="+file+"&get="+get+"&post="+post+"&cookie="+cookie+"&files="+files+"&server="+server);
	client.send();
}

function openCodeViewer(hoveritem, file, lines)
{
	var linenrs = lines.split(",");
	var title = 'CodeViewer - ';
	if(file.length > 50)
		title+= '...'+file.substr(file.length-50,50);
	else
		title+= file;
		
	var mywindow = document.getElementById("window1");	
	mywindow.style.display="block";
	
	if(hoveritem != 3 && hoveritem != 4)
		var tmp = hoveritem.offsetParent;
	else	
		var tmp = document.getElementById("windowtitle"+hoveritem);
		
	if(tmp.offsetParent != null)	
		mywindow.style.top = tmp.offsetParent.offsetTop - 100; 
	mywindow.style.right = 200; 
	
	document.getElementById("windowtitle1").innerHTML=title;
	
	var a = true;
	var client = new XMLHttpRequest();
	client.onreadystatechange = function () 
	{ 
		if(this.readyState == 4 && this.status == 200 && a) 
		{
			document.getElementById("windowcontent1").innerHTML=(this.responseText);
					
			if(document.getElementById(linenrs[0]) != null)	
				document.getElementById(linenrs[0]).scrollIntoView();
		
			if(tmp.offsetParent != null)
				document.body.scrollTop = tmp.offsetParent.offsetTop - 200;
			else
				document.body.scrollTop = document.body.scrollTop - 100;
			
			document.getElementById("scrollcode").innerHTML=document.getElementById("codeonly").innerHTML;
			a=false;
		} 
		else if (this.readyState == 4 && this.status != 200) 
		{
			alert("Network error ("+this.status+").");
		}
	}
	client.open("GET", "windows/code.php?file="+file+"&lines="+lines);
	client.send();
}





var myData = Array();
