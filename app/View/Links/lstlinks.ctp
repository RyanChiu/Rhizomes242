<?php
//echo print_r($rs, true);
App::import('Vendor', 'extrakits');
$userinfo = $this->Session->read('Auth.User.Account');
?>
<h1>Link Codes</h1>
<br/>
<div style="float:right">
<?php
if ($userinfo['role'] == 0) {//means an administrator
	echo $this->Html->link(
		'Configure Sites...',
		array('controller' => 'links', 'action' => 'lstsites')
	);
}
?>
</div>
<!--  
<small>(You're from:<?php //echo __getclientip(); ?>, and you'll be <?php //echo __isblocked(__getclientip()) ? 'blocked.' : 'passed.'; ?>)</small>
-->
<?php
echo $this->Form->create(null, array('url' => array('controller' => 'links', 'action' => 'lstlinks')));
?>
<table style="width:100%">
<caption>
	Please get your link(s)
	<br/>
	<font style="color:red;">
	<?php
	if (!empty($suspsites)) {
		echo '>>Site "' . implode(",", $suspsites) . '"' . (count($suspsites) > 1 ? ' are' : ' is')
			. ' suspended for now.';
	}
	?>
	</font>
</caption>
<tr>
	<td width="31%" align="right">
	<?php
	echo $this->Form->input('Site.id',
		array('options' => $sites, 'style' => 'width:170px;', 'label' => 'Site:', 'type' => 'select')
	);
	?>
	</td>
	<td width="40%" align="center">
	<?php
	echo $this->Form->input('ViewAgent.id',
		array('options' => $ags, 'style' => 'width:290px;', 'label' => 'Agent:', 'type' => 'select')
	);
	?>
	</td>
	<td width="29%">
	<?php
	echo $this->Form->submit('Generate Link Codes', array('style' => 'width:180px;'));
	?>
	</td>
</tr>
</table>
<?php
echo $this->Form->end();
?>

<br/>
<?php
if (!empty($rs)) {
?>
	<table style="width:100%;border:0;">
	<?php
	foreach ($rs as $r):
		if (array_key_exists('ViewLink', $r)) {
	?>
		<tr>
			<td align="center">
			<?php
			echo $r['ViewLink']['sitename'] . '_' . $r['ViewLink']['typename'] . ':&nbsp;&nbsp;&nbsp;';
			echo '<b>';
			echo $this->Html->url(
				array('controller' => 'accounts', 'action' => 'golink',
					'to' => __codec($r['ViewLink']['id'] . ',' . $r['ViewLink']['agentid'], 'E')
				),
				true
			);
			echo '</b>';
			?>
			</td>	
		</tr>
	<?php
		} else if (array_key_exists('AgentSiteMapping', $r)) {
			$i = 0;
			foreach ($types as $type) {
	?>
		<tr>
			<td align="center">
			<?php
			/*
			 * HARD CODE HERE, IN ORDER TO SHOW SOME SPECIAL INFO FOR CAMS2
			 */
			$typealias = "";
			if ($r['AgentSiteMapping']['siteid'] == 7) {
				if ($i == 0) {
					$typealias = "(Straight)";
				} else if ($i == 1) {
					$typealias = "(Gay)";
				} else if ($i == 2) {
					$typealias = "(Straight)";
				} else if ($i == 3) {
					$typealias = "(Straight)";
				}
			}
			
			echo $sites[$r['AgentSiteMapping']['siteid']] . '_' . $type['Type']['typealias'] 
				. $typealias . ':&nbsp;&nbsp;&nbsp;';
			echo '<b>';
			echo $this->Html->url(array('controller' => 'accounts', 'action' => 'go'), true) . '/'
				. $r['AgentSiteMapping']['siteid'] . '/'
				. $type['Type']['id']. '/'
				. $ags[$r['AgentSiteMapping']['agentid']];
			echo '</b>';
			?>
			</td>
		</tr>
	<?php
			$i++;
			}
		}
	endforeach;
	?>
	</table>
<?php
}
?>
