<?php

// You could add some numbers to this if you want it to be secure, as it is, it's kinda a joke.

ob_start();
session_start();
$length = 2;
$width = ($length * 126);
$height = 50;
$im = ImageCreate($width, $height);
$numlinesbg = 40;
$numshapes = 40;
$chars = array('inet', 'rsvp', 'mpls', 'http', 'smtp', 'ircd', 'dhcp', 'nntp', 'icmp', 'arpa', 'ajax', 'bsod', 'beep', 'chap', 'cpan', 'cdrw', 'dimm', 'adsl', 'dram', 'xvid', 'ddos', 'dbms', 'dmca', 'eula', 'html', 'isdn', 'jpeg', 'lilo', 'grub', 'ldap', 'mime', 'motd', 'pata', 'pptp', 'perl', 'igrp', 'ebgp', 'ibgp', 'risc', 'sftp', 'sata', 'ssid', 'sshd', 'snmp', 'sftp', 'svga', 'ttyl', 'voip', 'vlan', 'wwan', 'wlan', 'xmms');
$cs = '';
$x = mt_rand(5, 10);
$y = mt_rand(34, 38);
$fonts = glob("fonts/*.ttf");

for ($i=1;$i<=$numshapes;$i++) {
	$rc1 = mt_rand(170, 220);
        $rc2 = mt_rand(170, 220);
        $rc3 = mt_rand(170, 220);
        $lc = ImageColorAllocate($im, $rc1, $rc2, $rc3);
        if (mt_rand(0,9) >= 5) {
                imagefilledrectangle($im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lc);
        } else {
                imagefilledellipse($im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lc);
        }
}

for ($i=1;$i<=$numlinesbg;$i++) {
	$rc1 = mt_rand(170, 220);
	$rc2 = mt_rand(170, 220);
	$rc3 = mt_rand(170, 220);
	$lc = ImageColorAllocate($im, $rc1, $rc2, $rc3);
	imageline($im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lc);
	if ($i<($length+1))
		$co[$i] = ImageColorAllocate($im, rand(0, 100), rand(0, 100), rand(0, 100));
}

for ($i=1;$i<=$length;$i++) {
	$str[$i] = $chars[rand(0, count($chars)-1)];
	ImageTTFText(   $im, 
			mt_rand(20, 24),
			mt_rand(-7, 7),
			$x,
			$y,
			$co[$i],
			$fonts[rand(0, count($fonts)-1)],
			$str[$i] ); 
	$x = $x+mt_rand(120, 130);
	$y = mt_rand(34, 38);
	$cs .= $str[$i] . ' ';
}

$_SESSION['lolcode'] = trim($cs);

header("Content-Type: image/png");
ImagePNG($im);
ob_end_flush();
ImageDestroy($im);

?>
