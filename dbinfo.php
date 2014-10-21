<?php

// Config
$sitename  = "MewTube";			// This be the site name
$mtv       = "4.2.0";			// don't change this part
$motd      = "Automagic robot complete, send me your show subscription requests. Email sysop@cvncpu.net for details.";	// what could this be?
$pidFile = "/tmp/mkflv.pid";		// PID File for the encoder
$wwwDir = $_SERVER['DOCUMENT_ROOT'];	// WWW Root, change this if your install does not support $_SERVER vars
$contentDir = "/srv/svs/";		// Content Root
$relativeContentDir = "svs/";		// Content relative to the WWW Root (usually a symlink)
$workDir = "/srv/incoming/tmp/";	// Where to keep files while they are being encoded
$thumbDir = "/srv/mewtube/thumbs/";	// Where to put the thumnails (Useful for using one thumbnail dir on multiple sites)
$hqBitRate = "1200k";			// Bitrate of High Quality FLVs
$lqBitRate = "400k";			// Bitrate of Low Quality FLVs
$usc = array("|", "'", "/", "!", "[", "]", "(", ")", "\\", "`");	// Unsafe charactor array for video filenames
$useableExt = array(".ogm", ".avi", ".mpg", ".mkv", ".mpeg");		// Extensions you want to encode (this should match your ffmpeg install
$sqlhost = "localhost";			// MySQL Hostname
$sqluser = "mewtube";			// MySQL User
$sqlpass = "MFxPPa8s8GjmBSHp";		// MySQL Pass
$sqldb   = "mewtube";			// MySQL DB
$psk = "633582aaa91159d0c6450daf165088cd";			// Salt for passwords
$bgs = array("stone.jpg", "faces.jpg", "panda-tile.jpg", "vectar.png", "wallpape.png", "squiggle.gif", "dolphinz.jpg", "wolf.jpg", "gross.png", "bandana.jpg", "bamboo.png", "dollaz.jpg", "hubble.jpg", "orange.jpg", "yarn.jpg", "Undercutbg.jpg", "catbg.jpg", "mo-snuffles.jpg", "wolf-trip.jpg");

// BEWARE SHITTY CODE BELOW

function getFileList($fullPath) {
        global $useableExt;
        $fileList = array();
        foreach ($useableExt as $ext) {
                $fileList = array_merge($fileList, glob($fullPath."*".$ext));
        }
        return $fileList;
}

class db {
	var $connection;
	var $queryCount;
	var $errorMsg;
	var $resultSet;
	public function db() {
		if (!$this->connection = @mysql_connect($GLOBALS['sqlhost'], $GLOBALS['sqluser'], $GLOBALS['sqlpass'])) {
			$this->errorMsg = mysql_error();
			return false;
		}
		if (!@mysql_select_db($GLOBALS['sqldb'], $this->connection)) {
			$this->errorMsg = mysql_error();
			@mysql_close($this->connection);
			return false;
		}
		return true;
	}

	function Query($query, $values) {
		$this->queryCount++;
		if ($values) {
			foreach ($values as $k => $v) {
				$query = str_replace("%".$k, mysql_real_escape_string($v), $query);
			}
		}
		if (!$this->resultSet = @mysql_query($query, $this->connection)) {
			$this->errorMsg = mysql_error();
			return false;
		} 
		return $this->resultSet;
	}

	function getRow() {
		return @mysql_fetch_assoc($this->resultSet);
	}

	function getNumRows() {
		return @mysql_num_rows($this->resultSet);
	}

	function getInsertId() {
		return @mysql_insert_id($this->connection);
	}

}

class pageBuilder {
	var $login_header;
	var $login_bum;
	function pageBuilder() {
		global $bgs;
		$this->login_header	= str_replace("<%LOLRANDOM%>", "/beegees/".$bgs[rand(0, count($bgs)-1)], file_get_contents('includes/header.inc.php'));
		$this->login_bum	= file_get_contents('includes/bum.inc.php');
	}

	function doLogin($msg) {
		global $wwwDir;
		$page = $this->login_header;
		if ($msg != '') {
			$page .= "<span id=errmsg>&nbsp;&nbsp;&nbsp;&nbsp;Error: ".strip_tags($msg)."&nbsp;&nbsp;&nbsp;&nbsp;</span><br><br>";
		}
		if (array_search($wwwDir.'/index.php', get_included_files()) === false) {
			$page .= file_get_contents('includes/ajlogin.inc.php');
		} else {
			$page .= file_get_contents('includes/login.inc.php');
		}
		$page .= $this->login_bum;
		echo($page);
		exit;
		return true;
	}

	function doEpisode($filename, $eid, $name, $edescription) {
		echo("<img src='thumbs/still-".substr($filename, 0, -4).".jpg' align=left>");
                echo("[<a href='#pas/".$eid."' onClick=\"javascript: playlistMod('pas', ".$eid.");\"><b>+P</b></a>] ");
                echo("[<a href='#vid-hq/".$eid."' onClick=\"javascript: setRandomRoll(0);loadVid(".$eid.", 1);\"><b>HQ</b></a>] ");
                echo("<a href='#vid/".$eid."' onClick=\"javascript: loadVid(".$eid.", 0);\">".$name."</a>");
		echo("<br>".$edescription."<br clear=all>&nbsp;<br>");
	}
}
?>
