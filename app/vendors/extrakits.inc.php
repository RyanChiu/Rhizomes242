<?php
	/*
	 * define the constants for sites from BBR:
	 * THE CONSTANT NAME combined with 
	 * upper cased abbreviation of the site and "_CHS".
	 * say CAMS2_CHS is for site with abbreviation "cams2".
	 * THE CONSTANT VALUE is a set of numbers seperated by ",".
	 */
	/*
	define("CAMS2_CHS", "0,1,2,3,8,9");
	define("CAMS3_CHS", "4,5,6,7");
	define("BBRD_CHS", "5,6");
	define("ADC_CHS", "10");
	define("BLDS_CHS", "11,12,13,14");
	*/
	define("NED_CHS", "19,20,21");
	define("JSH_CHS", "22,23");
	define("LCS_CHS", "24,25");
	/*
	 * routines area
	 */
	//date_default_timezone_set("Asia/Manila");
	date_default_timezone_set("EST5EDT");

	/*
	 * functions area
	 */
	function __codec($string, $operation) {
		$codes = array(
			array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ',', ' '),
			array('6', '2', '0', 'a', 'c', '1', '3', '4', 'd', '5' ,'7', 'f')
		);
        if($operation=='D')
        {
        	$d = '';
        	for ($i = 0; $i < strlen($string); $i++) {
        		for ($j = 0; $j < count($codes[1]); $j++) {
        			if ($codes[1][$j] == $string[$i]) break;
        		}
        		if ($j == count($codes[1])) return 'err';
        		$d .= $codes[0][$j];
        	}
            return $d;
        }
        else
        {
        	$e = '';
        	for ($i = 0; $i < strlen($string); $i++) {
        		for ($j = 0; $j < count($codes[0]); $j++) {
        			if ($codes[0][$j] == $string[$i]) break;
        		}
        		if ($j == count($codes[0])) return 'err';
        		$e .= $codes[1][$j];
        	}
            return $e;
        }
    }
    
    function __getclientip() {
    	$onlineip = false;
		if(getenv('HTTP_CLIENT_IP')) { 
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR')) { 
			$onlineip = getenv('REMOTE_ADDR');
		} else { 
			$onlineip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		}
		return $onlineip;
    }
    
/*
   function __isblocked($ip, $fiplst = 'philippines') {
    	$handle = fopen(APP . 'vendors' . DS . $fiplst . '.txt', 'r');
    	while (!feof($handle)) {
    		$buf = fgets($handle);
    		$subnet = explode('/', $buf);
    		if (count($subnet) == 2) {
    			//echo '(ip:' . $subnet[0] . ', mask:' . long2ip(0xffffffff << 32 - $subnet[1]) . ')';
    			if (ip2long($ip) >> (32 - $subnet[1]) == ip2long($subnet[0]) >> (32 - $subnet[1])) return true;
    		}
    	}
    	fclose($handle);
    	return false;
    }
*/
   function __isblocked($ip, $fiplst = 'philippines') {
        $url="http://208.76.89.61/isBlock.php?ip=$ip";
        $scrape_ch = curl_init();
        curl_setopt($scrape_ch, CURLOPT_URL, $url);
        curl_setopt($scrape_ch, CURLOPT_RETURNTRANSFER, true);
        
        $scrape = curl_exec( $scrape_ch );
        return "Y" == $scrape;
   }
   
	function __fillzero4m($str, $forelen = 24, $afterlen = 24) {
		/*
		 * we get rid of characters followed "_" (and "_" itself)
		 * from $str here,in order to make the similar username
		 * be closely after sorting.
		 */
		$pos = strpos($str, "_");
		if ($pos !== false) {
			$str = substr($str, 0, $pos);
		}
		
		$str0 = $str;
		$str1 = "";
		for ($i = 0; $i < strlen($str); $i++) {
			if ($str{$i} >= '0' && $str{$i} <= '9') {
				$str0 = substr($str, 0, $i);
				$str1 = substr($str, $i, strlen($str) - $i);
				break;
			}
		}
		
		$str0 = $str0 . str_repeat("0", $forelen - strlen($str0));
		$str1 = str_repeat("0", $afterlen - strlen($str1)) . $str1;
		
		$str = substr($str0, 0, $forelen) . substr($str1, 0, $afterlen);
		
		return $str;
	}
	
	/*functions for stats drivers*/
	function __stats_get_abbr($argv0) {
		$path_parts = pathinfo($argv0);
		$basenames = explode("_", $path_parts['basename']);
		return $basenames[0];
	}
	
	function __stats_get_types_site(&$typeids, &$siteid, $abbr, $dblink) {
		/*find out the typeids and siteid from db by the abbreviation of the site*/
		$sql = sprintf(
			'select a.id as typeid, a.siteid from types a, sites b'
			. ' where a.siteid = b.id and b.abbr = "%s"'
			. ' order by a.id',
			$abbr
		);
		$rs = mysql_query($sql, $dblink)
			or die ("Something wrong with: " . mysql_error());
		$typeids = array();
		$siteid = null;
		while ($row = mysql_fetch_assoc($rs)) {
			array_push($typeids, $row['typeid']);
			$siteid = $row['siteid'];
		}
	}
	
	/*
	 * try to send an email
	 */
	function __phpmail($mailto = "agents.maintainer@gmail.com", $subject = "", $content = "") {
		require_once("Mail.php");
		$mailer = Mail::factory(
			"SMTP",
			array(
				'host' => "ssl://smtp.gmail.com",
				'port' => "465",
				'auth' => true,
				'username' => "agents.maintainer@gmail.com",
				'password' => "`1qaz2wsx"
			)
		);
		
		$a_headers['From'] = "agents.maintainer@gmail.com";
		$a_headers['To'] = $mailto;
		
		$a_headers['Subject'] = $subject;
		
		$res = $mailer->send($a_headers['To'], $a_headers, $content);
		if ($res) {
			$mailinfo = 'email sent.';
		} else {
			$mailinfo = $res->getMessage();
		}
		return $mailinfo;
	}
	
	/*
	 * get the local date of the stats servers
	 * parameters:
	 * origin_dt	the string present date, like 2010-05-01,12:34:56
	 * remote_tz	the time zone of the remote server, like "Europe/London"
	 * offset_h		the offset time in hours
	 * origin_tz	the time zone of the server which the origin_dt belongs to, like "America/New_York"
	 * islongf		if the return value should be as 2010-05-01 or 2010-05-01 12:00:01
	 */
	function __get_remote_date($origin_dt, $remote_tz = null, $offset_h = -1, $origin_tz = "America/New_York", $islongf = false) {
		$err = "Illegal parameter, it should be like '2010-05-01,12:34:56'.\n";
		if (strpos($origin_dt, ",") === false) {
			exit($err);
		}
		$datestr = trim(str_replace(",", " ", $origin_dt));
		if (strlen($datestr) != 19) {
			exit($err);
		}
		if (strtotime($datestr) == -1) {
			exit($err);
		}
		$arydt = explode(",", $origin_dt);
		$ymdhis = array();
		$ymdhis[0] = explode("-", $arydt[0]);
		if (count($ymdhis[0]) != 3) {
			exit($err);
		}
		$ymdhis[1] = explode(":", $arydt[1]);
		if (count($ymdhis[0]) != 3) {
			exit($err);
		}
		if ($remote_tz == null) {
			return $islongf ? $arydt[0] . " " . $arydt[1] : $arydt[0];
		}
		
		$_origin_dtz = new DateTimeZone($origin_tz);
		$_remote_dtz = new DateTimeZone($remote_tz);
		$_origin_dt = new DateTime("now", $_origin_dtz);
		$_remote_dt = new DateTime("now", $_remote_dtz);
		$offset = $_origin_dtz->getOffset($_origin_dt) - $_remote_dtz->getOffset($_remote_dt);
		$dt = date($islongf ? "Y-m-d H:i:s" : "Y-m-d",
			mktime(
				$ymdhis[1][0], $ymdhis[1][1], 
				$ymdhis[1][2] - $offset + ($offset_h * 3600), 
				$ymdhis[0][1], $ymdhis[0][2], $ymdhis[0][0])
		);
		return $dt;
	}
	
	/*
	 * for CKEditor, the file upload function module
	 */
	function __mkuploadhtml($fn,$fileurl,$message) 
	{ 
		$str = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('
			. $fn
			. ', \''
			. $fileurl
			. '\', \''
			. $message
			. '\');</script>'; 
		return $str;
	}
	
	/*
	 * try to save a cookie forever
	 * when $cookievalue equals to null or is ignored, it'll try
	 * to reset the cookie named $cookiename for 1 year again if
	 * it exists and return the value of it, otherwise just will
	 * return null.
	 * when $cookievalue does not equal to null, it'll try to set
	 * the cookie named $cookiename the value of $cookievalue, and
	 * then return the value of $cookievalue, otherwise just will
	 * return null, too. 
	 */
	function __crucify_cookie($cookiename, $cookievalue = null) {
		if ($cookievalue == null) {
			if (isset($_COOKIE[$cookiename])) {
				setcookie(
					$cookiename,
					$_COOKIE[$cookiename], 
					time() + (60 * 60 * 24 * 365)// it seems that it could only be saved for 1 year
				);
				return $_COOKIE[$cookiename];
			}
			return null;
		} else {
			setcookie(
				$cookiename, 
				$cookievalue, 
				time() + (60 * 60 * 24 * 365)// it seems that it could only be saved for 1 year
			);
			if (isset($_COOKIE[$cookiename])) {
				return $_COOKIE[$cookiename];
			} else {
				return null;
			}
		}
	}
	
	/*
	 * try to judge if it's in daylight saving time (summer time).
	 * 1 means true, and 0 means false;
	 */
	function is_dst()
	{
		$timezone = date('e'); //get local time zone
		date_default_timezone_set('US/Pacific-New'); //set time zone
		$dst = date('I'); //judge
		date_default_timezone_set($timezone); //set time zone back
		return $dst;
	}

	/*
	 *postback function for all the sites from Jesse's
	*/
	function _postback4Jesse($_chs_) {
		include_once("zmysqlConn.class.php");
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
				//$agent = $affid;
			}
			$conn = new zmysqlConn();
			$sql = "select a.*, g.companyid, b.id as 'typeid' 
				from agent_site_mappings a, sites s, accounts n, types b, agents g, companies m 
				where a.siteid = s.id and a.siteid = b.siteid and s.abbr = '$bname' 
					and a.agentid = g.id and g.companyid = m.id
					and a.agentid = n.id and n.username = '$agent'
				ORDER BY typeid";
			$rs = mysql_query($sql, $conn->dblink);
			//echo $sql; echo "\n[select]\n"; //for debug
			$chsfrombbr = explode(",", $_chs_);;// !!! MUST MAKE SURE ABOUT THIS ARRAY WITH BBR
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

						if (mysql_query($sql, $conn->dblink) === false) {
							$err = mysql_error();
						}
						//echo "$sql\n($i/$ch)[insert]\n"; $i++; if ($i >= count($chsfrombbr)) break; else continue; //for debug;
					} else {
						error_log(
							"do nothing, cause transactionid '$trxid' already exists.\n",
							3,
							$logpath
						);
						//echo "do nothing:($i/$ch)\n"; $i++; if ($i >= count($chsfrombbr)) break; else continue; //for debug;
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
	}
?>
