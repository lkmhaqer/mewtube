<?php
require("dbinfo.php");
session_start();
if ($_GET['a'] == 'logout') {
	$_SESSION = array();
	if (isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-42000, '/');
	session_destroy();
	header('Location: /');
	exit;
}

$pb = new pageBuilder();

if ($_SESSION['uid'] == '' && $_POST['username'] == '') {
	if ($_GET['a'] == 'stat') {
		echo("Session Expired");
		exit;
	} elseif ($_GET['a'] == 'ajlol') {
		echo("<img src='lol.php?salt=".mt_rand()."'>");
		exit;
	} else {
		$pb->doLogin("");
	}
} elseif ($_POST['username'] != '') {
	if ($_POST['password'] != '') {
		$password = md5($_POST['password'].$psk);
		$db = new db();
		if (strtolower($_SESSION['lolcode']) == strtolower($_POST['captcha'])) {
			$results = $db->Query("SELECT `username`, `uid`, `password`, `perms` FROM `users` WHERE username = '%0' LIMIT 1", array($_POST['username']));
			if ($db->getNumRows() == 1) {
				$row = $db->getRow();
				if (($row['password'] == '') && ($row['perms'] > 0)) {
					$_SESSION['uid']	= $row['uid'];
					$_SESSION['username']	= $row['username'];
					$_SESSION['perms']	= $row['perms'];
					$db->Query("UPDATE `users` SET `users`.`password` = '%0' WHERE `users`.`uid` = '%1'", array($password, $row['uid']));
					$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`)
					VALUES (NULL, '%0', '%1', '%2', '%3')", array($row['uid'], $_SERVER['REMOTE_ADDR'], time(), 0));
				} elseif ($row['password'] == $password || $row['perms'] <= 0) {
					if ($row['perms'] > 0) {
						$db->Query("SELECT `vidlog`.`timestamp` FROM `vidlog` WHERE `vidlog`.`uid` = '%0' ORDER BY `vidlog`.`vidlogid` DESC LIMIT 1",
						array($row['uid']));
						$ts = $db->getRow();
						if ($ts['timestamp'] >= (time()-300)) {
							$db->Query("SELECT `iplog`.`ip` FROM `iplog` WHERE `iplog`.`uid` = '%0' AND `iplog`.`fail` = '0'
							ORDER BY `iplog`.`logid` DESC LIMIT 1", array($row['uid']));
							if ($db->getNumRows() > 0) {
								$ip = $db->getRow();
								if ($ip['ip'] != $_SERVER['REMOTE_ADDR']) {
									$db->Query("UPDATE `users` SET `users`.`perms` = '0' WHERE `users`.`uid` = '%0' LIMIT 1", array($row['uid']));
									$pb->doLogin("Account sharing detected, contact the admin.");
								}
							}
						}
						$_SESSION['uid']	= $row['uid'];
						$_SESSION['username']	= $row['username'];
						$_SESSION['perms']	= $row['perms'];
						$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`) 
						VALUES (NULL, '%0', '%1', '%2', '%3')", array($row['uid'], $_SERVER['REMOTE_ADDR'], time(), 0));
					} else {
						$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`) 
						VALUES (NULL, '%0', '%1', '%2', '%3')", array($row['uid'], $_SERVER['REMOTE_ADDR'], time(), 1));
						$pb->doLogin("Inactive User");
					}
				} else {
					$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`) 
					VALUES (NULL, '%0', '%1', '%2', '%3')", array($row['uid'], $_SERVER['REMOTE_ADDR'], time(), 1));
					$pb->doLogin("Wrong User/Pass");
				}
			} else {
				$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`) 
				VALUES (NULL, '%0', '%1', '%2', '%3')", array(0, $_SERVER['REMOTE_ADDR'], time(), 1));
				$pb->doLogin("Wrong User/Pass");
			}
		} else {
			$db->Query("INSERT INTO `iplog` (`logid`, `uid`, `ip`, `timestamp`, `fail`) 
			VALUES (NULL, '%0', '%1', '%2', '%3')", array(0, $_SERVER['REMOTE_ADDR'], time(), 2));
			$pb->doLogin("You a robot bro?");
		}
	} else {
		$pb->doLogin("Password empty dingus");
	}
}
unset($pb, $db);
?>
