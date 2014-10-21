<?php
include("auth.php");
$db = new db();
if (!$_SESSION['username']) {
	echo "Session expired, You must log back in.";
	exit;
}
if ($_GET['q'] == 2 || $_GET['q'] == 3) {
	$db->Query("SELECT `movies`.`filename` FROM `movies` WHERE `movies`.`mid` = '%0' LIMIT 1", array($_GET['vid']));
} else {
	$db->Query("SELECT `episodes`.`filename` FROM `episodes` WHERE `episodes`.`eid` = '%0' LIMIT 1", array($_GET['vid']));
}
if ($_GET['q'] == 1 || $_GET['q'] == 3) {
	$quality = "high";
}
if ($db->getNumRows() == 0) {
	echo("Not found...");
	exit;
}
$row = $db->getRow();
if ($quality) {
	$vidURL = $relativeContentDir.substr($row['filename'], 0, -4)."-hq.flv";
} else {
	$vidURL = $relativeContentDir.$row['filename'];
}
?>
<body topmargin="0" leftmargin="0"><script type='text/javascript' src='vid.js'></script>
<center><div id='vidcel2'>LaLaLa</div></center>
<script type='text/javascript'>
	<?php
	if ($quality) {
		echo "var s1 = new SWFObject('vid.swf','mewplayer','640','368','9');\n";
	} else {
		echo "var s1 = new SWFObject('vid.swf','mewplayer','480','320','9');\n";
	}
	?>
	s1.addParam('allowfullscreen','true');
	s1.addParam('allowscriptaccess','always');
	var url = '<?php echo($vidURL); ?>';
	var tni = 'preview-'+url.substring(4).substring(0, url.substring(4).length-4)+'.jpg';
        s1.addParam('flashvars','file='+url+'&id=mewplayer&streamer=lighttpd&image=/thumbs/'+tni+'&controlbar=over&backcolor=000000&frontcolor=C0C0C0');
	s1.write('vidcel2');
</script>
<body>
