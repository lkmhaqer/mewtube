<?php
include("dbinfo.php");
$connect = mysql_connect($sqlhost, $sqluser, $sqlpass);
mysql_select_db($sqldb);
$query = "SELECT * FROM ewwtube.episodes ORDER BY eid DESC";
$results = mysql_query($query);
while ($row = mysql_fetch_assoc($results)) {
	$tfn = substr($row['filename'], 0, -4) . ".jpg";
	$tfnhq = substr($row['filename'], 0, -4) . "-hq.jpg";
	if (!file_exists("/srv/ewwtube/thumbs/preview-" . $tfn)) {	
		echo "Generating new image (preview-" . $tfn . ")...\n";
		exec("ffmpeg -i '/srv/svs/" .substr($row['filename'], 0, -4).".flv' -y -f image2 -ss 60 -t 0.001 -s 480x320 '/srv/ewwtube/thumbs/preview-" . $tfn . "'");
		echo "Thumbnail Generated for ".$row['filename']."!\n";
	} else {
		echo $tfn . " seems to exist already, skipping\n";
	}
	if (!file_exists("/srv/ewwtube/thumbs/preview-".$tfnhq)) {
		echo "Generating new image (preview-" . $tfnhq . ")...\n";
		exec("ffmpeg -i '/srv/svs/" .substr($row['filename'], 0, -4)."-hq.flv' -y -f image2 -ss 60 -t 0.001 -s 640x368 '/srv/ewwtube/thumbs/preview-" . $tfnhq . "'");
		echo "HQ Thumbnail Generated for ".$row['filename']."!\n";
	} else {
		echo $tfnhq . " seems to exist already, skipping\n";
	}
}
$query = "SELECT * FROM ewwtube.movies";
$results = mysql_query($query);
while ($row = mysql_fetch_assoc($results)) {
        $tfn = substr($row['filename'], 0, -4) . ".jpg";
        $tfnhq = substr($row['filename'], 0, -4) . "-hq.jpg";
        if (!file_exists("/srv/ewwtube/thumbs/preview-" . $tfn)) {
                echo "Generating new image (preview-" . $tfn . ")...\n";
                exec("ffmpeg -i '/srv/svs/" .substr($row['filename'], 0, -4).".flv' -y -f image2 -ss 120 -t 0.001 -s 480x320 '/srv/ewwtube/thumbs/preview-" . $tfn . "'");
                echo "Thumbnail Generated for ".$row['filename']."!\n";
        } else {
                echo $tfn . " seems to exist already, skipping\n";
        }
        if (!file_exists("/srv/ewwtube/thumbs/preview-".$tfnhq)) {
                echo "Generating new image (preview-" . $tfnhq . ")...\n";
                exec("ffmpeg -i '/srv/svs/" .substr($row['filename'], 0, -4)."-hq.flv' -y -f image2 -ss 120 -t 0.001 -s 640x368 '/srv/ewwtube/thumbs/preview-" . $tfnhq . "'");
                echo "HQ Thumbnail Generated for ".$row['filename']."!\n";
        } else {
                echo $tfnhq . " seems to exist already, skipping\n";
        }
}
?>
