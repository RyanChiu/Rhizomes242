<?php
App::import('Vendor', 'extrakits');
App::import('Vendor', 'zmysqlConn');
?>
<?php
class StatsController extends AppController {
	/*properties*/
	var $name = 'Stats';
	var $uses = array(
		'ViewCompany', 'ViewLiteAgent', 'Site', 'Type',
		'Stats', 'Site', 'Type',
		'ViewStats', 'TmpStats', 'RunStats', 'ViewTStats',
		'ViewSaleLog'
	);
	var $components = array('RequestHandler');
	var $helpers = array(
		'Form', 'Html', 'Js',
		'ExPaginator'
	);
	
	var $curuser = null;
	var $__limit = 500;
	var $__runid = -1;
		
	/*callbacks*/
	function beforeFilter() {
		$this->set('title_for_layout', 'The Rhizomes.[STATS]');
		if ($this->Session->check("Auth")) {
			$u = $this->Session->read("Auth");
			$u = array_values($u);
			if (count($u) == 0) {
				$this->curuser = null;
			} else {
				$this->curuser = $u[0]['Account'];
			}
		} else {
			$this->curuser = null;
		}
		
		/*set $this->__runid*/
		if ($this->curuser) {
			if ($this->Session->valid()) {
				if (!$this->Session->check('runid')) {
					$runid = array('RunStats' => array('id' => null, 'runtime' => date('Y-m-d H:i:s')));
					$this->RunStats->create();
					$tmp = $this->RunStats->saveAll($runid);//generate the runid
					$this->Session->write('runid', $this->RunStats->id);
				}
				$this->__runid = $this->Session->read('runid');
			}
		}
		
		/*check if the user could visit some actions*/
		$this->__handleAccess();
		
		parent::beforeFilter();
	}
	
	function __accessDenied() {
		$this->Session->setFlash('Sorry, you are not authorized to visit that location, so you\'ve been relocated here.');
		$this->redirect(array('controller' => 'accounts', 'action' => 'index'));
	}
	
	function __handleAccess() {
		if ($this->curuser == null) {
			$this->__accessDenied();
			return;
		}
	
		if ($this->curuser['role'] == 0) {//means an administrator
			
			return;
		}
		if ($this->curuser['role'] != 0) {//means an office or an agent
			switch ($this->request->params['action']) {
				case '':
				case '':
					$this->__accessDenied();
					return;
			}
		}
	}
		
	function ___getcond_4statsby_only() {
		$conditions = array('1' => '0');
		if ($this->curuser) {}
		else return $conditions;
		$conditions = array(
			'trxtime >=' => ($this->request->data['Stats']['startdate'] . ' 00:00:00'),
			'trxtime <=' => ($this->request->data['Stats']['enddate'] . ' 23:59:59')
		);
		if ($this->request->data['Stats']['siteid'] != 0) {
			$conditions += array('siteid' => $this->request->data['Stats']['siteid']);
		}
		if ($this->request->data['Stats']['typeid'] != 0) {
			$conditions += array('typeid' => $this->request->data['Stats']['typeid']);
		}
		if ($this->curuser['role'] == 0) {//means an administrator
			if (!empty($this->request->data['Stats']['companyid'])
				&& !in_array('0', $this->request->data['Stats']['companyid'])) {
				$conditions += array('companyid' => $this->request->data['Stats']['companyid']);
			}
			if ($this->request->data['Stats']['agentid'] != 0) {
				$conditions += array('agentid' => $this->request->data['Stats']['agentid']);
			}
		} else if ($this->curuser['role'] == 1) {//means a company
			$conditions += array('companyid' => $this->curuser['id']);
			if ($this->request->data['Stats']['agentid'] != 0) {
				$conditions += array('agentid' => $this->request->data['Stats']['agentid']);
			}
		} else if ($this->curuser['role'] == 2) {//means an agent
			$conditions += array('agentid' => $this->curuser['id']);
		}
		return $conditions;
	}
	
	function ___getwhere_4statsby_only() {
		$where = 'where false';
		if ($this->curuser) {}
		else return $where;
		$where = 'where trxtime >= "' . $this->request->data['Stats']['startdate'] . ' 00:00:00"'
		. ' and trxtime <= "' . $this->request->data['Stats']['enddate'] . ' 23:59:59"';
		if ($this->request->data['Stats']['siteid'] != 0) {
			$where .= ' and siteid = ' . $this->request->data['Stats']['siteid'];
		}
		if ($this->request->data['Stats']['typeid'] != 0) {
			$where .= ' and typeid = ' . $this->request->data['Stats']['typeid'];
		}
		if ($this->curuser['role'] == 0) {//means an administrator
			if (!empty($this->request->data['Stats']['companyid'])
					&& !in_array('0', $this->request->data['Stats']['companyid'])) {
				$coms = $this->request->data['Stats']['companyid'];
				$where .= ' and companyid ' . (is_array($coms) ? ('in (' . implode(", ", $coms) . ')') : " = $coms");
			}
			if ($this->request->data['Stats']['agentid'] != 0) {
				$where .= ' and agentid = ' . $this->request->data['Stats']['agentid'];
			}
		} else if ($this->curuser['role'] == 1) {//means a company
			$where .= ' and companyid = ' . $this->curuser['id'];
			if ($this->request->data['Stats']['agentid'] != 0) {
				$where .= ' and agentid = ' . $this->request->data['Stats']['agentid'];
			}
		} else if ($this->curuser['role'] == 2) {//means an agent
			$where .= ' and agentid = ' . $this->curuser['id'];
		}
		
		//don't show hidden ones stats
		$coms = $this->ViewCompany->find('list',
			array(
				'fields' => array('companyid', 'officename'),
				'conditions' => array('status >= 0')
			)
		);
		$where .= ' and companyid in (' . implode(",", array_keys($coms)) . ')';
						
		return $where;
	}
	
	function ___prepconstparms_4statsby_only(
		&$sites, &$types, &$coms, &$ags, &$periods
	) {
		$periods = array('0' => '-CHOOSE PAYOUT PERIOD-');
		$periods += array(date('Y-m-d') . ',' . date('Y-m-d') => 'TODAY');
		$periods += array(
			date('Y-m-d', mktime(0,0,0,date("m"), date("d") - 1, date("Y")))
			. ','
			. date('Y-m-d', mktime(0,0,0,date("m"), date("d") - 1, date("Y")))
			=> 'YESTERDAY'
		);
		
		/*
		$halfmons = array();
		for ($i = 0; $i < 12; $i++) {
			$halfmon = mktime(0, 0, 0, date('m') - 1 - $i, date('d'), date('Y'));
			$halfmons += array(
				date('Y-m-01,Y-m-15', $halfmon) 
					=> date('M 1-15, Y', $halfmon)
			);
			$lastday = date('d', mktime(0, 0, 0, date('m') - $i, 0, date('Y')));
			$halfmon = mktime(0, 0, 0, date('m') - 1 - $i, date('d'), date('Y'));
			$halfmons += array(
				date('Y-m-16,Y-m-' . $lastday, $halfmon) => date('M 16-' . $lastday . ', Y', $halfmon)
			);
		}
		if (date('Y-m-d') <= date('Y-m-15')) {
			$halfmons = array(date('Y-m-01,Y-m-' . date('d')) => date('M 1-' .date('j') . ', Y')) + $halfmons;
		} else {
			$lastday = date('d');
			$halfmons = array(date('Y-m-16,Y-m-' . $lastday) => date('M 16-' . $lastday . ', Y')) + $halfmons;
			$halfmons = array(date('Y-m-01,Y-m-15') => date('M 1-15, Y')) + $halfmons;
		}
		$periods += $halfmons;
		*/
		
		$lastday = date("Y-m-d", strtotime(date('Y-m-d') . " Sunday"));
		//$lastday = date("Y-m-d", strtotime($lastday . " + 6 days"));
		if (date("D") == "Sun") {
			$lastday = date("Y-m-d", strtotime($lastday . " + 6 days"));
		} else {
			$lastday = date("Y-m-d", strtotime($lastday . " - 1 days"));
		}
		$periods += array(
			date("Y-m-d", strtotime($lastday . " - 6 days")) . ',' . $lastday
			=> 'THIS WEEK'
		);
		$periods += array(
			date("Y-m-d", strtotime($lastday . " - 13 days"))
			. ','
			. date("Y-m-d", strtotime($lastday . " - 7 days"))
			=> 'LAST WEEK'
		);
		$periods += array(
			date("Y-m-d", strtotime($lastday . " - 20 days"))
			. ','
			. date("Y-m-d", strtotime($lastday . " - 14 days"))
			=> 'TWO WEEKS AGO'
		);
		for ($i = 3; $i < 24; $i++) {
			$m = 7 * ($i + 1) -1;
			$n = 7 * $i;
			$tmpstart = date("Y-m-d", strtotime($lastday . " - $m days"));
			$tmpend = date("Y-m-d", strtotime($lastday . " - $n days"));
			$periods += array(
				$tmpstart . ',' . $tmpend
				=> $tmpstart . '~' .$tmpend
			);
		}
		
		$periods += array(
			date('Y-m', mktime(0,0,0,date("m"), date("d"), date("Y"))) . '-01'
			. ','
			. date('Y-m-d', mktime(0,0,0,date("m") + 1, 0, date("Y")))
			=> 'THIS MONTH'
		);
		$periods += array(
			date('Y-m', mktime(0,0,0,date("m") - 1, date("d"), date("Y"))) . '-01'
			. ','
			. date('Y-m-d', mktime(0,0,0,date("m"), 0, date("Y")))
			=> 'LAST MONTH'
		);
		for ($i = 2; $i < 12; $i++) {
			$periods += array(
				date('Y-m', mktime(0,0,0,date("m") - $i, date("d"), date("Y"))) . '-01'
				. ','
				. date('Y-m-d', mktime(0,0,0,date("m") - $i + 1, 0, date("Y")))
				=> 'MONTH ' . date('Y-m', mktime(0,0,0,date("m") - $i, date("d"), date("Y")))
			);
		}
		$periods += array(
			date('Y-m-d', mktime(0,0,0, 1, 1, date("Y")))
			. ','
			. date('Y-m-d', mktime(0,0,0, 12, 31, date("Y")))
			=> 'THIS YEAR'
		);
		$periods += array(
			date('Y-m-d', mktime(0,0,0, 1, 1, date("Y") - 1))
			. ','
			. date('Y-m-d', mktime(0,0,0, 12, 31, date("Y") - 1))
			=> 'LAST YEAR'
		);
		
		$this->___prepparms_4statsby_only(
			$tmp0, $tmp1, $selsite, $tmp2, $selcoms, $tmp3
		);
		
		$sites = array();
		$types = array();
		$coms = array();
		$ags = array();
		$sites = $this->Site->find('list',
			array(
				'fields' => array('id', 'sitename'),
				'conditions' => array('status' => 1),
				'order' => 'sitename'
			)
		);
		$sites = array('0' => 'All') + $sites;
		
		$types = $this->Type->find('list',
			array(
				'fields' => array('id', 'typename'),
				'conditions' => array('1' => '1')
					+ ($selsite == -1 ? array('1' => '1') : array('siteid' => $selsite)),
				'order' => 'typename'
			)
		);
		$types = array('0' => 'All') + $types;

		if ($this->curuser['role'] == 0) {//means an administrator
			$coms = $this->ViewCompany->find('list',
				array(
					'fields' => array('companyid', 'officename'),
					'conditions' => array('status >= 0'),
					'order' => 'officename'
				)
			);
		}
		$coms = array('0' => 'All') + $coms;

		if ($this->curuser['role'] == 0) {//means an administrator
			$ags = $this->ViewLiteAgent->find('list',
				array(
					'fields' => array('id', 'username'),
					'conditions' => array(/*'status' => 1*/)
						+ (empty($selcoms) || in_array('0', $selcoms) ? array('companyid' => array_keys($coms)) : array('companyid' => $selcoms)),
					'order' => 'username4m'
				)
			);
		} else if ($this->curuser['role'] == 1) {//means an office
			$ags = $this->ViewLiteAgent->find('list',
				array(
					'fields' => array('id', 'username'),
					'conditions' => array(
						'companyid' => $this->curuser['id']/*,  
						'status' => 1*/
					),
					'order' => 'username4m'
				)
			);
		} else if ($this->curuser['role'] == 2) {//means an agent
			$ags = $this->ViewLiteAgent->find('list',
				array(
					'fields' => array('id', 'username'),
					'conditions' => array(
						'id' => $this->curuser['id']/*,  
						'status' => 1*/
					),
					'order' => 'username4m'
				)
			);
		}
		$ags = array('0' => 'All') + $ags;
		
		$this->set(compact('sites'));
		$this->set(compact('types'));
		$this->set(compact('coms'));
		$this->set(compact('ags'));
		$this->set(compact('periods'));
	}
	
	function ___prepparms_4statsby_only(
		&$startdate, &$enddate, &$selsite, &$seltype, &$selcoms, &$selagent
	) {
		$selsite = 0;
		if (!empty($this->request->data)) {
			$selsite = $this->request->data['Stats']['siteid'];
		} else if (array_key_exists('siteid', $this->passedArgs)) {
			$selsite = $this->passedArgs['siteid'];
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$selsite = $conds['selsite'];
			}
		}
		$seltype = 0;
		if (!empty($this->request->data)) {
			$seltype = $this->request->data['Stats']['typeid'];
		} else if (array_key_exists('typeid', $this->passedArgs)) {
			$seltype = $this->passedArgs['typeid'];
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$seltype = $conds['seltype'];
			}
		}
		$selcoms = array();
		if (!empty($this->request->data)) {
			$__selcoms = $this->request->data['Stats']['companyid'];
			$selcoms = is_array($__selcoms) ? $__selcoms : array($__selcoms);
		} else if (array_key_exists('companyid', $this->passedArgs)) {
			$selcoms = explode(',', $this->passedArgs['companyid']);
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$selcoms = $conds['selcoms'];
			}
		}
		$selagent = 0;
		if (!empty($this->request->data)) {
			$selagent = $this->request->data['Stats']['agentid'];
		} else if (array_key_exists('agentid', $this->passedArgs)) {
			$selagent = $this->passedArgs['agentid'];
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$selagent = $conds['selagent'];
			}
		}
		
		$__lastday = date("Y-m-d", strtotime(date('Y-m-d') . " Sunday"));
		if (date("D") == "Sun") {
			$__lastday = date("Y-m-d", strtotime($__lastday . " + 6 days"));
		} else {
			$__lastday = date("Y-m-d", strtotime($__lastday . " - 1 days"));
		}
		$startdate = date("Y-m-d", strtotime($__lastday . " - 6 days"));
		$enddate = $__lastday;
		
		if (!empty($this->request->data)) {
			$startdate = $this->request->data['Stats']['startdate'];
		} else if (array_key_exists('startdate', $this->passedArgs)) {
			$startdate = $this->passedArgs['startdate'];
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$startdate = $conds['startdate'];
			}
		}
		if (!empty($this->request->data)) {
			$enddate = $this->request->data['Stats']['enddate'];
		} else if (array_key_exists('enddate', $this->passedArgs)) {
			$enddate = $this->passedArgs['enddate'];
		} else if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
			if ($this->Session->check('conditions_stats')) {
				$conds = $this->Session->read('conditions_stats');
				$enddate = $conds['enddate'];
			}
		}
	}
	
	function ___prepare_into_t_stats($sites, $selsite, $group, $where, $groupby, $runid, $conn) {
		$sites4loop = [];
		foreach ($sites as $sid => $sname) {
			if ($sid > 0) {
				$sites4loop[$sid] = $sname;
			}
		}
		if ($selsite > 0) {
			unset($sites4loop);
			$sites4loop[$selsite] = "couldbeanyofsitenames";
		}
		$sql = "";
		foreach ($sites4loop as $sid => $sname) {
			$sql = "insert into t_stats "
				. "select convert(trxtime, date),"
				. "agentid,"
				. "companyid,"
				. "siteid,"
				. "sum(raws),"
				. "sum(uniques),"
				. "sum(chargebacks),"
				. "sum(signups),"
				. "sum(frauds),"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 0, 1), sales_number, 0)) as sales_type1,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 1, 1), sales_number, 0)) as sales_type2,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 2, 1), sales_number, 0)) as sales_type3,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 3, 1), sales_number, 0)) as sales_type4,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 4, 1), sales_number, 0)) as sales_type5,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 5, 1), sales_number, 0)) as sales_type6,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 6, 1), sales_number, 0)) as sales_type7,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 7, 1), sales_number, 0)) as sales_type8,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 8, 1), sales_number, 0)) as sales_type9,"
				. "sum(if(typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 9, 1), sales_number, 0)) as sales_type10,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 0, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type1_payout," 
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 0, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type1_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 1, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type2_payout," 
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 1, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type2_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 2, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type3_payout," 
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 2, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type3_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 3, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type4_payout," 
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 3, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type4_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 4, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type5_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 4, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type5_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 5, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type6_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 5, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type6_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 6, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type7_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 6, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type7_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 7, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type8_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 7, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type8_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 8, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type9_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 8, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type9_earning,"
				. "(SELECT price FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 9, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type10_payout,"
				. "(SELECT earning FROM fees where (typeid = (SELECT id FROM types WHERE siteid = $sid order by id limit 9, 1)) and (stats.trxtime >= start and stats.trxtime <= end)) as sales_type10_earning,"
				. $runid . ", " . $group
				. " from stats "
				. $where . " and siteid = $sid"
				. $groupby;
			$result = mysql_query($sql, $conn->dblink);
		}

		return $sql . "(($selsite))";
	}

	function __statsby($group) {
		$this->layout = "defaultlayout";
		
		/*if $this->__runid is N/A, then redirect to the home page*/
		if ($this->__runid == -1) {
			$this->redirect(array('controller' => 'accounts', 'action' => 'index'));
		}
		
		/*prepare for the parameters*/
		$this->___prepconstparms_4statsby_only($sites, $types, $coms, $ags, $periods);
		$this->___prepparms_4statsby_only($startdate, $enddate, $selsite, $seltype, $selcoms, $selagent);

		/*prepare addons for the conditions & group by*/
		$gbaddons = '';
		$_gbaddons = [];
		$order = 'trxtime desc';
		switch ($group) {
			case 1:
				$gbaddons = 'convert(trxtime, date)';
				array_push($_gbaddons, 'trxtime');
				$order = 'trxtime desc, username4m asc';
				break;
			case 2:
				$gbaddons = 'companyid';
				array_push($_gbaddons, 'companyid');
				$order = 'officename asc, trxtime desc';
				break;
			case 3:
				$gbaddons = 'agentid';
				array_push($_gbaddons, 'agentid');
				$order = 'username4m asc, trxtime desc';
				break;
			case 4:
				$gbaddons = 'convert(trxtime, date), companyid, agentid';
				array_push($_gbaddons, 'trxtime');
				array_push($_gbaddons, 'companyid');
				array_push($_gbaddons, 'agentid');
				$order = 'trxtime desc, username4m asc';
				break;
		}
		$groupby = ' group by ' . $gbaddons;
		$orderby = ' order by ' . $order;		
			
		$conn = new zmysqlConn();
		if (empty($this->request->data)) {
			/* see if there is already a result which was grouped by some group inserted. 
			 * but do nothing when it's about paginating. */
			
			/*prepare the data*/
			if (!(array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs))) {
				//if it's not paginating, then it should be drilling down
				if ($this->Session->check('conditions_stats')
					|| (array_key_exists('clear', $this->passedArgs) && $this->passedArgs['clear'] == -2)) {
					
					if (array_key_exists('clear', $this->passedArgs) && $this->passedArgs['clear'] == -1) {
						$tmp_periods = array_keys($periods);
						$tmp = explode(",", $tmp_periods[3]);
						$startdate = $tmp[0];
						$enddate = $tmp[1];
						$where = ' where trxtime >= "' . $startdate . ' 00:00:00"'
							. ' and trxtime <= "' . $enddate . ' 23:59:59"';
						$selsite = 0;
						$seltype = 0;
						$types = $this->Type->find('list',
							array(
								'fields' => array('id', 'typename')
							)
						);
						$types = array('0' => 'All') + $types;
						$this->set(compact('types'));
						/*
						if ($selsite != 0) {
							$where .= ' and siteid = ' . $selsite;
						}
						*/
					} else {
						$where = ' where trxtime >= "' . $startdate . ' 00:00:00"'
							. ' and trxtime <= "' . $enddate . ' 23:59:59"';
						if ($selsite != 0)
							$where .= ' and siteid = ' . $selsite;
						if ($seltype != 0)
							$where .= ' and typeid = ' . $seltype;
						if (!empty($selcoms) && !in_array('0', $selcoms)) {
							if (count($selcoms) == 1) {
								$where .= ' and companyid = ' . $selcoms[0];
							} else {
								$where .= ' and companyid in (' . implode(",", $selcoms) . ')';
							}
						}
						if ($selagent != 0)
							$where .= ' and agentid = ' . $selagent;
					}
					
					if ($this->curuser['role'] == 1) {//if it's an office
						$where .= ' and companyid = ' . $this->curuser['id'];
					}
					if ($this->curuser['role'] == 2) {//if it's an agent
						$where .= ' and agentid = ' . $this->curuser['id'];
					}
					$sql = 'delete from t_stats where run_id = ' . $this->__runid . ' and group_by = ' . $group;
					$result = mysql_query($sql, $conn->dblink);
					$sql = $this->___prepare_into_t_stats($sites, $selsite, $group, $where, $groupby, $this->__runid, $conn);
					
					$this->Session->write('conditions_stats',
						array(
							'startdate' => $startdate,
							'enddate' => $enddate,
							'selsite' => $selsite,
							'seltype' => $seltype,
							'selcoms' => $selcoms,
							'selagent' => $selagent
						)
					);
					
					$crumbs = array();
					if ($this->Session->check('crumbs_stats')) {
						$crumbs = $this->Session->read('crumbs_stats'); 
					}
					$cururl = array(
						'controller' => 'stats',
						'action' => $group == 1 ? 'statsdate' : ($group == 2 ? 'statscompany' : ($group == 3 ? 'statsagent' : '')),
						'startdate' => $startdate, 'enddate' => $enddate,
						'siteid' => $selsite, 'typeid' => $seltype,
						'companyid' => empty($selcoms) || in_array('0', $selcoms) ? implode(',', array_keys($coms)) : implode(',', $selcoms),
						'agentid' => $selagent
					);
					$isin = false;
					$i = 0;
					foreach ($crumbs as $k => $v) {
						$diff = array_diff_assoc($v, $cururl);
						if (empty($diff)) {
							$isin = true;
							array_splice($crumbs, $i + 1);
							break;
						}
						$i++;
					}
					if (!$isin) {
						$crumbs[$group == 1 ? 'Day' : ($group == 2 ? 'Office' : ($group == 3 ? 'Agent' : ''))] = $cururl;
					}
					$this->Session->write('crumbs_stats', $crumbs);
				} else {
					$this->Session->setFlash('Illegal drilldown(2).');
					$this->redirect(array('controller' => 'accounts', 'action' => 'index'));
				}
			}
		} else {
			$where = $this->___getwhere_4statsby_only();
			$sql = 'delete from t_stats where run_id = ' . $this->__runid . ' and group_by = ' . $group;
			$result = mysql_query($sql, $conn->dblink);
			$sql = $this->___prepare_into_t_stats($sites, $selsite, $group, $where, $groupby, $this->__runid, $conn);
			
			$startdate = $this->request->data['Stats']['startdate'];
			$enddate = $this->request->data['Stats']['enddate'];
			$selsite = $this->request->data['Stats']['siteid'];
			$seltype = $this->request->data['Stats']['typeid'];
			$__selcoms = $this->request->data['Stats']['companyid'];
			$selcoms = is_array($__selcoms) ? $__selcoms : array($__selcoms);
			$selagent = $this->request->data['Stats']['agentid'];
			$this->Session->write('conditions_stats',
				array(
					'startdate' => $startdate,
					'enddate' => $enddate,
					'selsite' => $selsite,
					'seltype' => $seltype,
					'selcoms' => $selcoms,
					'selagent' => $selagent
				)
			);
			$this->Session->write('crumbs_stats',
				array(
					$group == 1 ? 'Day' : ($group == 2 ? 'Office' : ($group == 3 ? 'Agent' : '')) => array(
						'controller' => 'stats',
						'action' => $group == 1 ? 'statsdate' : ($group == 2 ? 'statscompany' : ($group == 3 ? 'statsagent' : '')),
						'startdate' => $startdate, 'enddate' => $enddate,
						'siteid' => $selsite, 'typeid' => $seltype,
						'companyid' => empty($selcoms) || in_array('0', $selcoms) ? implode(',', array_keys($coms)) : implode(',', $selcoms),
						'agentid' => $selagent
					)
				)
			);
		}
		
		/*set the vriables for the view*/
		$this->set(compact("startdate"));
		$this->set(compact("enddate"));
		$this->set(compact('selsite'));
		$this->set(compact('seltype'));
		$this->set(compact('selcoms'));
		$this->set(compact('selagent'));
		
		/*prepare totals*/
		$totals = array(
			'raws' => 0, 'uniques' => 0, 'chargebacks' => 0, 'signups' => 0, 'frauds' => 0,
			'sales_type1' => 0, 'sales_type2' => 0, 'sales_type3' => 0, 'sales_type4' => 0,
			'sales_type5' => 0, 'sales_type6' => 0, 'sales_type7' => 0, 'sales_type8' => 0,
			'sales_type9' => 0, 'sales_type10' => 0,
			'net' => 0, 'payouts' => 0, 'earnings' => 0
		);
		array_push($_gbaddons, 'run_id');
		$fields = array(
			'run_id',
			'group_by',
			'trxtime',
			'siteid',
			'companyid',
			'officename',
			'agentid',
			'username',
			'ag1stname',
			'aglastname',
			'sum(raws) as raws',
			'sum(uniques) as uniques',
			'sum(chargebacks) as chargebacks',
			'sum(signups) as signups',
			'sum(frauds) as frauds',
			'sum(sales_type1) as sales_type1',
			'sum(sales_type2) as sales_type2',
			'sum(sales_type3) as sales_type3',
			'sum(sales_type4) as sales_type4',
			'sum(sales_type5) as sales_type5',
			'sum(sales_type6) as sales_type6',
			'sum(sales_type7) as sales_type7',
			'sum(sales_type8) as sales_type8',
			'sum(sales_type9) as sales_type9',
			'sum(sales_type10) as sales_type10',
			'sum(net) as net',
			'sum(payouts) as payouts',
			'sum(earnings) as earnings'
		);
		$rs = $this->ViewTStats->find('all',
			array(
				'fields' => $fields,
				'conditions' => array('run_id' => $this->__runid, 'group_by' => $group, 'status > 0'),
				'group' => $_gbaddons
			)
		);
		if (!empty($rs)) {
			foreach ($rs as $rvts) {//debug($rvts);
				$totals['raws'] += $rvts[0]['raws'];
				$totals['uniques'] += $rvts[0]['uniques'];
				$totals['chargebacks'] += $rvts[0]['chargebacks'];
				$totals['signups'] += $rvts[0]['signups'];
				$totals['frauds'] += $rvts[0]['frauds'];
				$totals['sales_type1'] += $rvts[0]['sales_type1'];
				$totals['sales_type2'] += $rvts[0]['sales_type2'];
				$totals['sales_type3'] += $rvts[0]['sales_type3'];
				$totals['sales_type4'] += $rvts[0]['sales_type4'];
				$totals['sales_type5'] += $rvts[0]['sales_type5'];
				$totals['sales_type6'] += $rvts[0]['sales_type6'];
				$totals['sales_type7'] += $rvts[0]['sales_type7'];
				$totals['sales_type8'] += $rvts[0]['sales_type8'];
				$totals['sales_type9'] += $rvts[0]['sales_type9'];
				$totals['sales_type10'] += $rvts[0]['sales_type10'];
				$totals['net'] += $rvts[0]['net'];
				$totals['payouts'] += $rvts[0]['payouts'];
				$totals['earnings'] += $rvts[0]['earnings'];
			}
		}
		$this->set('totals', $totals);
		/*pagination things*/
		$this->paginate = array(
			'ViewTStats' => array(
				'fields' => $fields,
				'conditions' => array('run_id' => $this->__runid, 'group_by' => $group, 'status > 0'),
				'group' => $_gbaddons,
				'order' => $order,
				'limit' => $this->__limit
			)
		);		
		$this->set('rs',
			$this->paginate('ViewTStats')
		);
		$this->set('limit', $this->__limit);
	}
	
	function statsdate() {
		$this->__statsby(1);
		
		$this->set('bywhat', 0);
		$this->render('statsem');
	}
	
	function statscompany() {
		$this->__statsby(2);
		
		$this->set('bywhat', 1);
		$this->render('statsem');
	}
	
	function statsagent() {
		$this->__statsby(3);
		
		$this->set('bywhat', 2);
		$this->render('statsem');
	}
	
	function statsagdetail() {
		$this->__statsby(4);
		
		$this->set('bywhat', 3);
		$this->render('statsem');
	}
	
	function switchtype() {
		$this->layout = "emptylayout";
		Configure::write('debug', '0');
		
		$options = array('0' => 'All');
		if($this->request->data['Stats']['siteid'] != -1) {
		    $options = $options + $this->Type->find('list',
		    	array(
		    		'fields' => array('id', 'typename'),
		    		'conditions' => array('siteid' => $this->request->data['Stats']['siteid'])
		    	)
		    );
		} else {
			$options = array('-1' => '--------');
		}
		$this->set(compact('options'));
		$this->render('switchem');
	}
	
	function switchagent() {
		$this->layout = "emptylayout";
		Configure::write('debug', 0);
		
		$options = array('0' => 'All');
		if(!empty($this->request->data['Stats']['companyid'])
			&& !in_array('0', $this->request->data['Stats']['companyid'])) {
		    $options = $options + $this->ViewLiteAgent->find('list',
		    	array(
		    		'fields' => array('id', 'username'),
		    		'conditions' => array('companyid' => $this->request->data['Stats']['companyid']),
		    		'order' => 'username4m'
		    	)
		    );
		} else {
			$options = $options + $this->ViewLiteAgent->find('list',
		    	array(
		    		'fields' => array('id', 'username'),
		    		'order' => 'username4m'
		    	)
		    );
		}
		$this->set(compact('options'));
		$this->render('switchem');
	}
	
	function updfrauds() {
		$this->layout = "emptylayout";
		Configure::write('debug', 0);
		
		$frauds = intval($_REQUEST['value']);
		if ($this->__runid != -1 && $frauds >= 0
			&& array_key_exists('date', $this->passedArgs)
			&& array_key_exists('agentid', $this->passedArgs)
			&& array_key_exists('siteid', $this->passedArgs)
			&& array_key_exists('typeid', $this->passedArgs)
		) {
			if ($this->Stats->updateAll(
					array('frauds' => $frauds),
					array(
						'convert(trxtime, date)' => $this->passedArgs['date'],
						'agentid' => $this->passedArgs['agentid'],
						'siteid' => $this->passedArgs['siteid'],
						'typeid' => $this->passedArgs['typeid']
					)
				)
				&&
				$this->TmpStats->updateAll(
					array('frauds' => $frauds),
					array(
						'runid' => $this->__runid,
						'convert(trxtime, date)' => $this->passedArgs['date'],
						'agentid' => $this->passedArgs['agentid'],
						'siteid' => $this->passedArgs['siteid'],
						'typeid' => $this->passedArgs['typeid']
					)
				)
			) {
			} else {
				$frauds = -1;
			}
			
		}
		$this->set(compact('frauds'));
	}
	
	function lstsales() {
		$this->layout = "defaultlayout";
		
		$conditions = array('1' => '1');
		if ($this->curuser['role'] == 0) {
			
		} else if ($this->curuser['role'] == 1) {
			$conditions = array(
				'comid' => $this->curuser['id']
			);
		} else if ($this->curuser['role'] == 2) {
			$conditions = array(
				'agid' => $this->curuser['id']
			);
		}
		
		$sales = $this->ViewSaleLog->find('all',
			array(
				'conditions' => $conditions,
				'order' => 'date desc'
			)
		);
		
		$this->paginate = array(
			'ViewSaleLog' => array(
				'conditions' => $conditions,
				'order' => 'date desc',
				'limit' => ($this->__limit / 5)
			)
		);
		
		$rs = $this->paginate('ViewSaleLog');
		$i = 0;
		if ($this->curuser['role'] == 1) {
			foreach ($rs as $r) {
				$rs[$i]['ViewSaleLog']['comid'] = $r['companies']['comid'];
				$rs[$i]['ViewSaleLog']['office'] = $r['companies']['office'];
				$i++;
			}
		} else if ($this->curuser['role'] == 2) {
			foreach ($rs as $r) {
				$rs[$i]['ViewSaleLog']['agid'] = $r['agents']['agid'];
				$rs[$i]['ViewSaleLog']['email'] = $r['agents']['email'];
				$rs[$i]['ViewSaleLog']['agent'] = $r['agents']['agent'];
				$i++;
			}
		}
		
		$this->set('rs', $rs);
	}
}
?>