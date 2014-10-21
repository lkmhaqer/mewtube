<?php

$psk = "87aa7cdea5ef619d4ff0b4241a1d6cb02379f4e2ce4ec2787ad0b30545e17cdefaea9ea9076ede7f4af152e8b2fa9cb6";

function hashPass($pass, $sekkrit) {
	global $psk;
	return hash_hmac('sha512', $pass . $sekkrit, $psk);
}

if ($_GET['pass']) {
	if ($_GET['salt']) {
		$sec = $_GET['salt'];
	} else {
		$sec = mt_rand();
	}
	echo "salt: ".$sec." | hash: ".hashPass($_GET['pass'], $sec);
}
