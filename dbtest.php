<?php
require("dbinfo.php");
$db = new db();
echo("Starting to generate ".$argv[1]." vidid records...\n");
for ($i=$argv[1];$i>=0;$i--) {
	$db->Query("INSERT INTO `vidlog` (`vidlogid`, `uid`, `vidid`, `quality`, `timestamp`) VALUES (null, '%0', '%1', '%2', '%3')",
		    array('1', mt_rand(900, 4300), '1', time()));
	echo("[".$i."]");
}
echo("\nDone!\n");
?>
