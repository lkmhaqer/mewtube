<?php
include("../dbinfo.php");
$connect = mysql_connect($sqlhost, $sqluser, $sqlpass);
mysql_select_db($sqldb);
$query = "SELECT * FROM newtube.episodes ORDER BY eid DESC";
$results = mysql_query($query);
while ($row = mysql_fetch_assoc($results)) {
	$tfn = substr($row['filename'], 0, -4) . ".gif";
	if (!file_exists("/srv/mewtube/thumbs/" . $tfn)) {	
		echo "Generating new image (" . $tfn . ")...\n";
		$gif = new ffmpeg_animated_gif("thumbs/" . $tfn, 200, 140, 1, 0);
		echo "Done.\nOpening movie...\n";
		$mov = new ffmpeg_movie("/srv/svs/" . $row['filename'], false);
		for ($i=10000;$i<=20000;$i=$i+1200) {
			if ($i <= $mov->getFrameCount()) {
				$gif->addFrame($mov->getFrame($i));
				echo "Frame added\n";
			} else {
				echo "Out of Frames :(\n";
			}
		}
		echo "Thumbnail Generated for ".$row['filename']."!\n";
	} else {
		echo $tfn . " seems to exist already, skipping\n";
	}
}
?>
