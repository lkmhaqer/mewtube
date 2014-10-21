<?php
session_start();
include("auth.php");
$db = new db();
$id = (int)$_GET['id'];
$pb = new pageBuilder();
$vr = "/srv/svs";
$rr = $relativeContentDir;
if (strstr($_GET['a'], "mod_") && $_SESSION['perms'] < 10) {
	echo("PERMISSION DENIED");
	exit;
}
if (strstr($_GET['a'], "admin_") && $_SESSION['perms'] < 100) {
	echo("PERMISSION DENIED");
	exit;
}
switch ($_GET['a']) {
	case "vid":
		if ($_GET['id'] == "random") {
			$db->Query("SELECT `episodes`.`eid`, `episodes`.`filename` FROM `episodes` ORDER BY RAND() LIMIT 1", array());
			$row = $db->getRow();
			$tv = $row['filename'];
			echo($rr.$tv."|".$row['eid']);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
				    array($_SESSION['uid'], $row['eid'], '0', time()));
			break;
		} elseif ((int)$_GET['id'] != 0) {
			$db->Query("SELECT `episodes`.`eid`, `episodes`.`filename` FROM `episodes` WHERE `eid` = '%0' LIMIT 1", array($_GET['id']));
			$row = $db->getRow();
			$tv = $row['filename'];
			echo($rr.$tv);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
				    array($_SESSION['uid'], $row['eid'], '0', time()));
		}
		break;
        case "vid-hq":
                if ($_GET['id'] == "random") {
                        $db->Query("SELECT `episodes`.`eid`, `episodes`.`filename` FROM `episodes` ORDER BY RAND() LIMIT 1", array());
                        $row = $db->getRow();
                        $tv = substr($row['filename'], 0, -4)."-hq.flv";
			echo($rr.$tv."|".$row['eid']);
                        $db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
				    array($_SESSION['uid'], $row['eid'], '1', time()));
                        break;
                } elseif ((int)$_GET['id'] != 0) {
                        $db->Query("SELECT `episodes`.`eid`, `episodes`.`filename` FROM `episodes` WHERE `eid` = '%0' LIMIT 1", array($_GET['id']));
                        $row = $db->getRow();
                        $tv = substr($row['filename'], 0, -4)."-hq.flv";
			echo($rr.$tv);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
                                    array($_SESSION['uid'], $row['eid'], '1', time()));
		}
                break;
	case "movie":
		if ($_GET['id'] == "random") {
			$db->Query("SELECT `movies`.`mid`, `movies`.`filename` FROM `movies` ORDER BY RAND() LIMIT 1", array());
			$row = $db->getRow();
			$tv = $row['filename'];
			echo($rr.$tv."|".$row['eid']);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
                                    array($_SESSION['uid'], $row['mid'], '2', time()));
			break;
		} elseif ((int)$_GET['id'] != 0) {
			$db->Query("SELECT `movies`.`mid`, `movies`.`filename` FROM `movies` WHERE `mid` = '%0' LIMIT 1", array($_GET['id']));
			$row = $db->getRow();
			$tv = $row['filename'];
			echo($rr.$tv);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
                                    array($_SESSION['uid'], $row['mid'], '2', time()));
		}
		break;
	case "movie-hq":
		if ($_GET['id'] == "random") {
			$db->Query("SELECT `movies`.`mid`, `movies`.`filename` FROM `movies` ORDER BY RAND() LIMIT 1", array());
                        $row = $db->getRow();
			$tv = substr($row['filename'], 0, -4)."-hq.flv";
			echo($rr.$tv."|".$row['eid']);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
                                    array($_SESSION['uid'], $row['mid'], '3', time()));
			break;
		} elseif ((int)$_GET['id'] != 0) {
			$db->Query("SELECT `movies`.`mid`, `movies`.`filename` FROM `movies` WHERE `mid` = '%0' LIMIT 1", array($_GET['id']));
                        $row = $db->getRow();
			$tv = substr($row['filename'], 0, -4)."-hq.flv";
			echo($rr.$tv);
			$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
                                    array($_SESSION['uid'], $row['mid'], '3', time()));
		}
		break;
	case "stat":
		$db->Query("SELECT `iplog`.`ip` FROM `iplog` WHERE uid = '%0' ORDER BY logid DESC LIMIT 2", array($_SESSION['uid']));
		if ($db->getNumRows() < 2) {
			echo("Current IP: ".$_SERVER['REMOTE_ADDR']." | First Time Logged In!");
		} else {
			$row1 = $db->getRow();
			$row2 = $db->getRow();
			echo("Current IP: ".$row1['ip']." | Last IP(<a href=\"#iplog\" onclick=\"javascript: loadFiles('iplog', 0);\">s</a>): ".$row2['ip']);
		}
		echo(" | Disk Usage: ".round((disk_total_space($vr) - disk_free_space($vr))/1073741824)."GB of ".round(disk_total_space($vr)/1073741824)."GB");
		$db->Query("SELECT `episodes`.`name`, `episodes`.`eid` FROM `episodes` ORDER BY eid DESC", array());
		echo("<br>Episode Count: ".$db->getNumRows()." | Last Added: ");
		$row = $db->getRow();
		echo("[<a href='#vid-hq/".$row['eid']."' onClick=\"javascript: loadVid(".$row['eid'].", 1);\"><b>HQ</b></a>] ");
		echo("<a href='#vid/".$row['eid']."' onClick=\"javascript: loadVid(".$row['eid'].", 0);\">".$row['name']."</a>");
		$db->Query("SELECT `movies`.`mid`, `movies`.`title` FROM `movies` ORDER BY `mid` DESC", array());
		echo("<br>Movie Count: ".$db->getNumRows()." | Last Added: ");
		$row = $db->getRow();
		echo("[<a href='#' onClick=\"javascript: loadVid(".$row['mid'].", 3);\"><b>HQ</b></a>] ");
		echo("<a href='#' onClick=\"javascript: loadVid(".$row['mid'].", 2);\">".$row['title']."</a>");
		break;
	case "latest-eps":
		$db->Query("SELECT `episodes`.`eid`, `episodes`.`name`, `episodes`.`edescription`, `episodes`.`filename`, `episodes`.`sid`, `episodes`.`seasonid`, `shows`.`title`, `seasons`.`num` FROM `episodes`
		JOIN `shows` ON `shows`.`sid` = `episodes`.`sid` JOIN `seasons` ON `seasons`.`seasonid` = `episodes`.`seasonid` ORDER BY `eid` DESC LIMIT 10", array());
		if ($db->getNumRows() == 0) {
                        echo("No episodes found");
                        exit;
                }
		echo("<h2>Latest Episodes</h2>");
		echo("<br><hr>");
		$currentShow = '';
                for ($k=$db->getNumRows();$k>0;$k--) {
                        $row = $db->getRow();
			if ($currentShow != $row['title'].$row['num']) {
				$currentShow = $row['title'].$row['num'];
				if ($row['num'] == 0) {
					$season = "Specials";
				} else {
					$season = "Season ".$row['num'];
				}
				echo("<h3><a href='#show/".$row['sid']."' onClick=\"javascript: loadFiles('show', ".$row['sid'].");\">".$row['title']."</a> - ");
				echo("<a href='#season/".$row['seasonid']."' onClick=\"javascript: loadFiles('season', ".$row['seasonid'].");\">".$season."</a></h3>");
			}
			$pb->doEpisode($row['filename'], $row['eid'], $row['name'], $row['edescription']);
                }
		break;
	case "latest-season":
		$db->Query("SELECT `seasons`.`seasonid`, `seasons`.`num`, `seasons`.`sdescription`, `shows`.`sid`, `shows`.`title` FROM `seasons`
		JOIN `shows` ON `shows`.`sid` = `seasons`.`sid` ORDER BY `seasonid` DESC LIMIT 20", array());
		if ($db->getNumRows() == 0) {
			echo("No seasons found");
			exit;
		}
		echo("<h2>Latest Seasons</h2>");
		echo("<br><hr>");
		while ($row = $db->getRow()) {
			echo("<h3><a href='#show/".$row['sid']."' onClick=\"javascript: loadFiles('show', ".$row['sid'].");\">".$row['title']."</a> - ");
			if ($row['num'] == 0) {
				$season = "Specials";
			} else {
				$season = "Season ".$row['num'];
			}
			echo("<a href='#season/".$row['seasonid']."' onClick=\"javascript: loadFiles('season', ".$row['seasonid'].");\">".$season."</a></h3>");
		}
		break;
	case "admin_menu":
		echo("<h2>Admin Menu</h2><br><hr>");
		echo("<a href='#admin_user' onClick=\"javascript: loadFiles('admin_user', null);\">Users</a>");
		break;
	case "admin_user":
		if ($_GET['id'] == 'null') {
			$db->Query("SELECT `users`.`uid`, `users`.`username`, `users`.`password`, `users`.`perms` FROM `users`", array());
			if ($db->getNumRows() == 0) {
				echo("No users found :(");
				exit;
			}
			echo("<h2>Users</h2>");
			echo("<br><a href='#admin_create_user' onClick=\"javascript: loadFiles('admin_create_user', null);\">Create User</a><hr>");
			while ($row = $db->getRow()) {
				echo("<a href='#admin_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_user', ".$row['uid'].");\"><b>".$row['username']."</b></a>");
				if ($row['password'] == '' && $row['perms'] > 0) {
					echo(" | <font color='red'><b>Empty Password</b></font>");
				}
				if ($row['perms'] <= 0) {
					echo(" | Deactivated");
				} elseif ($row['perms'] >= 100) {
					echo(" | <font color='#CC6600'><b>Admin</b></font>");
				} elseif($row['perms'] >= 10) {
					echo(" | <font color='#000099'><b>Moderator</b></font>");
				}
				echo("<br>");
			}
		} else {
			$db->Query("SELECT `users`.`uid`, `users`.`username`, `users`.`email`, `users`.`perms`, `users`.`homepath`, `users`.`password` FROM `users`
			WHERE `users`.`uid` = '%0' LIMIT 1", array($_GET['id']));
			if ($db->getNumRows() == 0) {
				echo("User not found");
				exit;
			}
			$row = $db->getRow();
			echo("<h2>User - ".$row['username']."</h2><br><a href='/#admin_user' onClick=\"javascript: loadFiles('admin_user', null);\">");
			echo("Users</a><hr>");
			if ($row['password'] == '') {
				echo("<font color='red'><b>Warning: User's password is empty.</b></font><br>");
			}
			echo("Email: ".$row['email']."<br>");
			echo("Permissions: ".$row['perms']."<br>");
			echo("Homepath: ".$row['homepath']."<br>");
			echo("<a href='#admin_reset_pwd/".$row['uid']."' onClick=\"javascript: loadFiles('admin_reset_pwd', ".$row['uid'].");\">");
			echo("Reset</a> user password.<br>");
			if ($row['perms'] > 0) {
				echo("<a href='#admin_deactivate_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_deactivate_user', ".$row['uid'].");\">");
				echo("Deactivate</a> user");
			} else {
				echo("<a href='#admin_activate_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_activate_user', ".$row['uid'].");\">");
				echo("Activate</a> User");
			}
			echo("<h3>IP History:</h3>");
			$db->Query("SELECT `iplog`.`ip`, `iplog`.`timestamp`, `iplog`.`fail` FROM `iplog` WHERE uid = '%0' ORDER BY logid DESC", array($row['uid']));
                	echo("<table><tr><td><b>Date</b></td><td align=center><b>IP &nbsp;(<font color=red>failed</font>)</b></td></tr>");
                	while ($row = $db->getRow()) {
                        	echo("<tr><td>".date('F j, Y, g:i a', $row['timestamp'])."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=right>");
                        	if ($row['fail'] == 1) {
                                	echo("<font color=red><b>".$row['ip']."</b></font>");
                        	} else {
                                	echo($row['ip']);
                        	}
                        	echo("</td></tr>");
                	}
                echo("</table>");
		}
		break;
	case "admin_reset_pwd":
		echo("<h2>User Password Reset</h2><br><hr>");
		$db->Query("SELECT `users`.`uid`, `users`.`username` FROM `users` WHERE `users`.`uid` = '%0' LIMIT 1", array($_GET['id']));
		if ($db->getNumRows() == 0) {
			echo("User not found");
			exit;
		}
		$row = $db->getRow();
		$db->Query("UPDATE `users` SET `users`.`password` = '' WHERE `users`.`uid` = '%0' LIMIT 1", array($row['uid']));
		echo("Reset password for <a href='#admin_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_user', ".$row['uid'].");\">".$row['username']."</a> completed");
		break;
	case "admin_create_user":
		echo("<h2>User Creation Page</h2><br><hr>");
		if ($_GET['id'] != 'null') {
			$db->Query("SELECT `users`.`uid` FROM `users` WHERE `users`.`username` = '%0' LIMIT 1", array($_GET['id']));
			if ($db->getNumRows() > 0) {
				echo("Username already exists");
				exit;
			}
			$db->Query("INSERT INTO `users` (`uid`, `username`, `password`, `email`, `perms`, `homepath`) VALUES
			(NULL, '%0', '', 'suck@lol.com', '1', '')", array($_GET['id']));
			$db->Query("SELECT `users`.`uid`, `users`.`username` FROM `users` WHERE `users`.`uid` = '%0' LIMIT 1", array($db->getInsertId()));
			$row = $db->getRow();
			echo("User <a href='#admin_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_user', ".$row['uid'].");\">".$row['username']."</a> was created?");
		} else {
			echo("<form action=\"javascript: loadFiles('admin_create_user', document.getElementById('username').value);\" method=\"GET\">");
			echo("Username: <input type='text' id='username' name='username'><br><input type=submit value='Create'></form>");
		}
		break;
	case "admin_deactivate_user":
		echo("<h2>User Deactivation Page</h2><a href='/#admin_user' onClick=\"javascript: loadFiles('admin_user', null);\">");
                echo("Users</a><br><hr>");
		$db->Query("SELECT `users`.`uid`, `users`.`username` FROM `users` WHERE `users`.`uid` = '%0' LIMIT 1", array($_GET['id']));
		if ($db->getNumRows() == 0) {
			echo("User not found");
			exit;
		}
		$row = $db->getRow();
		$db->Query("UPDATE `users` SET `users`.`perms` = '0' WHERE `users`.`uid` = '%0' LIMIT 1", array($row['uid']));
                echo("User <a href='#admin_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_user', ".$row['uid'].");\">".$row['username']."</a> was deactivated?");
		break;
	case "admin_activate_user":
		echo("<h2>User Activation Page</h2><a href='/#admin_user' onClick=\"javascript: loadFiles('admin_user', null);\">");
                echo("Users</a><br><hr>");
		$db->Query("SELECT `users`.`uid`, `users`.`username` FROM `users` WHERE `users`.`uid` = '%0' LIMIT 1", array($_GET['id']));
                if ($db->getNumRows() == 0) {
                        echo("User not found");
                        exit;
                }
		$row = $db->getRow();
		$db->Query("UPDATE `users` SET `users`.`perms` = '1' WHERE `users`.`uid` = '%0' LIMIT 1", array($row['uid']));
		echo("User <a href='#admin_user/".$row['uid']."' onClick=\"javascript: loadFiles('admin_user', ".$row['uid'].");\">".$row['username']."</a> activated?");
		break;
	case "show":
		$db->Query("SELECT `seasons`.`num`, `seasons`.`sdescription`, `seasons`.`seasonid`, `shows`.`title`, `shows`.`description` FROM `seasons`, `shows` 
		WHERE `seasons`.`sid` = '%0' AND `seasons`.`sid` = `shows`.`sid` ORDER BY `seasons`.`num` ASC", array($id));
		if ($db->getNumRows() == 0) {
			echo("No seasons found");
			exit;
		}
		for ($k=$db->getNumRows();$k>0;$k--) {
			$row = $db->getRow();
			if ($k == $db->getNumRows()) {
				echo("<h2>".$row['title']."</h2><br>".$row['description']."</b> - <a href='#main' onClick=\"javascript: loadFiles('main', 0);\">back</b></a><hr>");
				echo("<a href='#season/".$row['seasonid']."' onClick=\"javascript: loadFiles('season', ".$row['seasonid'].");\"><b>");
				if ($row['num'] == 0) {
					echo("Specials");
				} else {
					echo("Season ".$row['num']);
				}
				echo("</b></a> ");
				echo(" [<a href='#pax/".$row['seasonid']."' onClick=\"javascript: playlistMod('pax', ".$row['seasonid'].");\">+P</a>] ");
				echo($row['sdescription']."<br>");
			} else {
				echo("<a href='#season/".$row['seasonid']."' onClick=\"javascript: loadFiles('season', ".$row['seasonid'].");\">");
				echo("<b>Season ".$row['num']."</b></a> ");
				echo(" [<a href='#pax/".$row['seasonid']."' onClick=\"javascript: playlistMod('pax', ".$row['seasonid'].");\">+P</a>] ");
				echo($row['sdescription']."<br>");
			}
		}
		break;
	case "season":
		$db->Query("SELECT `shows`.`title`, `shows`.`sid`, `shows`.`description`, 
		`seasons`.`num`, `seasons`.`seasonid`, `seasons`.`sdescription`, `seasons`.`sid`, 
		`episodes`.`eid`, `episodes`.`name`, `episodes`.`edescription`, `episodes`.`filename` FROM `seasons` 
		JOIN `shows` ON `shows`.`sid` = `seasons`.`sid` JOIN `episodes` ON `episodes`.`seasonid` = `seasons`.`seasonid` 
		WHERE `seasons`.`seasonid` = '%0' ORDER BY `episodes`.`name` ASC", array($id));
		if ($db->getNumRows() == 0) {
			echo("No episodes found");
			exit;
		}
		for ($k=$db->getNumRows();$k>0;$k--) {
			$row = $db->getRow();
			if ($k == $db->getNumRows()) {
				echo("<h2>".$row['title']." - ");
				if ($row['num'] == 0) {
					echo("Specials");
				} else {
					echo("Season ".$row['num']);
				}
				echo(" [<a href='#pax/".$row['seasonid']."' onClick=\"javascript: playlistMod('pax', ".$row['seasonid'].");\">+P</a>]");
				echo("</h2><b>".$row['description']."</b>");
				if ($row['sdecription']) {
					echo("<br>".$row['sdescription']);
				}
				echo(" - <a href='#show/".$row['sid']."' onClick=\"javascript: loadFiles('show', ".$row['sid'].");\">back</a><hr>");
				$pb->doEpisode($row['filename'], $row['eid'], $row['name'], $row['edescription']);
			} else {
				$pb->doEpisode($row['filename'], $row['eid'], $row['name'], $row['edescription']);
			}
		}
		break;
	case "movies":
		$db->Query("SELECT `movies`.`mid`, `movies`.`title`, `movies`.`filename`, `movies`.`description` FROM `movies` ORDER BY `movies`.`title` ASC", array());
		if ($db->getNumRows() == 0) {
			echo("No movies");
			exit;
		}
		echo("<h2>Movies</h2><b>The newest section of mewtube!</b><hr>");
		while ($row = $db->getRow()) {
			$imgu = substr($row['filename'], 0, -4).".jpg";
			if (file_exists("thumbs/still-".$imgu)) {
				echo("<img src='thumbs/still-".$imgu."' align=left>");
			}
			echo("[<a href='#movie-hq/".$row['mid']."' onClick=\"javascript: loadVid(".$row['mid'].", 3);\"><b>HQ</b></a>] ");
			echo("<a href='#movie/".$row['mid']."' onClick=\"javascript: loadVid(".$row['mid'].", 2);\">".$row['title']."</a><br>".$row['description']);
			echo("<br clear=all>&nbsp;<br>");
		}
		break;
	case "gnr":
		if ($_GET['id'] == 'random') {
			$db->Query("SELECT `episodes`.`eid`, `episodes`.`filename` FROM `episodes` ORDER BY RAND() LIMIT 1", array());
		} else {
			$db->Query("SELECT `episodes`.`eid` FROM `episodes` WHERE `episodes`.`eid` >= '%0' ORDER BY `episodes`.`eid` LIMIT 2", array($_GET['id']));
			$db->getRow();
		}
		if ($db->getNumRows() <= 0) {
                        echo("error");
                        exit;
                }
		$row = $db->getRow();
		echo($row['eid']);
		break;
	case "gpi":
		$db->Query("SELECT `playlist`.`peid`, `playlist`.`vidid`, `episodes`.`eid` FROM `playlist` JOIN `episodes` ON `episodes`.`eid` = `playlist`.`vidid` 
			    WHERE `playlist`.`uid` = '%0' ORDER BY PEID", array($_SESSION['uid']));
		if ($db->getNumRows() == 0) {
			echo("error");
			exit;
		}
		$i=1;
		while ($row = $db->getRow()) {
			if ((int)$_GET['id'] == $i) {
				echo($row['eid']);
				$_SESSION['cpi'] = $i;
				exit;
			}
			$i++;
		}
		$_SESSION['cpi'] = 0;
		echo("eop");
		break;
	case "gsi":
		$db->Query("SELECT `episodes`.`seasonid` FROM `episodes` WHERE `episodes`.`eid` = '%0' LIMIT 1;", array($_GET['id']));
		if ($db->getNumRows() <= 0) {
			echo("how....");
			exit;
		}
		$row = $db->getRow();
		echo($row['seasonid']);
		break;
	case "pas":
		$db->Query("SELECT `episodes`.`eid` FROM `episodes` WHERE eid = '%0' LIMIT 1", array($_GET['id']));
		if ($db->getNumRows() <= 0) {
			echo("Are you fucking retarded?");
			exit;
		}
		$row = $db->getRow();
		$db->Query("INSERT INTO `playlist` (`peid`, `uid`, `vidid`) VALUES (NULL, '%0', '%1')", array($_SESSION['uid'], $row['eid']));
		echo("<br><br>Show added to playlist<br><br><br>");
		break;
	case "pax":
		$db->Query("SELECT `episodes`.`seasonid` FROM `episodes` WHERE seasonid = '%0' LIMIT 1", array($_GET['id']));
		if  ($db->getNumRows() <= 0) {
                        echo("Are you fucking retarded?");
                        exit;
                }
		$row = $db->getRow();
		$db->Query("INSERT INTO `playlist` (`peid`, `uid`, `vidid`) SELECT NULL, %0, eid FROM `episodes` WHERE seasonid = '%1'",
			array($_SESSION['uid'], $row['seasonid']));
		echo("<br>Season added to playlist<br><br>$db->errorMsg<br>");
		break;
	case "pam":
		$db->Query("SELECT `movies`.`mid` FROM `movies` WHERE mid = '%0' LIMIT 1", array($_GET['id']));
		if ($db->getNumRows() <= 0) {
			echo("Are you fucking retarded?");
			exit;
		}
		$row = $db->getRow();
		$db->Query("INSERT INTO `playlist` (`peid`, `uid`, `vidid`) VALUES (NULL, '%0', '%1') LIMIT 1", array($_SESSION['uid'], $row['mid']));
		echo("Movie added to playlist");
		break;
	case "pd":
		if ((int)$_GET['id'] == -1) {
			echo("<br><br>Are you sure?<br><br><a href='#' onClick='javascript: playlistMod(\"pd\", -2);'>Yes</a>&nbsp;&nbsp;&nbsp;<a href='#' onClick='javascript: playlistMod(\"pd\", -3);'>No</a><br><br><br>");
			exit;
		} elseif ((int)$_GET['id'] == -3) {
			echo("<br><br>Delete all aborted<br><br><br>");
			exit;
		} elseif ((int)$_GET['id'] == -2) {
			$db->Query("DELETE FROM `playlist` WHERE `playlist`.`uid` = '%0'", array($_SESSION['uid']));
		} else {
			$db->Query("DELETE FROM `playlist` WHERE `playlist`.`peid` = '%0' AND `playlist`.`uid` = '%1' LIMIT 1", array($_GET['id'], $_SESSION['uid']));
		}
		echo("<br><br>Playlist Item(s) Deleted<br><br><br>");
		break;
	case "playlist":
		$db->Query("SELECT `playlist`.`peid`, `playlist`.`vidid`, `episodes`.`filename` FROM `playlist` JOIN `episodes` ON `episodes`.`eid` = `playlist`.`vidid` 
			    WHERE uid = '%0' ORDER BY PEID", array($_SESSION['uid']));
		if ($db->getNumRows() <= 0) {
			echo("<table cellpadding=20><tr><td align=center colspan=2><h3>".$_SESSION['username']."'s Playlist</h3>");
			echo("No items in your playlist.");
			echo("</td></tr></table>");
			exit;
		}
		echo("<table><tr><td align=center colspan=2><h3>".$_SESSION['username']."'s Playlist</h3><a href='#' onClick='javascript: playlistMod(\"pd\", -1);'>Delete All</a><br><br></td></tr>");
		$i=1;
		while ($row = $db->getRow()) {
			echo("<tr><td>[<a href='#' onClick=\"javascript: playlistMod('pd', ".$row['peid'].");\">X</a>]&nbsp;".$i."&nbsp;&nbsp;</td><td>[<a href='#' onClick='javascript: startPlaylist(".$i.", 1);'><b>HQ</b></a>] <a href='#' onClick='javascript: startPlaylist(".$i.", 0);'>");
			if ($_SESSION['cpi'] == $i) {
                       		echo("<b>".substr($row['filename'], 0, -4)."<b>");
                	} else {
                       		echo(substr($row['filename'], 0, -4));
                	}
                	echo("</a>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>");
			$i++;
        	}
		break;
	case "queue":
		if ($_SESSION['perms'] >= 10) {
			$colSpan = 3;
			echo("<form name='prioritys' id='prioritys' action='javascript: loadFiles(\"mod_edit_priority_change\", this.form);' method='POST'>");
		} else {
			$colSpan = 2;
		}
		echo("<table width=320><tr bgcolor='#CCCCCC'><td align=center colspan=".$colSpan."><h3><br>Transcode Queue</h3></td></tr>");
		echo("<tr bgcolor='#CCCCCC'><td><b>Show</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>");
		echo("<td><b>Season</b>&nbsp;&nbsp;&nbsp;</td>");
		if ($_SESSION['perms'] >= 10) {
			echo("<td><b>Priority</b>&nbsp;&nbsp;&nbsp;</td>");
		}
		echo("</tr>");
		$db->Query("SELECT `encode_jobs`.`jid`, `encode_jobs`.`priority`, `encode_jobs`.`snum`, `encode_jobs`.`show`, `encode_jobs`.`curfile`
			    FROM `encode_jobs` WHERE `encode_jobs`.`completed` = '0'
			    ORDER BY `encode_jobs`.`priority` DESC, `encode_jobs`.`jid` ASC", array());
		if ($db->getNumRows() == 0) {
			echo("<tr><td align=center colspan=".$colSpan.">");
			echo("<br>&nbsp;&nbsp;&nbsp;&nbsp;No encoding jobs at the moment.&nbsp;&nbsp;&nbsp;&nbsp;<br><br></td></tr></table>");
		} else {
			while ($row = $db->getRow()) {
				if ($row['curfile'] != '') {
					echo("<tr bgcolor='#CCFF99'><td>".$row['show']."&nbsp;&nbsp;&nbsp;&nbsp;");
					echo("</b><br><small>[".$row['curfile']."]</small>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>");
				} else {
					echo("<tr><td>".$row['show']."&nbsp;&nbsp;&nbsp;&nbsp;</td><td>");
				}
				switch ($row['snum']) {
					case 0:
						echo("Specials");
						break;
					case -1:
						echo("Movie");
						break;
					default:
						echo("Season ".$row['snum']);
				}
				echo("&nbsp;&nbsp;&nbsp;");
				if ($_SESSION['perms'] >= 10) {
					echo("</td><td align=center><input type='text' name='jid".$row['jid']."' value='".$row['priority']."' size='2' /></td></tr>");
				} else {
					echo("</td></tr>");	
				}
			}
			if ($_SESSION['perms'] >= 10) {
				echo("<tr><td colspan=3 align='center'><input type='submit' value='Update' /></form></td></tr>");
			}
			echo("</table>");
		}
		echo("<br><table width=320><tr bgcolor='#CCCCCC'><td align=center colspan=2><h3><br>Subscribed Shows</h3></td></tr>");
                echo("<tr bgcolor='#CCCCCC'><td><b>Show</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>");
                echo("<td><b>Season</b>&nbsp;&nbsp;&nbsp;</td></tr>");
		$db->Query("SELECT `shows`.`title`, `subscribedShows`.`subShowSeason` FROM `subscribedShows` JOIN `shows` ON `shows`.`sid` = `subscribedShows`.`subShowTitleId`",
			    array());
		if ($db->getNumRows() == 0) {
                        echo("<tr><td align=center colspan=2>");
                        echo("<br>&nbsp;&nbsp;&nbsp;&nbsp;No subscribed shows at the moment.&nbsp;&nbsp;&nbsp;&nbsp;<br><br></td></tr></table>");
                } else {
			while ($row = $db->getRow()) {
				echo("<tr><td>".$row['title']."</td><td>".$row['subShowSeason']."</td></tr>");
			}
			echo("</table>");
		}
		if ($_SESSION['perms'] >= 10) {
			echo("<br><br><div width=100%><font face='courier'>");
			echo(nl2br(`tail -n 20 /tmp/encode_log`));
			echo("</font></div>");
		}
		break;
	case "iplog":
		$db->Query("SELECT `iplog`.`ip`, `iplog`.`timestamp`, `iplog`.`fail` FROM `iplog` WHERE uid = '%0' ORDER BY logid DESC", array($_SESSION['uid']));
		echo("<table><tr><td><b>Date</b></td><td align=center><b>IP &nbsp;(<font color=red>failed</font>)</b></td></tr>");
		while ($row = $db->getRow()) {
			echo("<tr><td>".date('F j, Y, g:i a', $row['timestamp'])."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=right>");
			if ($row['fail'] == 1) {
				echo("<font color=red><b>".$row['ip']."</b></font>");
			} else {
				echo($row['ip']);
			}
			echo("</td></tr>");
		}
		echo("</table>");
		break;
	case "mod_enc_add":
#		if (round(disk_free_space($vr)/1024/1024/1024) <= 50) {
#			echo("Disk is getting too full, bug the admin");
#			exit;
#		}
		$db->Query("SELECT `users`.`homepath` FROM `users` WHERE `users`.`uid` = '%0' LIMIT 1", array($_SESSION['uid']));
		if ($db->getNumRows() == 0) {
			echo("Dun goofed");
			exit;
		}
		$row = $db->getRow();
		if ($row['homepath'] == "") {
			echo("You are not setup for encoding, please contact the admin.");
			exit;
		} else {
			$dir = $row['homepath'];
		}
		if ($_POST['submit']) {
			if ($_POST['enc_te'] == 1) {
				$encodeTitle = $_POST['enc_title'];
			} elseif ($_POST['enc_te'] == 2) {
				$encodeTitle = $_POST['enc_title_new'];
			} else {
				echo("Error, try again.");
				exit;
			}
			if (($_POST['enc_type'] < -1) || ($_POST['enc_type'] > 1)) {
				echo("Error, try again.");
				exit;
			} else {
				if ($_POST['enc_type'] == 1) {
					$encodeSnum = $_POST['enc_sn'];
				} else {
					$encodeSnum = $_POST['enc_type'];
				}
			}
			$encodePath = $dir.$_POST['enc_folder']."/";
			$db->Query("INSERT INTO `encode_jobs` (`jid`, `show`, `snum`, `filepath`, `tmp_description`, `curfile`, `priority`, `completed`) 
				    VALUES (null, '%0', '%1', '%2', '%3', '', '1', '0')",
				    array($encodeTitle, $encodeSnum, $encodePath, $_POST['enc_description']));
			echo("Added to the queue<br>Title: ".stripslashes($encodeTitle)."<br>Path: ".stripslashes($encodePath)."<br><br>Files to be encoded:<br>");
			foreach (getFileList($encodePath) as $filePath) {
				echo(end(preg_split("/\//", $filePath))."<br>");
			}
			exit;
		} else {
?>
		<h2 id="sex">Encode Cue Addition</h2>
		<form action='javascript: loadFiles("mod_enc_proc", 0);' id='enc_add' name='enc_add' method='POST'>
		<fieldset><legend><span>Title</span></legend>
		<ol><li>
		<label for='enc_te1'>
		<input type='radio' class='radio' name='enc_te' id='enc_te1' value='1' onClick="javascript: document.getElementById('enc_description').disabled=true;">
		Existing:</label><select name='enc_title' id='enc_title'><?php
			$db->Query("SELECT `title` FROM `shows` ORDER BY `title`", array());
			while ($row = $db->getRow()) {
				echo("<option>".$row['title']."</option>");
			}
		?></select>
		</li>
		<li>
		<label for='enc_te2'>
		<input type=radio name="enc_te" class='radio' id="enc_te2" value="2" onClick="javascript: document.getElementById('enc_description').disabled=false;">
		New:</label> <input type='text' size='40' name='enc_title_new' id='enc_title_new'>
		</li>
		<li>
		<label for='enc_description'>Description:</label> <input type='text' size='40' name='enc_description' id='enc_description' disabled="disabled">
		</li>
		</ol>
		</fieldset>
		<fieldset><legend><span>Type</span></legend>
		<ol>
		<li>
		<label for='enc_type1'><input type='radio' class='radio' name='enc_type' id='enc_type1' value='-1'> Movie</label>
		</li>
		<li>
		<label for='enc_type2'><input type='radio' class='radio' name='enc_type' id='enc_type2' value='0'> Special</label>
		</li>
		<li>
		<label for='enc_type3'><input type='radio' class='radio' name='enc_type' id='enc_type3' value='1'> Season:</label>
		<input type=text size='3' id='enc_sn' name='enc_sn'>
		</li>
		</ol>
		</fieldset>
		<fieldset><legend><span>Location</span></legend>
		<ol>
		<li><label for='enc_folder'>Folder:</label> <select name='enc_folder' id='enc_folder'><?php
			if ($dirs = scandir($dir)) {
				foreach ($dirs as $file) {
					if (!($file == '.') && !($file == '..') && (is_dir($dir.'/'.$file))) {
						echo("<option>".$file."</option>");
					}
				}
			}
		?></select>
		</li>
		</ol>
		</fieldset><fieldset class='submit'>
		<input type='submit' name='submit' id='submit' value='Encode!'>
		</fieldset></form>
		<?php
		}
		break;
	case "mod_edit_priority_change":
		if ($_POST) {
			foreach ($_POST as $jobId => $priority) {
				$db->Query("UPDATE `encode_jobs` SET `encode_jobs`.`priority` = '%0' WHERE `encode_jobs`.`jid` = '%1' LIMIT 1",
				array($priority, str_replace("jid", "", $jobId)));
			}
			echo("Prioritys Updated, <a href='#queue' onClick='javascript: loadFiles(\"queue\", 0);'>back</a> to the queue");
		} else {
			echo("uhhh...........");
		}
		break;
	case "mod_ips":
		$db->Query("SELECT `users`.`username`, `iplog`.`ip`, `iplog`.`timestamp` FROM `users`, `iplog` WHERE (`iplog`.`fail` = '1') AND (`users`.`uid` = `iplog`.`uid`) ORDER BY `logid` DESC", array());
		echo("<table><tr><td><b>Date</b></td><td><b>IP</b></td><td><b>Username</b></td></tr>");
		while ($row = $db->getRow()) {
			echo("<tr><td>" . date('F j, Y, g:i a', $row['timestamp']));
			echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=right>");
			echo($row['ip'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>");
			echo($row['username'] . "</td></tr>");
		}
		echo("</table>");
		break;
	default:
		$db->Query("SELECT `sid`, `title`, `description` FROM `shows` ORDER BY `shows`.`title` ASC", array());
		if ($db->getNumRows() == 0) {
			echo("No shows");
			exit;
		}
		echo("<h2>All the Shows!</h2><b>It's all we have to offer right now!</b><hr>");
		while ($row = $db->getRow()) {
			echo "<a href='#show/".$row['sid']."' onClick=\"javascript: loadFiles('show', ".$row['sid'].");\"><b>".$row['title']."</b></a> - ".$row['description']."<br>";
		}
}
?>
