<?php 
include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

if (($argc - 1) != 1) {//if there is 1 parameter and it must mean a date like '2010-04-01'
	exit("Only 1 parameter needed like '2010-05-01'.\n");
}

$date = $argv[1];
/*get the abbreviation of the site*/
$abbr = __stats_get_abbr($argv[0]);

$path_parts = pathinfo($argv[0]);
$output = array();
exec("php " . $path_parts["dirname"] . "/" . "bbrcheckfix.php " . $date . " " . $abbr, $output);
echo implode("\n", $output) . "\n";
?>