<?php
include("dbinfo.php");
$connect = mysql_connect($sqlhost, $sqluser, $sqlpass);
mysql_select_db($sqldb);
$query = "SELECT * FROM ewwtube.episodes ORDER BY eid DESC";
$results = mysql_query($query);
while ($row = mysql_fetch_assoc($results)) {
	$tfn = substr($row['filename'], 0, -4) . ".jpg";
	if (!file_exists("thumbs/still-" . $tfn)) {	
		echo "Generating new image (still-" . $tfn . ")...\n";
		exec("ffmpeg -i '/srv/svs/" .substr($row['filename'], 0, -4)."-hq.flv' -y -f image2 -ss 60 -t 0.001 -s 150x86 'thumbs/still-" . $tfn . "'");
		echo "Thumbnail Generated for ".$row['filename']."!\n";
	} else {
		echo $tfn . " seems to exist already, skipping\n";
	}
}
?>
