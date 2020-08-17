<?php 
/**
 * This file should be softly linked (ln -s) to /var/www/html/therhizomes/s.php,
 * and so does the "include" file.
 */
include "app/vendors/extrakits.inc.php";
$s = _short2realUrl();
if (!in_array($s, array(1, 2, 3, 4))) {
	echo $s;
}
?>