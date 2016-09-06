<?php 
/**
 * 
 */
include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

$today = date("Y-m-d");
$lastday = date("Y-m-d", strtotime(date('Y-m-d') . " Sunday"));
if (date("w") == 0) {
	$lastday = date("Y-m-d", strtotime($lastday . " + 6 days"));
} else {
	$lastday = date("Y-m-d", strtotime($lastday . " - 1 days"));
}
$weekend = $lastday;
$weekstart = date("Y-m-d", strtotime($lastday . " - 6 days"));

$monthstart = date("d") <= "15" ? date("Y-m-d", strtotime(date("Y-m-16") . " - 1 month")) : date("Y-m-16"); 
$monthend = date("Y-m-d", strtotime("$monthstart + 1 month - 1 day"));

$zconn = new zmysqlConn();

$_sql_ = 
	"SELECT %d, '%s', stats.agentid,  accounts.username, agents.ag1stname, companies.officename, 
		sum(sales_number - chargebacks) as sales 
	FROM stats, accounts, agents, companies   
	WHERE %s
		and stats.siteid not in (2, 7, 8, 11)
		and stats.agentid = accounts.id and agents.id = stats.agentid 
		and agents.companyid = companies.id 
		AND agentid > 0 
	GROUP BY agentid  ORDER BY `sales` desc  LIMIT 10;";
$sql = sprintf($_sql_, 0, $today, "convert(trxtime, date) >= '2016-08-14' and convert(trxtime, date) <= '$today'");
$rs = mysql_query("delete from top10s where flag = 0", $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
$rs = mysql_query("insert into top10s " . $sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
$sql = sprintf($_sql_, 1, $today, "convert(trxtime, date) <= '$weekend' AND convert(trxtime, date) >= '$weekstart'");
$rs = mysql_query("delete from top10s where flag = 1", $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
$rs = mysql_query("insert into top10s " . $sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
$sql = sprintf($_sql_, 2, $today, "convert(trxtime, date) <= '$monthend' AND convert(trxtime, date) >= '$monthstart'");
$rs = mysql_query("delete from top10s where flag = 2", $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
$rs = mysql_query("insert into top10s " . $sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");
echo "top10s generated.(" . date("Y-m-d H:i:s") . ")\n";
?>
