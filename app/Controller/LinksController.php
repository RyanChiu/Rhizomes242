<?php
App::import('Vendor', 'extrakits');
?>
<?php
class LinksController extends AppController {
	/*properties*/
	var $name = 'Links';
	var $uses = array(
		'Link', 'Site', 'Type', 'Fee', 'Clickout', 
		'Account', 'Company', 'Agent',
		'ViewLink', 'ViewSite', 'ViewType', 'ViewClickout',
		'ViewCompany', 'ViewAgent',
		'AgentSiteMapping', 'ViewMapping',
		'SiteExcluding'
	);
	var $helpers = array(
		'Form', 'Html', 'Javascript',
		'ExPaginator'
	);
	
	var $curuser = null;
	var $__limit = 50;
	
	/*callbacks*/
	function beforeFilter() {
		$this->set('title_for_layout', 'The GlobalNetAdvertising.[LINKS]');
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
			/**
			 * HARD CODE:
			 * avoid admin whose id is not 1 nor 2 to access some functions
			 */
			if (!in_array($this->curuser['id'], array(1, 2))) {
				switch ($this->request->params['action']) {
					case 'lstsites':
					case 'addsite':
					case 'updsite':
						$this->__accessDenied();
						return;
				}
			}
			return;
		}
		if ($this->curuser['role'] != 0) {//means an office or an agent
			switch ($this->request->params['action']) {
				case 'lstsites':
				case 'addsite':
				case 'updsite':
				case 'activatesite':
				case 'suspendsite':
				case 'lsttypes':
				case 'updtype':
				case 'lstcampaigns':
					$this->__accessDenied();
					return;
			}
		}
	}
	
	function lsttypes($id = -1) {
		$this->layout = "defaultlayout";
		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		$conditions = array('1' => '1');
		if ($id != -1) {
			array_push($conditions, array('siteid' => array(-1, $id)));
		}
		$this->paginate = array(
			'ViewType' => array(
				'conditions' => $conditions
				//'limit' => $this->__limit
			)
		);
		$this->set('rs', $this->paginate('ViewType'));
	}
	
	function updtype($id = -1) {
		$this->layout = "defaultlayout";

		//TEMPORARILY DISABLED
		//$this->render("tempdisinfo");

		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		if (empty($this->request->data)) {
			$this->Type->id = $id;
			$this->request->data = $this->Type->read();
			if (empty($this->request->data)) {
				$this->Session->setFlash('Sorry, no such a type.');
				$this->redirect(array('controller' => 'links', 'action' => 'lsttypes'));
			}
		} else {
			if (empty($this->request->data['Type']['url'])) {
				$this->request->data['Type']['url'] = null;
			}
			if ($this->Type->save($this->request->data)) {
				$this->ViewType->id = $this->request->data['Type']['id'];
				$data = $this->ViewType->read();
				$this->Session->setFlash('Type saved.');
				$this->redirect(array('controller' => 'links', 'action' => 'lsttypes', 'id' => $data['ViewType']['siteid']));
			}
		}
	}
	
	function lstsites() {
		$this->layout = "defaultlayout";
		
		/*
		$this->paginate = array(
			'ViewSite' => array(
				'limit' => $this->__limit
			)
		);
		*/
		$this->set('status', $this->Site->status);
		$this->set('rs', $this->paginate('ViewSite'));
	}
	
	function addsite() {
		$this->layout = "defaultlayout";

		//TEMPORARILY DISABLED
		//$this->render("tempdisinfo");
		
		if (!empty($this->request->data)) {
			if ($this->Site->save($this->request->data)) {
				$this->Session->setFlash('Site added.');
				$this->redirect(array('controller' => 'links', 'action' => 'lstsites'));
			}
		}
		$this->set('types', $this->Site->types);
	}
	
	function updsite($id = -1) {
		$this->layout = "defaultlayout";

		//TEMPORARILY DISABLED
		//$this->render("tempdisinfo");

		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		$rs = array();
		if (empty($this->request->data)) {
			$this->Site->id = $id;
			$this->request->data = $this->Site->read();
			$rs = $this->request->data;
			if (empty($this->request->data)) {
				$this->Session->setFlash('Sorry, no such site.');
				$this->redirect(array('controller' => 'links', 'action' => 'lstsites'));
			}
		} else {
			if ($this->Site->save($this->request->data)) {
				$this->Session->setFlash('Site updated.');
				$this->redirect(array('controller' => 'links', 'action' => 'lstsites'));
			}
		}
		$this->set('rs', $rs);
		$this->set('types', $this->Site->types);
	}
	
	function activatesite($id = -1) {
		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		if ($this->Site->updateAll(array('status' => 1), array('id' => $id))) {
			$this->Session->setFlash('Site activated.');
		}
		$this->redirect(array('controller' => 'links', 'action' => 'lstsites'));
	}
	
	function suspendsite($id = -1) {
		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		if ($this->Site->updateAll(array('status' => 0), array('id' => $id))) {
			$this->Session->setFlash('Site suspended.');
		}
		$this->redirect(array('controller' => 'links', 'action' => 'lstsites'));
	}
	
	function lstlinks($id = -1) {
		$this->layout = "defaultlayout";
		
		//TEMPORARILY DISABLED
		//$this->render("tempdisinfo");

		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		/*find out not "hidden" companies*/
		$coms = $this->ViewCompany->find('list',
			array(
				'fields' => array('companyid', 'officename'),
				'conditions' => array('status >= 0'),
				'order' => 'officename'
			)
		);
		
		/*prepare the agents for this view from DB*/
		/*prepare the sites for the view from DB*/
		$vags = array();
		$ags = array();
		$sags = array();
		$sites = array();
		//$exsites = array();
		$suspsites = $this->Site->find('list',
			array(
				'fields' => array('id', 'sitename'),
				'conditions' => array(
					'status' => 0//,
					//'id !=' => 5/*hard code here, try to put test site away*/
				),
				'order' => 'sitename'
			)
		);
		if ($this->curuser['role'] == 0) {//means an administrator
			$vags = $this->ViewAgent->find('all',
				array(
					'fields' =>	array(
						'id',
						'username',
						'officename'
					),
					'conditions' => array(
						'status >=' => 0,
						'AND' => array('companyid' => array_keys($coms))
					),
					'order' => 'username4m'
				)
			);
			$sites = $this->Site->find('list',
				array(
					'fields' => array('id', 'sitename'),
					'order' => 'id'
				)
			);
		} else if ($this->curuser['role'] == 1) {//means an office
			$vags = $this->ViewAgent->find('all',
				array(
					'fields' =>	array(
						'id',
						'username',
						'officename'
					),
					'conditions' => array('companyid' => $this->curuser['id']),
					'order' => 'username4m'
				)
			);
			$__exsites = $this->SiteExcluding->find('list',
				array(
					'fields' => array('siteid'),
					'conditions' => array('companyid' => $this->curuser['id']),
					'group' => 'siteid'
				)
			);
			$sites = $this->Site->find('list',
				array(
					'fields' => array('id', 'sitename'),
					'conditions' => array('status' => 1, 'not' => array('id' => $__exsites)),
					'order' => 'sitename'
				)
			);
			/*
			$exsites = $this->Site->find('list',
				array(
					'fields' => array('id', 'sitename'),
					'conditions' => array('id' => $__exsites),
					'order' => 'sitename'
				)
			);
			*/
		} else if ($this->curuser['role'] == 2) {//means an agent
			$vags = $this->ViewAgent->find('all',
				array(
					'fields' =>	array(
						'id',
						'username',
						'officename'
					),
					'conditions' => array('id' => $this->curuser['id']),
					'order' => 'username4m'
				)
			);
			$agcp = $this->Agent->find('first',
				array(
					'conditions' => array('id' => $this->curuser['id'])
				)
			);
			$__exsites = $this->SiteExcluding->find('list',
				array(
					'fields' => array('siteid'),
					'conditions' => array('companyid' => $agcp['Agent']['companyid']),
					'group' => 'siteid'
				)
			);
			$__exsites += $this->SiteExcluding->find('list',
				array(
					'fields' => array('siteid'),
					'conditions' => array('agentid' => $this->curuser['id']),
					'group' => 'siteid'
				)
			);
			$__exsites = array_unique($__exsites);
			$sites = $this->Site->find('list',
				array(
					'fields' => array('id', 'sitename'),
					'conditions' => array('status' => 1, 'not' => array('id' => $__exsites)),
					'order' => 'sitename'
				)
			);
			/*
			$exsites = $this->Site->find('list',
				array(
					'fields' => array('id', 'sitename'),
					'conditions' => array('id' => $__exsites),
					'order' => 'sitename'
				)
			);
			*/
		}
		
		//$sites = array(0 => 'All') + $sites;
		foreach ($vags as $vag) {
			$ags += array($vag['ViewAgent']['id'] => $vag['ViewAgent']['username']);
		}
		$sagsi = 0;
		foreach ($vags as $vag) {
			$sagsi++;
			$sags += array(
				$vag['ViewAgent']['id'] => 
					(
						$vag['ViewAgent']['username'] 
							. "____[" 
							. $vag['ViewAgent']['officename']
							. " #"
							. $sagsi
							. "]"
					)
			);
		}
		$this->set('ags', $ags);
		$this->set('sags', $sags);
		$this->set('sites', $sites);
		//$this->set('exsites', $exsites);
		$this->set('suspsites', $suspsites);
		
		$this->set('rs', array());
		if (!empty($this->request->data)) {
			if ($this->request->data['Site']['id'] == -1) {//REMARK IT FROM CCI:site CAM4 is the 1st & special one
				
			} else {
				/*
				 * for the way with new driver commdrv_links.php...processing...
				 * this part will checkout table agent_site_mappings and generate the link from it:
				 * step 1, find out all the campaign ids with "siteid = $this->request->data['Stie']['id']
				 * and agentid = $this->request->data['ViewAgent']['id']" from agent_site_mappings
				 * step2, generate the links with all the types from types in the same site 
				 * (siteid = $this->request->data['Stie']['id']), and with campaign id one by one.
				 */
				$rs = $this->AgentSiteMapping->find('all',
					array(
						'conditions' => array(
							'siteid' => $this->request->data['Site']['id'],
							'agentid' => $this->request->data['ViewAgent']['id'],
							'flag' => 1
						)
					)
				);
				$types = $this->Type->find('all',
					array(
						'conditions' => array(
							'AND' => array(
								'siteid' => $this->request->data['Site']['id'],
								'status' => '1',
								'NOT' => array('url' => 'NULL')
							)
						)
					)
				);
				$this->set(compact('rs'));
				$this->set(compact('types'));
			}
		}
	}
	
	function lstcampaigns($id = null) {
		$this->layout = 'defaultlayout';
		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		$rs = array();
		if (!empty($id)) {
			$this->paginate = array(
				'ViewMapping' => array(
					'conditions' => array('agentid' => array(-1, $id))
				)
			);
			$rs = $this->paginate('ViewMapping');
		}
		$this->set(compact('rs'));
	}
	
	function lstclickouts($id = -1) {
		$this->layout = 'defaultlayout';
		if (array_key_exists('id', $this->request->params['named'])){
			$id = $this->request->params['named']['id'];
		}
		
		$startdate = $enddate = date("Y-m-d", strtotime(date('Y-m-d') . " Sunday"));
		//$startdate = date("Y-m-d", strtotime($enddate . " - 7 days"));
		//$enddate = date("Y-m-d", strtotime($startdate . " + 6 days"));
		if (date("D") == "Sun") {
			$enddate = date("Y-m-d", strtotime($startdate . " + 6 days"));
		} else {
			$startdate = date("Y-m-d", strtotime($enddate . " - 7 days"));
			$enddate = date("Y-m-d", strtotime($startdate . " + 6 days"));
		}
		$fromip = '';

		$selcom = $selagent = $selsite = 0;
		if ($this->curuser['role'] == 1) {
			$selcom = $this->curuser['id'];
		} else if ($this->curuser['role'] == 2) {
			$selagent = $this->curuser['id'];
			$rs = $this->Agent->find('first',
				array(
					'fields' => array('companyid'),
					'conditions' => array('id' => $selagent)
				)
			);
			if (!empty($rs)) {
				$selcom = $rs['Agent']['companyid'];
			}
		}
		
		$coms = $this->ViewCompany->find('list',
			array(
				'fields' => array('companyid', 'officename'),
				'conditions' =>  array(
					($selcom == 0 ? array('1' => '1') : array('id' => $selcom)),
					'status >= 0'
				),
				'order' => array('officename')
			)
		);
		$coms = array('0' => 'All') + $coms;
		$ags = $this->ViewAgent->find('list',
			array(
				'fields' => array('id', 'username'),
				'conditions' => ($selcom == 0 ? array('companyid' => array_keys($coms)) : array('companyid' => $selcom)),
				'order' => array('username4m')
			)
		);
		$ags = array('0' => 'All') + $ags;
		$sites = $this->ViewSite->find('list',
			array(
				'fields' => array('id', 'sitename'),
				'conditions' => ($this->curuser['role'] == 0) ? array('1' => '1') : array('status' => '1'),
				'order' => array('sitename')
			)
		);
		$sites = array('0' => 'All') + $sites;
		
		$conditions = array();
		if (empty($this->request->data)) {
			$conditions = array(
					'convert(clicktime, date) >=' => $startdate,
					'convert(clicktime, date) <=' => $enddate,
					'1' => '1'
			);
				
			if (array_key_exists('page', $this->passedArgs) || array_key_exists('sort', $this->passedArgs)) {
				if ($this->Session->check('conditions_clickouts')) {
					$conditions = $this->Session->read('conditions_clickouts');
					$condv = array_values($conditions);
					$startdate = $condv[0];
					$enddate = $condv[1];
					$fromip = (array_key_exists('fromip', $conditions) ? $conditions['fromip'] : '');
					$selcom = (array_key_exists('companyid', $conditions) ? $conditions['companyid'][1] : 0);
					$selagent = (array_key_exists('agentid', $conditions) ? $conditions['agentid'] : 0);
					$selsite = (array_key_exists('siteid', $conditions) ? $conditions['siteid'] : 0);
				}
			}
		} else {
			$startdate = $this->request->data['ViewClickout']['startdate'];
			$enddate = $this->request->data['ViewClickout']['enddate'];
			$fromip = trim($this->request->data['ViewClickout']['fromip']);
			$selcom = $this->request->data['Stats']['companyid'];
			$selagent = $this->request->data['Stats']['agentid'];
			$selsite = $this->request->data['Stats']['siteid'];
			if (empty($fromip)) {
				$conditions = array(
					'convert(clicktime, date) >=' => $startdate,
					'convert(clicktime, date) <=' => $enddate,
					'1' => '1'
				);
			} else {
				$conditions = array(
					'convert(clicktime, date) >=' => $startdate,
					'convert(clicktime, date) <=' => $enddate,
					'fromip' => $fromip
				);
			}
			if ($selcom != 0) {
				$conditions['companyid'] = array(0, $selcom);//!!!Very important!!!If not put this way "array(0, $selcom)", the paginating will show wrong with officename.
			}
			if ($selagent != 0) {
				$conditions['agentid'] = $selagent;
			}
			if ($selsite != 0) {
				$conditions['siteid'] = $selsite;
			}
			$this->Session->write('conditions_clickouts', $conditions);
		}
		
		if ($selcom != 0) $conditions['companyid'] = array(-1, $selcom);
		else {
			$concoms = array();
			if (key_exists('companyid', $conditions)){
				$concoms = $conditions['companyid'];
			}
			$conditions['companyid'] = array_keys($coms) + $concoms;
		}
		if ($selagent != 0) $conditions['agentid'] = array(-1, $selagent);
		if ($selsite != 0) $conditions['siteid'] = array(-1, $selsite);
		
		$this->set(compact('startdate'));
		$this->set(compact('enddate'));
		$this->set(compact('fromip'));
		$this->set(compact('coms'));
		$this->set(compact('ags'));
		$this->set(compact('sites'));
		$this->set(compact('selcom'));
		$this->set(compact('selagent'));
		$this->set(compact('selsite'));
		
		$this->paginate = array(
			'ViewClickout' => array(
				'conditions' => $conditions,
				'order' => 'clicktime desc',
				'limit' => $this->__limit
			)
		);
		$this->set('rs', $this->paginate('ViewClickout'));
	}
}
?>
