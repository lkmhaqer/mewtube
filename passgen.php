<?php
$psk = "633582aaa91159d0c6450daf165088cd";
if (isset($_GET['s'])) {
	echo md5($_GET['s'].$psk);
}
?>
