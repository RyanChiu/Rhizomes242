<?php 
include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

$defined_constatns = get_defined_constants(true);
$user_defined_constants = $defined_constatns['user'];

if (($argc - 1) != 2) {//there has to be 2 parameter and the 1st one must mean a date like '2010-04-01', the 2nd one must be the abbreviation of the site
	exit("2 parameters needed, like '2010-05-01 camsx'.\n");
}

$date = $argv[1];
try {
	new DateTime($date);
} catch (Exception $e) {
	exit("Illegal date format about the param.\n");
}
/*get the abbreviation of the site*/
$abbr = $argv[2];
/*find out the typeids and siteid from db*/
$typeids = array();
$siteid = null;
$zconn = new zmysqlConn;
__stats_get_types_site($typeids, $siteid, $abbr, $zconn->dblink);
if (empty($siteid)) {
	exit(sprintf("The site with abbreviation \"%s\" does not exist.\n", $abbr));
}

$url = "http://joincheckout.com/stats/posts/?a_aid=SamiCole456&start_date=$date&password=PDD54321A";
$retimes = 0;
$toptimes = 2;
$response = file_get_contents($url);
while ($response === false) {
	$retimes++;
	sleep(35);
	$response = file_get_contents($url);
	if ($retimes == $toptimes) break;
}
if ($response === false) {
	$mailinfo =
	__phpmail("agents.maintainer@gmail.com",
		"$abbr (on PDD) STATS GETTING ERROR, REPORT WITH DATE: " 
			. date('Y-m-d H:i:s') . "(retried " . $retimes . " times)",
		"<b>FROM WEB02</b><br><b>--ERROR REPORT</b><br>"
	);
	exit(sprintf("Failed to read stats data of '$abbr'.(%s)(%d times)\n", $mailinfo, $retimes));
}
//echo "var_dump\n";//for debug
//var_dump($response);//for debug
//echo "var_dump\n";exit();//for debug
$response = trim($response);
$lines = split("\n", $response);
$i = 0;
$totals = -1;
$sales = array();
$trxes = array();
foreach ($lines as $line) {
	if ($i == 0) {
		$outlines = split(" ", $line);
		if ($outlines[0] != "SUCCESS") {
			exit("orginal failed message from $abbr: $line\n");
		} else {
			$totals = intval($outlines[2]);
			if ($totals != (count($lines) - 1)) {
				exit(
					"it's weird that total sales in the 1st line is $totals, but actually there are " 
						. (count($lines) - 1) . " lines down there.\n"
				);
			}
		}
	} else {
		$line = trim($line);
		parse_str($line, $sale);
		//echo "($i)\n" . print_r($sale, true) . "\n"; //for debug
		$chs = explode(",", $user_defined_constants[strtoupper($abbr) . '_CHS']);
		if (in_array($sale["ch"], $chs, true)) {
			array_push($sales, $sale);
			array_push($trxes, $sale['transactionid']);
		}
	}
	
	$i++;
}
sort($trxes);
//exit(print_r($sales, true) . "\n"); //for debug;
/*
 * db part
 */
$sql = "select * from stats where convert(trxtime, date) = '$date' and siteid = $siteid and sales_number > 0";
$rs = mysql_query($sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error());
$dbtotals = mysql_num_rows($rs);
$dbsales = array();
$dbtrxes = array();
if ($totals != $dbtotals) {
	//check out the differences
	while ($r = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		array_push($dbsales, $r);
		array_push($dbtrxes, $r['transactionid']);
	}
	sort($dbtrxes);
	//exit(print_r($dbsales, true) . "\n"); //for debug;
	$diff_less = array_diff($trxes, $dbtrxes);
	$diff_more = array_diff($dbtrxes, $trxes);
	echo "ours: " . count($dbtrxes) . ", api's: " . count($trxes) . "\n";
	echo "less ones: \n"; print_r($diff_less);//PDD's less than BBR's 
	echo "more ones: \n"; print_r($diff_more);//BBR's less than PDD's
} else {
	exit("great! $totals/$dbtotals, matched. (" . date("Y-m-d h:i:s") . ")\n");
}

echo print_r($sales, true);
echo "\n";
echo print_r($dbsales, true);
echo "\n";
?>