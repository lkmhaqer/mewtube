<?php
require("auth.php");
$_SESSION['cpi'] = 0;
?>
<html>
<head>
<link rel=stylesheet href="/huh.css" type="text/css">
<script type='text/javascript' src='vid.js'></script>
<script type='text/javascript'>
var loaded=0;
var pco;
var cpq;
var cpi=0;
var loadgif = new Image();
loadgif.src = '/loading.gif';
var loadmsg = "<center><img src='/loading.gif' border=0><br><br><b>Loading...</b></center>";
var loadmsgtxt = "Loading...";

function url_check() {
	var q = unescape(document.location.hash.substring(1));
	var hurl = q.split("/");
	if (q && hurl[1] != 'random') {
        	if (hurl[0] == 'vid-hq') {
			loadVid(hurl[1], 1);
			loadFiles("season", getSeasonId(hurl[1]));
		} else if (hurl[0] == 'vid') {
			loadVid(hurl[1], 0);
			loadFiles("season", getSeasonId(hurl[1]));
		} else if (hurl[0] == 'movie-hq') {
			loadVid(hurl[1], 3);
			loadFiles("movies", 0);
		} else if (hurl[0] == 'movie') {
			loadVid(hurl[1], 2);
			loadFiles("movies", 0);
		} else {
			loadFiles(hurl[0], hurl[1]);
		}
	} else if (q && hurl[1] == 'random') {
		if (hurl[0] == 'vid-hq') {
                        loadVid(hurl[1], 1);
			loadFiles("main", 0);
                } else if (hurl[0] == 'vid') {
                        loadVid(hurl[1], 0);
			loadFiles("main", 0);
                } else if (hurl[0] == 'movie-hq') {
                        loadVid(hurl[1], 3);
                        loadFiles("movies", 0);
                } else if (hurl[0] == 'movie') {
                        loadVid(hurl[1], 2);
                        loadFiles("movies", 0);
                } else {
                        loadFiles(hurl[0], hurl[1]);
                }
	} else {
		loadFiles("main", 0);
	}
}

function loadNextPlaylistItem(vid, q) {
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	var exp;
	var vida = '';
        if (q == 0) {
                vida = "vid";
        } else if (q == 1) {
                vida = "vid-hq";
        } else if (q == 2) {
                vida = "movie";
        } else if (q == 3) {
                vida = "movie-hq";
        }
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
			        if (xhr.responseText == "420") {
                                        document.getElementById('vidcel').innerHTML = "Uh oh, looks like you need to log in again ->";
                                        loadFiles(null, null);
                                        exp = 1;
				} else if (exp != 1) {
					pco.sendEvent("LOAD", xhr.responseText);
					pco.sendEvent("PLAY", true);
					document.getElementById('tcel').innerHTML = "<h1><?php echo $sitename; ?> - " + xhr.responseText.substring(4) + "</h1>";
					document.getElementById('vpucel').innerHTML = "[<a href=\"javascript: loadPV('"+escape(vid)+"', "+q+");\">PopOut</a>]";
					window.location.hash = "#"+vida+"/"+vid;
					if (cpi > 0) {
						setTimeout('loadFiles(\'playlist\', 0)', 2000);
					} else {
						loadFiles("season", getSeasonId(vid));
					}
				}
			} else {
				document.getElementById('vidcel').innerHTML = "Error: "+xhr.status;
			}
		}
	}
	xhr.open('GET', '/advfile.php?a='+vida+'&id='+vid, true);
        xhr.send(null);
}

function loadVid(vid, q) {
	if (window.XMLHttpRequest) {
        	xhr2 = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
        	xhr2 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	if (cpi >= 1) {
		cpi = 0;
	}
	var exp;
	var vida = '';
	if (q == 0) {
		vida = "vid";
	} else if (q == 1) {
		vida = "vid-hq";
	} else if (q == 2) {
		vida = "movie";
	} else if (q == 3) {
		vida = "movie-hq";
	}
        xhr2.onreadystatechange = function() {
                if (xhr2.readyState == 4) {
                        if (xhr2.status == 200) {
	                       if (xhr2.responseText.search("form action") >= 10) {
                                        document.getElementById('vidcel').innerHTML = "Uh oh, looks like you need to log in again ->";
                                        loadFiles("expired", null);
					exp = 1;
                                } else if (exp != 1) {
                                	delete s1;
                                	if (q == 1 || q == 3) {
                                        	var s1 = new SWFObject('vid.swf','mewplayer','640','368','9');
                             		} else {
                                     		var s1 = new SWFObject('vid.swf','mewplayer','480','320','9');
                                	}
                                	s1.addParam('allowfullscreen','true');
                                	s1.addParam('allowscriptaccess','always');
					if (vid == "random") {
						var tmp = xhr2.responseText.split("|");
						var url = tmp[0];
						vid = tmp[1];
						window.location.hash = "#"+vida+"/"+vid;
						if (q == 1 || q == 0) {
							loadFiles("season", getSeasonId(vid));
						}
					} else {
						var url = xhr2.responseText;
					}
					var tni = 'preview-'+url.substring(4).substring(0, url.substring(4).length-4)+'.jpg';
                                	s1.addParam('flashvars','file='+url+'&id=mewplayer&streamer=lighttpd&image=/thumbs/'+tni+'&controlbar=over&backcolor=000000&frontcolor=C0C0C0');
                                	document.getElementById('tcel').innerHTML = "<h1><?php echo $sitename; ?> - " + url.substring(4) + "</h1>";
					document.getElementById('vpucel').innerHTML = "[<a href='javascript: loadPV(\""+vid+"\", "+q+");'>PopOut</a>]"+" [<a href='#"+vida+"/"+vid+"' onClick='javascript: loadVid("+vid+", "+q+");'>Permalink</a>]";
                                	s1.write('vidcel');
                                	newStats();
				}
                        } else {
                                document.getElementById('vidcel').innerHTML = "Error: " + xhr2.status;
                	}
        	} else {
			document.getElementById('vidcel').innerHTML = loadmsg;
		}
	}
	xhr2.open('GET', '/advfile.php?a='+vida+'&id='+vid, true);
        xhr2.send(null);
}

function playerReady(obj) {
        pco = document.getElementById(obj['id']);
        pco.addModelListener("STATE", "playlistSwitch");
	document.getElementById('nextButton').innerHTML = "[<a href='#next' onClick='javascript: nextItem();'>Next</a>]";
}

function setRandomRoll(val) {
	if (val == -1) {
		document.randomRollForm.randomRoll[0].checked = true;
	} else {
		document.randomRollForm.randomRoll[1].checked = true;
	}
}

function playlistSwitch(obj) {
        if (obj['newstate'] == "COMPLETED") {
		nextItem();
        }
}

function nextItem() {
	if (cpi > 0) {
		cpi = cpi+1;
		loadNextPlaylistItem(getPlaylistItem(cpi), cpq);
	} else {
		if (document.randomRollForm.randomRoll[0].checked) {
        		loadNextPlaylistItem(getNextRolling('random'), 1);
		} else {
			var q = unescape(window.location.hash.substring(1))
			var i = q.split("/");
			var x = parseInt(i[1]);
			loadNextPlaylistItem(getNextRolling(x), 1);
		}
	}
}

function startPlaylist(id, q) {
	var tvid = getPlaylistItem(id);
	cpq = q;
	loadVid(tvid, q);
	cpi=id;
	setTimeout('loadFiles(\'playlist\', 0)', 2000);
}

function getPlaylistItem(id) {
        if (window.XMLHttpRequest) {
                xhr3 = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
                xhr3 = new ActiveXObject("Microsoft.XMLHTTP");
        }
	xhr3.open('GET', '/advfile.php?a=gpi&id='+id, false);
	xhr3.send(null);
	if (xhr3.responseText == 'error') {
                document.getElementById('vidcel').innerHTML = "Error!";
        } else if (xhr3.responseText == "eop") {
                document.getElementById('vidcel').innerHTML = "Playlist Over!"; 
        } else {
                tvid = xhr3.responseText;
        }
	return tvid;
}			

function getNextRolling(id) {
	var a;
	if (window.XMLHttpRequest) {
                xhr3 = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
                xhr3 = new ActiveXObject("Microsoft.XMLHTTP");
        }
	xhr3.open('GET', '/advfile.php?a=gnr&id='+id, false);
	xhr3.send(null);
	if (xhr3.responseText == 'error') {
                document.getElementById('vidcel').innerHTML = "Error!";
	} else {
		tvid = xhr3.responseText;
	}
	return tvid;
}

function getSeasonId(id) {
	if (window.XMLHttpRequest) {
                xhr4 = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
                xhr4 = new ActiveXObject("Microsoft.XMLHTTP");
        }
	xhr4.open('GET', '/advfile.php?a=gsi&id='+id, false);
        xhr4.send(null);
	return xhr4.responseText;
}

function playlistMod(func, id) {
        if (window.XMLHttpRequest) {
                xhr5 = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
                xhr5 = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xhr5.onreadystatechange = function () {
                if (xhr5.readyState == 4) {
                        if (xhr5.status == 200) {
                                document.getElementById('tt').innerHTML = xhr5.responseText;
                        } else {
                                document.getElementById('tt').innedHTML = xhr5.status;
                        }
                        if (func == 'pd' && !(id == -1)) {
                                loadFiles("playlist", 0);
                        }
			if (!(id == -1)) {
                       		setTimeout('document.getElementById(\'tt\').style.visibility = \'hidden\'', 500);
			}
                } else {
                        document.getElementById('tt').innerHTML = loadmsg;
                        document.getElementById('tt').style.visibility = "visible";
			document.getElementById('tt').style.top = document.getElementById("filecel").offsetTop+50+window.pageYOffset;
			document.getElementById('tt').style.left = document.getElementById("filecel").offsetLeft+50;
                }
        }
        xhr5.open('GET', '/advfile.php?a='+func+'&id='+id, true);
        xhr5.send(null);
}

function loadPV(vid, q) {
	document.getElementById('vidcel').innerHTML = "<img src=/mewtube.png>";
	document.getElementById('vpucel').innerHTML = "[<a href=\"javascript: loadVid('"+vid+"', "+q+");\">PopIn</a>]";
	if (q == 1 || q == 3) {
		window.open('vpu.php?vid='+vid+'&q='+q, "MewTubeVideo", "menubar=no,innerWidth=640,innerHeight=368,toolbar=no");
	} else {
		window.open('vpu.php?vid='+vid+'&q='+q, "MewTubeVideo", "menubar=no,innerWidth=480,innerHeight=320,toolbar=no");
	}
}

function loadFiles(action, id) {
	if (window.XMLHttpRequest) {
        	xhr6 = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
        	xhr6 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xhr6.onreadystatechange = function () {
		if (xhr6.readyState == 4) {
			if (xhr6.status == 200) {
				document.getElementById('filecel').innerHTML = xhr6.responseText;
				if (loaded == 1) {
					newStats();
				}
			} else {
				document.getElementById('filecel').innerHTML = xhr6.status;
			}
		} else {
			if (action != 'login' && action != 'mod_enc_proc') {
				document.getElementById('filecel').innerHTML = loadmsg;
			} else {
				document.getElementById('sex').innerHTML = loadmsgtxt;
			}
		}
	}
	if (action == 'main') {
		xhr6.open('GET', '/advfile.php', true);
		xhr6.send(null);
	} else if (action == 'login') {
		xhr6.open("POST", "/advfile.php?a=login", true);
        	var params = "captcha="+document.getElementById('captcha').value+"&username="+document.getElementById('username').value+"&password="+document.getElementById('password').value;
        	xhr6.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	xhr6.setRequestHeader("Content-length", params.length);
        	xhr6.setRequestHeader("Connection", "close");
        	xhr6.send(params);
		url_check();
<?php if ($_SESSION['perms'] >= 10) { ?>
	} else if (action == 'mod_enc_proc') {
		var params = "enc_te="+grv('enc_te')+"&enc_title_new="+document.getElementById('enc_title_new').value+"&enc_title=";
		params += document.getElementById('enc_title').value+"&enc_description="+document.getElementById('enc_description').value;
		params += "&enc_type="+grv('enc_type')+"&enc_sn="+document.getElementById('enc_sn').value+"&enc_folder=";
		params += document.getElementById('enc_folder').value+"&submit=Encode";
		xhr6.open("POST", "/advfile.php?a=mod_enc_add", true);
		xhr6.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr6.setRequestHeader("Content-length", params.length);
                xhr6.setRequestHeader("Connection", "close");
                xhr6.send(params);
	} else if (action == 'mod_edit_priority_change') {
		var params = '';
		var e = document.getElementById('prioritys').elements;
		for (var i=0;i<e.length;i++) {
			if (e[i].value != 'Update') {
				if (i!=0) {
					params += '&';
				}
				params += e[i].name + '=' + e[i].value;
			}
		}
		xhr6.open("POST", "/advfile.php?a=mod_edit_priority_change", true);
		xhr6.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr6.setRequestHeader("Content-length", params.length);
                xhr6.setRequestHeader("Connection", "close");
                xhr6.send(params);
		loadFiles("queue", 0);
<?php } ?>
	} else {
		xhr6.open('GET', '/advfile.php?a='+action+'&id='+id, true);
		xhr6.send(null);
	}
	if (action == 'expired') {
		document.getElementById('tcel').innerHTML = "<h1><?php echo $sitename; ?> - Session Expired";
	}	
}

function newCaptcha() {
	if (window.XMLHttpRequest) {
	        xhr7 = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
	        xhr7 = new ActiveXObject("Microsoft.XMLHTTP");
	}
        xhr7.onreadystatechange = function () {
                if (xhr7.readyState == 4) {
                        if (xhr7.status == 200) {
                                document.getElementById('hvt').innerHTML = xhr7.responseText;
                                document.getElementById('captcha').value = '';
                        } else {
                                document.getElementById('hvt').innerHTML = xhr7.status;
                        }
                } else {
			document.getElementById('hvt').innerHTML = loadmsgtxt;
		}
        }
        xhr7.open('GET', '/advfile.php?a=ajlol', true);
        xhr7.send(null);
}

function newStats() {
	if (window.XMLHttpRequest) {
        	xhr8 = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		xhr8 = new ActiveXObject("Microsoft.XMLHTTP");
	}
        xhr8.onreadystatechange = function () {
                if (xhr8.readyState == 4) {
                        if (xhr8.status == 200) {
                                document.getElementById('stats').innerHTML = xhr8.responseText;
                        } else {
                                document.getElementById('stats').innerHTML = xhr8.status;
                        }
                } else {
			document.getElementById('stats').innerHTML = loadmsgtxt;
		}
        }
        xhr8.open('GET', '/advfile.php?a=stat', true);
        xhr8.send(null);
}

document.onkeyup = function(e) {
        	     	if(e.which == 13) {
			        pco.sendEvent("PLAY");
                        }
                   }

<?php if ($_SESSION['perms'] >= 10) { ?>
function grv(elem) {
	if (document.forms['enc_add'].elements[elem]) {
		rvobj = document.forms['enc_add'].elements[elem];
		for (var i=0;i<rvobj.length;i++) {
			if(rvobj[i].checked) {
				return rvobj[i].value;
			}
		}
	} else {
		return '';
	}
}

<?php } ?>
</script>
<title><?php echo $sitename; ?></title>
</head>
<body background="/beegees/<?php echo $bgs[rand(0, count($bgs)-1)]; ?>" onload="url_check();loaded=1;" vlink=blue>
<table border=1 width=100% cellpadding=10 cellspacing=0>
<tr><td colspan=2 align=center>
<div id='tcel'><h1><?php echo $sitename; ?></h1></div>
<div id='greet'>Welcome back <b><?php echo $_SESSION['username']; ?></b>!
<br>Message of the Day: <?php echo $motd; ?><br></div>
<form name="randomRollForm"><div style="margin:0;padding:0;display:inline">Random Rolling: <input type=radio name="randomRoll" value="-1">On <input type=radio name="randomRoll" value="0" checked>Off 
<span id='nextButton'>[Next]</span></div></form>
<div id="menu"><ul><li><a href='#main' onClick='javascript: loadFiles("main", 0);'>Shows</a></li><li><a href='#vid-hq/random' onClick='javascript: setRandomRoll(-1);loadVid("random", 1);'><b>HQ Random</b></a><a href='#vid/random' onClick='javascript: loadVid("random", 0);'>Random</a></li><li><a href='#movies' onClick='javascript: loadFiles("movies", 0);'>Movies</a></li><li><a href='#playlist' onClick='javascript: loadFiles("playlist", 0);'>Playlist</a></li><li><a href='#latest-eps' onClick='javascript: loadFiles("latest-eps", 0);'>Latest Episodes</a></li><li><a href='#queue' onClick='javascript: loadFiles("queue", 0);'>Encode Queue</a></li><?php
	if ($_SESSION['perms'] >= 10) {
		echo "<li><a href='#' onClick='javascript: loadFiles(\"mod_enc_add\", 0);'>Add Encode</a></li>";
		echo "<li><a href='#' onClick='javascript: loadFiles(\"mod_ips\", 0);'>Failed IPs</a></li>";
		#echo "<li><a href='#' onClick='javascript: loadFiles(\"mod\", 0);'>Moderator</a></li>"; 
	}
	if ($_SESSION['perms'] >= 100) {
		echo "<li><a href='#admin_menu' onClick=\"javascript: loadFiles('admin_menu', null);\">Admin</a></li>";
	}
?><li><a href='/advfile.php?a=logout'>Logout</a></li></ul></div></td></tr>
<tr><td width=500 valign=top>
<div id='vidcel'>
<img src="/mewtube.png">
</div><br>
<center><div id='vpucel'></div></center>
</td><td id='filecel' valign=top>
<script type='text/javascript'>document.write(loadmsg);</script> 
</td></tr>
<tr><td align=center colspan=2><div id=stats><script type='text/javascript'>document.write(loadmsgtxt);</script></div><?php echo strtolower($sitename)." v".$mtv; ?></td></tr>
</table><div id='tt'><script type='text/javascript'>document.write(loadmsg);</script></div><div id='overlay'></div></body>
</html>
