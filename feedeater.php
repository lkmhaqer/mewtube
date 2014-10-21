<?php
require("dbinfo.php");
require("ripcord.php");
require("BDencode.php");
require("BEncode.php");
$torrentDir = "/mnt/cdj1000/torrent/";
$expandDir = "/home/roor/tmp/";
$rssFeed = "http://rss.torrentleech.org/784e2b30ba0c3b8a0c68";
$localRSS = "/tmp/torrentLeech.rss";
$xmlURL = "http://table.cvn/RPC2";
$pidFile = "/tmp/feedeater.pid";
$tickFile = "/tmp/feedearer_tick";
$sourceType = "xvid";
$client = ripcord::xmlrpcClient($xmlURL);
$completeTorrents = $client->download_list('complete');
$leadingRSSURLLength = strlen("http://www.torrentleech.org/rss/download/292028/784e2b30ba0c3b8a0c68/");

//lookout below
if (file_exists($pidFile)) {
        $f = fopen($pidFile, 'r');
        flock($f, LOCK_SH);
        $pid = trim(fgets($f));
        if (posix_getsid($pid)) {
                exec("echo '" . date(DATE_RFC822) . " " . $pid . " appears to be transcoding.' >> ".$tickFile);
                exit;
        } else {
                echo "$pid doesn't appear to be running oh noes! Re-starting...\n";
        }
        fclose($f);
}
$pid = posix_getpid();
$f = fopen($pidFile, 'w');
flock($f, LOCK_EX);
fwrite($f, $pid . "\n");
fclose($f);
echo("###### Script Started with $pid ######\n");
echo(date(DATE_RFC822)."\n");

if (is_file($localRSS)) {
	if ((time()-900) >= filemtime($localRSS)) {
		echo("RSS feed is older than 15 minutes, grabbin a new one.\n");
		echo(exec("wget -O '".$localRSS."' '".$rssFeed."'"));
	} else {
		echo("RSS feed is fresh in my mind.\n");
	}
} else {
	echo("RSS feed doesn't exist, grabbin a new one.\n");
	echo(exec("wget -O '".$localRSS."' '".$rssFeed."'"));
}
$db = new db();
$db->Query("SELECT `torrents`.`torrentFile`, `torrents`.`torrentInfoHash` FROM `torrents`", array());
$torrentFiles = array();
$torrentHashes = array();
while ($row = $db->getRow()) {
	array_push($torrentFiles, $row['torrentFile']);
	array_push($torrentHashes, $row['torrentInfoHash']);
}
$subShowResults = $db->Query("SELECT `subscribedShows`.`subShowID`, `subscribedShows`.`subShowSeason`, `shows`.`title` FROM `subscribedShows` JOIN `shows` ON `shows`.`sid` = `subscribedShows`.`subShowTitleID` ORDER BY `subscribedShows`.`subShowID`", array());
$doc = new DOMDocument();
$doc->load($localRSS);
echo("Looking up RSS Feeds...\n");
while ($row = mysql_fetch_assoc($subShowResults)) {
	$searchString = $row['title']." S".$row['subShowSeason'];
	echo("Searching for: ".$searchString."\n");
	foreach ($doc->getElementsByTagName('item') as $node) {
		$torrentTitle = $node->getElementsByTagName('title')->item(0)->nodeValue;
		if (preg_match("/".$row['title']."/i", $torrentTitle) && preg_match("/S".$row['subShowSeason']."/i", $torrentTitle)) {
			echo("Found: ".$node->getElementsByTagName('title')->item(0)->nodeValue."\n");
			$torrent = substr($node->getElementsByTagName('link')->item(0)->nodeValue, $leadingRSSURLLength);
			echo(" file: ".$torrent."\n");
			if (preg_match("/".$sourceType."/i", $torrentTitle)) {
				if (array_search($torrent, $torrentFiles) === FALSE) {
					exec("wget -P \"".$torrentDir."\" \"".$node->getElementsByTagName('link')->item(0)->nodeValue."\"");
					$tfile = BDecode(file_get_contents($torrentDir.$torrent));
					$infoHash = strtoupper(sha1(BEncode($tfile['info'])));
					$db->Query("INSERT INTO `torrents` (`torrentID`, `subShowID`, `torrentTitle`, `torrentFile`,
						   `torrentStage`, `torrentInfoHash`, `torrentEncodeID`)
						   VALUES (null, '%0', '%1', '%2', '0', '%3', '0')",
						   array($row['subShowID'], $node->getElementsByTagName('title')->item(0)->nodeValue, $torrent, $infoHash));
					echo(" Added to DB and downloaded");
				} else {
					echo(" Found in the DB");
				}
				echo("\n");
			} else {
				echo(" Wrong Source Type.\n");
			}
		}
	}
}
echo("Searching for completed torrents...\n");
foreach ($torrentHashes as $infoHash) {
	if (array_search($infoHash, $completeTorrents) !== FALSE) {
		echo("Torrent with hash: \"".$infoHash."\" Completed!\n");
		$torrentFolder = str_replace("/media/shit/", "/mnt/cdj1000/", $client->d->get_directory($infoHash));
		echo("Torrent Folder: ".$torrentFolder."\n");
		$expandHashDir = $expandDir.$infoHash."/";
		$db->Query("SELECT `torrents`.`torrentID`, `torrents`.`torrentStage`, `subscribedShows`.`subShowSeason`, `shows`.`title` FROM `torrents`
                            JOIN `subscribedShows` ON `subscribedShows`.`subShowID` = `torrents`.`subShowID`
			    JOIN `shows` ON `shows`.`sid` = `subscribedShows`.`subShowTitleID`
			    WHERE `torrents`.`torrentInfoHash` = '%0' LIMIT 1", array($infoHash));
		if ($db->getNumRows() == 0) {
			echo("What the fuck?\n");
		} else {
			$row = $db->getRow();
			if (count(getFileList($expandHashDir)) == 0 && $row['torrentStage'] < 2) {
				echo("No vids found, trying to extract.\n");
				if (is_dir($expandHashDir) === FALSE) {
					mkdir($expandHashDir);
				}
				if (glob($torrentFolder."/*part01.rar")) {
					$rarExt = "*part01.rar";
				} elseif (glob($torrentFolder."/*.rar")) {
					$rarExt = "*.rar";
				}
				$rarCmd = "unrar x -o- ".$torrentFolder."/".$rarExt." ".$expandHashDir;
				echo("Trying: ".$rarCmd."\n");
				echo(exec($rarCmd)."\n");
				if (count(getFileList($expandHashDir)) != 0) {
					echo("Torrent found with ID: ".$row['torrentID']."\n");
					if ($row['torrentStage'] == 0) {
						$db->Query("UPDATE `torrents` SET  `torrentStage` =  '1' WHERE  `torrents`.`torrentID` = '%0' LIMIT 1", array($row['torrentID']));
						echo("Torrent updated to stage 1.\n");
						echo("Torrent is for show: ".$row['title']." S".$row['subShowSeason']."\n");
						$db->Query("INSERT INTO `encode_jobs` (`jid`, `show`, `snum`, `filepath`, `tmp_description`, `curfile`, `priority`, `completed`)
							    VALUES (null, '%0', '%1', '%2', '', '', '10', '0')", array($row['title'], ltrim($row['subShowSeason'], "0"), $expandHashDir));
						$encodeID = $db->getInsertId();
						$db->Query("UPDATE `torrents` SET `torrentStage` =  '1', `torrentEncodeID` = '%0' WHERE  `torrents`.`torrentID` = '%1' LIMIT 1",
							    array($encodeID, $row['torrentID']));
						echo("Torrent added to encode queue.\n");
					} else {
						echo("Torrent already at stage ".$row['torrentStage']."\n");
					}
				}
			} elseif ($row['torrentStage'] < 2) {
				echo("Vids found, Checking for encoded torrents.\n");
				$db->Query("SELECT `torrents`.`torrentEncodeID`, `torrents`.`torrentStage` FROM `torrents`
					    WHERE `torrents`.`torrentInfoHash` = '%0' LIMIT 1", array($infoHash));
				$row = $db->getRow();
				$encodeID = $row['torrentEncodeID'];
				$torrentStage = $row['torrentStage'];
				if ($encodeID >= 1) {
					$db->Query("SELECT `encode_jobs`.`completed` FROM `encode_jobs` WHERE `encode_jobs`.`jid` = '%0' LIMIT 1", array($encodeID));
					$row = $db->getRow();
					if ($row['completed'] == 1 && $torrentStage < 2) {
						echo("Encoding job ".$encodeID." was completed, removing dir.\n");
						echo(exec("rm -rfv ".$expandHashDir)."\n");
						$db->Query("UPDATE `torrents` SET `torrentStage` = '2' WHERE `torrents`.`torrentInfoHash` = '%0' LIMIT 1", array($infoHash));
					} else {
						echo("Encoding Job ".$encodeID." doesn't appear to be done, leaving files.\n");
					}
				} else {
					echo("No encoding job stated, what the fuck?\n");
				}
			} else {
				echo("Torrent already 100% complete.\n");
			}
		}
	}
}
unlink($pidFile);
echo("Done?\n");
?>
