<?php
require("/srv/mewtube/dbinfo.php");
$db = new db();

function encodeFile($filePath, $jobID) {
	global $db, $workDir, $contentDir, $thumbDir, $hqBitRate, $lqBitRate, $usc;
	$fileName = str_replace($usc, "", end(preg_split("/\//", $filePath)));
	$fileNoExt = substr($fileName, 0, -4);
	$fileFLV = $fileNoExt.".flv";
	$fileHQFLV = $fileNoExt."-hq.flv";
	$db->Query("UPDATE `encode_jobs` SET `encode_jobs`.`curfile` = '%0' WHERE `encode_jobs`.`jid` = '%1' LIMIT 1", array($fileNoExt, $jobID));
	echo(exec("cp -rfv '".$filePath."' '".$workDir.$fileName."'"));
	echo("\nEncoding HQ FLV (1st Pass)\n");
	exec("ffmpeg -i '".$workDir.$fileName."' -y -an -pass 1 -b ".$hqBitRate." -s 640x368 '".$workDir.$fileHQFLV."'");
        echo("\nEncoding HQ FLV (2nd Pass)\n");
        exec("ffmpeg -i '".$workDir.$fileName."' -y -pass 2 -b ".$hqBitRate." -s 640x368 -ac 2 -acodec libmp3lame -ab 128k -ar 44100 '".$workDir.$fileHQFLV."'");
	echo("\nAdding HQ Metadata\n");
	exec("flvtool2 -U '".$workDir.$fileHQFLV."'");
	echo(exec("mv -fv '".$workDir.$fileHQFLV."' '".$contentDir.$fileHQFLV."'"));
	echo("\nEncoding LQ FLV (1st Pass)\n");
	exec("ffmpeg -i '".$workDir.$fileName."' -y -an -pass 1 -b ".$lqBitRate." -s 480x320 '".$workDir.$fileFLV."'");
        echo("\nEncoding LQ FLV (2nd Pass)\n");
        exec("ffmpeg -i '".$workDir.$fileName."' -y -pass 2 -b ".$lqBitRate." -s 480x320 -ac 2 -acodec libmp3lame -ab 128k -ar 44100 '".$workDir.$fileFLV."'");
	echo("\nAdding LQ Metadata\n");
	exec("flvtool2 -U '".$workDir.$fileFLV."'");
	echo(exec("mv -fv '".$workDir.$fileFLV."' '".$contentDir.$fileFLV."'"));
	exec("rm '".$workDir.$fileName."'");
	echo("\nThumbnail Time\n");
	exec("ffmpeg -ss 120 -i '".$contentDir.$fileHQFLV."' -an -f image2 -vcodec mjpeg -y -vframes 1 -s 150x86 '".$thumbDir."still-".$fileNoExt.".jpg'");
	exec("ffmpeg -ss 120 -i '".$contentDir.$fileHQFLV."' -an -f image2 -vcodec mjpeg -y -vframes 1 -s 640x368 '".$thumbDir."preview-".$fileNoExt."-hq.jpg'");
	exec("ffmpeg -ss 120 -i '".$contentDir.$fileFLV."' -an -f image2 -vcodec mjpeg -y -vframes 1 -s 480x320 '".$thumbDir."preview-".$fileNoExt.".jpg'");
	if (!file_exists($contentDir.$fileFLV) || !file_exists($contentDir.$fileHQFLV)) {
		echo("Error: encoder failed :(\n");
		exit;
	}
	echo("\nDone with ffmpeg!\n");
}

if (file_exists($pidFile)) {
        $f = fopen($pidFile, 'r');
        flock($f, LOCK_SH);
        $pid = trim(fgets($f));
        if (posix_getsid($pid)) {
                exec("echo '" . date(DATE_RFC822) . " " . $pid . " appears to be transcoding.' >> /tmp/encode_tick");
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
echo "###### Script Started with $pid ######\n";
$db->Query("SELECT `encode_jobs`.`jid`, `encode_jobs`.`show`, `encode_jobs`.`snum`, `encode_jobs`.`filepath`, `encode_jobs`.`tmp_description`
	    FROM `encode_jobs` WHERE `encode_jobs`.`completed` = '0' ORDER BY `encode_jobs`.`priority` DESC, `encode_jobs`.`jid` ASC LIMIT 1", array());
if ($db->getNumRows() == 0) {
	echo("No encoding jobs found, exiting.\n");
	unlink($pidFile);
	exit;
}
$encodeRow = $db->getRow();
echo("encoding jobID: ".$encodeRow['jid']."\n");
foreach (getFileList($encodeRow['filepath']) as $filePath) {
        $fileName = end(preg_split("/\//", $filePath));
        $fileSafeName = str_replace($usc, "", $fileName);
        $fileNoExt = substr($fileSafeName, 0, -4);
	$fileFLV = $fileNoExt.".flv";
	$fileHQFLV = $fileNoExt."-hq.flv";
	if ($encodeRow['snum'] == -1) {
		$db->Query("SELECT `movies`.`mid` FROM `movies` WHERE `movies`.`filename` = '%0' LIMIT 1", array($fileFLV));
	} else {
		$db->Query("SELECT `episodes`.`eid` FROM `episodes` WHERE `episodes`.`filename` = '%0' LIMIT 1", array($fileFLV));
	}
	if ($db->getNumRows() == 0) {
		echo($fileFLV." was not found, adding.\n");
		encodeFile($filePath, $encodeRow['jid']);
		if ($encodeRow['snum'] == -1) {
			$db->Query("INSERT INTO `movies` (mid, title, description, filename) VALUES
				    (null, '%0', '%1', '%2')", array($fileNoExt, $encode_row['tmp_description'], $fileFLV));
			$movieid = $db->getInsertID();
			echo($fileFLV." was added with movieID = ".$movieid."\n");
		} else {
			$db->Query("SELECT `shows`.`sid`, `shows`.`title` FROM `shows` WHERE `shows`.`title` = '%0' LIMIT 1", array($encodeRow['show']));
			if ($db->getNumRows() == 0) {
				echo($encodeRow['show']." was not found, adding.\n");
				$db->Query("INSERT INTO `shows` (sid, title, description) VALUES (null, '%0', '%1')",
					    array($encodeRow['show'], $encodeRow['tmp_description']));
				$sid = $db->getInsertId();
				echo($encodeRow['show']." was created with showID = ".$sid."\n");
			} else {
				$row = $db->getRow();
				$sid = $row['sid'];
				echo($encodeRow['show']." was found with showID = ".$sid."\n");
			}
			$db->Query("SELECT `seasons`.`seasonid` FROM `seasons` WHERE `seasons`.`num` = '%0' AND `seasons`.`sid` = '%1' LIMIT 1",
				    array($encodeRow['snum'], $sid));
			if ($db->getNumRows() == 0) {
				echo("Season ".$encodeRow['snum']." of ".$encodeRow['show']." was not found, adding now.\n");
				$db->Query("INSERT INTO `seasons` (seasonid, sid, num) VALUES (null, '%0', '%1')", array($sid, $encodeRow['snum']));
				$seasonid = $db->getInsertId();
				echo("Season ".$encodeRow['snum']." was added to ".$encodeRow['show']." with seasonID = ".$seasonid."\n");
			} else {
				$row = $db->getRow();
				$seasonid = $row['seasonid'];
				echo("Season ".$encodeRow['snum']." of ".$encodeRow['show']." was found with seasonID = ".$seasonid."\n");
			}
			$db->Query("INSERT INTO `episodes` (eid, name, edescription, sid, seasonid, filename) VALUES (null, '%0', 'no description', '%1', '%2', '%3')",
				    array($fileNoExt, $sid, $seasonid, $fileFLV));
			$episodeID = $db->getInsertId();
			echo("File ".$fileFLV." was added with episodeID = ".$episodeID."\n");
		}
	} else {
		echo($fileFLV." was found in the DB, please remove to encode\n");
	}
}
$db->Query("UPDATE `encode_jobs` SET `encode_jobs`.`completed` = '1' WHERE `encode_jobs`.`jid` = '%0' LIMIT 1", array($encodeRow['jid']));
unlink($pidFile);
echo("Done?\n");
?>
