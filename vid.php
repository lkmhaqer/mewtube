<?php
session_start();
if ($_SESSION['pass'] == '69696969') {
	$secureKey = "7iURlaPIuwRlupl6";
	$secureURI = "/video/";
	$tv        = "/cops.s3e15.flv";
	$t_hex     = sprintf("%08x", time());
	include("dbinfo.php");
	$connect = mysql_connect($sqlhost, $sqluser, $sqlpass);
	if (!$connect) {
		echo "DB issues";
		exit;
	}
	mysql_select_db($sqldb);
        if ($_GET['id'] == "Random") {
                $query = "SELECT * FROM `ewwtube`.`episodes` ORDER BY RAND() LIMIT 1";
                $row = mysql_fetch_assoc(mysql_query($query));
                $tv = "/" . $row['filename'];
                $m = md5($secureKey . $tv . $t_hex);
                printf('%s%s/%s%s', $secureURI, $m, $t_hex, $tv, $tv);
                $svstr = substr(mysql_real_escape_string($row['filename']), 0, -4);
                $query = "UPDATE `ewwtube`.`users` SET `lastvid` = '" . $svstr . "' WHERE `users`.`username` = '" . $_SESSION['user'] . "' LIMIT 1;";
                $result = mysql_query($query);
	} elseif ($_GET['id'] == "Random-HQ") {
                $query = "SELECT * FROM `ewwtube`.`episodes` ORDER BY RAND() LIMIT 1";
                $row = mysql_fetch_assoc(mysql_query($query));
                $tv = "/" . substr($row['filename'], 0, -4) . "-hq.flv";
                $m = md5($secureKey . $tv . $t_hex);
                printf('%s%s/%s%s', $secureURI, $m, $t_hex, $tv, $tv);
                $svstr = substr(mysql_real_escape_string($row['filename']), 0, -4);
                $query = "UPDATE `ewwtube`.`users` SET `lastvid` = '" . $svstr . "' WHERE `users`.`username` = '" . $_SESSION['user'] . "' LIMIT 1;";
                $result = mysql_query($query);
	} elseif ($_GET['id'] == "Random-mov") {
		$query = "SELECT * FROM `ewwtube`.`movies` ORDER BY RAND() LIMIT 1";
		$row = mysql_fetch_assoc(mysql_query($query));
		$tv = "/" . $row['filename'];
                $m = md5($secureKey . $tv . $t_hex);
		printf('%s%s/%s%s', $secureURI, $m, $t_hex, $tv, $tv);
                $svstr = substr(mysql_real_escape_string($row['filename']), 0, -4);
                $query = "UPDATE `ewwtube`.`users` SET `lastvid` = '" . $svstr . "' WHERE `users`.`username` = '" . $_SESSION['user'] . "' LIMIT 1;";
                $result = mysql_query($query);
	} elseif ($_GET['id'] == "Random-mov-HQ") {
                $query = "SELECT * FROM `ewwtube`.`movies` ORDER BY RAND() LIMIT 1";
                $row = mysql_fetch_assoc(mysql_query($query));
                $tv = "/" . substr($row['filename'], 0, -4) . "-hq.flv";
                $m = md5($secureKey . $tv . $t_hex);
                printf('%s%s/%s%s', $secureURI, $m, $t_hex, $tv, $tv);
                $svstr = substr(mysql_real_escape_string($row['filename']), 0, -4);
                $query = "UPDATE `ewwtube`.`users` SET `lastvid` = '" . $svstr . "' WHERE `users`.`username` = '" . $_SESSION['user'] . "' LIMIT 1;";
                $result = mysql_query($query);
        } elseif (preg_match('/^[a-zA-Z0-9\s\,\(\)\.\-\_\!]*$/i', $_GET['id'])) {
                $tv = "/" . $_GET['id'];
                $m  = md5($secureKey . $tv . $t_hex);
                printf('%s%s/%s%s', $secureURI, $m, $t_hex, $tv, $tv);
                $svstr = substr(mysql_real_escape_string($_GET['id']), 0, -4);
                $query = "UPDATE `ewwtube`.`users` SET `lastvid` = '" . $svstr . "' WHERE `users`.`username` = '" . $_SESSION['user'] . "' LIMIT 1;";
                $result = mysql_query($query);
                if (!$result) {
                        echo "DB issues: " . mysql_error();
                        exit;
                }
        } else {
                echo "invalid video";
                exit;
        }
} else {
	echo "420";
	exit;
}
?>
