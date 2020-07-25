<?php 
include "app/vendors/ShortURL.class.php";

/*
include "app/vendors/zmysqlConn.class.php";

echo "AN TEST FOR CLASS ShortURL <br/><br/>";

$ps = [];
$ps[0] = ShortURL::encode(4);
$ps[1] = ShortURL::encode(8);
$ps[2] = ShortURL::encode(13392130222);

echo print_r($ps, true);
echo "<br/><br/>";

$ds = [];
$ds[0] = ShortURL::decode("3");
$ds[1] = ShortURL::decode("3");
$ds[2] = ShortURL::decode("5fB");
$conn = new zmysqlConn();
$sql = sprintf("select username from accounts where id = %d", $ds[2]);
$result = mysql_query($sql, $conn->dblink);
if ($result !== false) {
	$row = mysql_fetch_assoc($result);
	$ds[2] = $row['username'];
} else {
	$ds[2] = 'error';
}
echo print_r($ds, true);
*/

echo "********************<br/>";
$t = isset($_GET['t']) ? $_GET['t'] : '';
if (empty($t)) {
	echo ShortURL::encrypt("1/1/NEPT0002");
	echo "<br/>";
	echo ShortURL::decrypt("YmNmkYZ9gbmUYWJq", SHORT_URL_KEY);
} else {
	echo ShortURL::decrypt($t, SHORT_URL_KEY);
}
?>