<?php
echo($_SERVER['REMOTE_ADDR']);
unset($_GET['a']);
foreach($_GET as $rofl => $lol) {
	echo($rofl." => ".$lol."<br>");
}
$vidURL = array( 
		"lol.flv"
		);

$defaultSize = '650x368';

$vidSize = array(
		);

function getDim($id) {
	global $vidSize, $defaultSize;
	if ($vidSize[$id]) {
                $dim = preg_split('/x/', $vidSize[$id]);
        } else {
                $dim = preg_split('/x/', $defaultSize);
        }
	return $dim;
}

?>
<html><head><script type='text/javascript' src='vid.js'></script>
<script type='text/javascript'>
<?php
foreach ($vidURL as $k => $vid) {
	$i = $k+1;
	$size = getDim($k);
?>
var s<?php echo($i); ?> = new SWFObject('vid.swf','mewplayer','<?php echo($size[0]); ?>','<?php echo($size[1]) ?>','9');
s<?php echo($i); ?>.addParam('allowfullscreen','true');
s<?php echo($i); ?>.addParam('allowscriptaccess','always');
s<?php echo($i); ?>.addParam('flashvars','file=/<?php echo($vid); ?>&id=mewplayer&streamer=lighttpd&controlbar=over&backcolor=000000&frontcolor=C0C0C0');
<?php
}
?>
</script>
</head>
<body vlink='blue'>
<?php
foreach ($vidURL as $k => $vid) {
	$i = $k+1;
	$size = getDim($k);
	echo("(".$size[0]."x".$size[1].", ".round((filesize($vid)/1024/1024), 1)."MB) <a href='#vid".$i."'>".$vid."</a><br>\n");
}

foreach ($vidURL as $k => $vid) {
	$i = $k+1;
	$size = getDim($k);
	if ($k > 0) {
		echo("<hr width='650' align='left'>");
	}
?><a name='vid<?php echo($i); ?>'><h3><?php echo($i); ?>.</a> <?php echo($vid); ?> - <?php echo($size[0]."x".$size[1]." | ".round((filesize($vid)/1024/1024), 1)); ?>MB <a href='#'>top</a></h3>
<div id='vidcel<?php echo($i); ?>'>
<img src="/mewtube.png">
</div>
<script type='text/javascript'>
s<?php echo($i); ?>.write('vidcel<?php echo($i); ?>');
</script>
<?php
}
?>
</body>
</html>
