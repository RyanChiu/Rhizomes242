<?php 
/**
 * this driver is for the new site, it'll be abbrivated with "blds" (keep diff from the others)
 */

include "../app/vendors/extrakits.inc.php";
include "../app/vendors/zmysqlConn.class.php";

$ip = __getclientip();
$tz = "EST";
$now = new DateTime("now", new DateTimeZone($tz));

$bname = basename($_SERVER["PHP_SELF"], ".php");
/*
 * just log every POST/GET at the very beginning
*/
$logpath = "./logs/$bname.log";
$from = "from ip: $ip";
$ending =  " [" . $ip . "/" . $now->format("Y-m-d H:i:s") . "($tz)]\n";
error_log("######\n", 3, $logpath);
if (empty($_POST) && empty($_GET)) {
	error_log(
		$from . "\nNothing posted here" . $ending,
		3,
		$logpath
	);
	echo "nothing posted";
} else {
	if(!empty($_POST)) {
		error_log(
			$from . "(POST)\n" . print_r($_POST, true) . $ending,
			3,
			$logpath
		);
	}
	if(!empty($_GET)) {
		error_log(
			$from . "(GET)\n" . print_r($_GET, true) . $ending,
			3,
			$logpath
		);
	}
}
//exit(); //for debugging
$err = "";
$s = "";
/*actually save the data into stats*/
if (true || $ip == "66.180.199.11" || $ip == "127.0.0.1") {
	$stamp = (isset($_GET['date']) ? trim($_GET['date']) : (isset($_POST['date']) ? trim($_POST['date']) : ''));
	$stamp = strtolower($stamp);
	$type = (isset($_GET['type']) ? trim($_GET['type']) : (isset($_POST['type']) ? trim($_POST['type']) : 'ill'));//not for sure
	$type = strtolower($type);
	$agent = (isset($_GET['agent']) ? trim($_GET['agent']) : (isset($_POST['agent']) ? trim($_POST['agent']) : ''));
	$unique = (isset($_GET['unique']) ? trim($_GET['unique']) : (isset($_POST['unique']) ? trim($_POST['unique']) : ''));//not for sure
	$unique = strtolower($unique);
	$ch = (isset($_GET['ch']) ? trim($_GET['ch']) : (isset($_POST['ch']) ? trim($_POST['ch']) : ''));
	$ch = intval($ch);
	$trxid = (isset($_GET['client_id']) ? trim($_GET['client_id']) : (isset($_POST['client_id']) ? trim($_POST['client_id']) : ''));
	$trxid = intval($trxid);
	$affid = (isset($_GET['affid']) ? trim($_GET['affid']) : (isset($_POST['affid']) ? trim($_POST['affid']) : ''));
	if (!empty($trxid)) {
		$type = 'sale';
		$agent = $affid;
	}
	$conn = new zmysqlConn();
	$sql = "select a.*, g.companyid, b.id as 'typeid' 
		from agent_site_mappings a, sites s, accounts n, types b, agents g, companies m 
		where a.siteid = s.id and a.siteid = b.siteid and s.abbr = 'blds' 
			and a.agentid = g.id and g.companyid = m.id
			and a.agentid = n.id and n.username = '$agent'
		ORDER BY typeid";
	$rs = mysql_query($sql, $conn->dblink);
	$chsfrombbr = explode(",", BLDS_CHS);;// !!! MUST MAKE SURE ABOUT THIS ARRAY WITH BBR
	$i = 0;
	$chs_exist = false;
	while ($r = mysql_fetch_assoc($rs)) {
		if ($chsfrombbr[$i] == $ch) {
			$chs_exist = true;
			$typeid = $r['typeid'];
			$agid = $r['agentid'];
			$comid = $r['companyid'];
			$siteid = $r['siteid'];
			$campid = $r['campaignid'];
			$clicks = ($type == 'click' ? 1 : 0);
			$uniques = ($unique == 'y' ? 1 : 0);
			$sales = ($type == 'sale' ? 1 : 0);
			$trxtime = $now->format("Y-m-d H:i:s");
			$donothing = false;
			if ($type == 'sale') {
				if (!empty($stamp)) {
					$ts = DateTime::createFromFormat("Y-m-d", $stamp);
					if ($ts !== false) {
						$trxtime = $ts->format("Y-m-d 00:00:11");
					} else {
						$trxtime = $now->format("Y-m-d 00:00:22");
					}
				} else {
					$trxtime = $now->format("Y-m-d 00:00:33");
				}
				/*
				 * check if $trxid already exists
				 */
				$tsql = sprintf("select * from stats where siteid = %d and transactionid = %d", $siteid, $trxid);
				$trs = mysql_query($tsql, $conn->dblink);
				if ($trs === false) {
					error_log(
						"error:failed to search transactionid '$trxid'\n",
						3,
						$logpath
					);
				} else {
					if (mysql_num_rows($trs) > 0) {
						$donothing = true;
					}
				}
			}

			if (!$donothing) {
				$sql = "insert into stats (agentid, companyid, raws, uniques, chargebacks, signups, frauds, sales_number, typeid, siteid, campaignid, trxtime, transactionid)"
					. " values ($agid, $comid, $clicks, $uniques, 0, 0, 0, $sales, $typeid, $siteid, '$campid', '$trxtime', $trxid)";
				//echo "$sql($i/$ch)\n"; continue; //for debug;
	
				if (mysql_query($sql, $conn->dblink) === false) {
					$err = mysql_error();
				}
			} else {
				error_log(
					"do nothing, cause transactionid '$trxid' already exists.\n",
					3,
					$logpath
				);
			}
		}
		$i++;
		if ($i >= count($chsfrombbr)) break;
	}
	if ($i == 0) {
		error_log("no such an agent '$agent'.\n", 3, $logpath);
		echo "no such an agent '$agent'.";
	} else {
		if ($chs_exist) {
			error_log("ok.\n", 3, $logpath);
			echo "ok";
		} else {
			error_log("no such a type.\n", 3, $logpath);
			echo "no such a type.\n";
		}
	}
} else {
	$s = "illegal visit";
	echo $s;
}

/*
 * log sql err if needed
*/
if (!empty($err)) {
	$now = $now->format("Y-m-d H:i:s");
	$time = str_replace(" ", "", $now);
	$time = str_replace("-", "", $time);
	$time = str_replace(":", "", $time);
	error_log(
		$from . "\n" . $err . $ending,
		3,
		"./logs/err_" . $time . "_" . $bname . ".log"
	);
}

if (!empty($s)) error_log($s . "\n", 3, $logpath);
?>
