<?php 
$this->Js->JqueryEngine->jQueryObject = 'jQuery';
echo $this->Html->scriptBlock(
		array('inline' => false)
);
?>

<?php
$userinfo = $this->Session->read('Auth.User.Account');
//echo print_r($userinfo, true);
//echo '<br/>';
?>
<h1>Chat Logs</h1>

<?php
echo $this->element('timezoneblock');
?>

<?php
echo $this->Form->create(null, array('url' =>  array('controller' => 'accounts', 'action' => 'lstchatlogs')));
?>
<div style="width:100%;margin-top:5px;">
<table style="width:100%">
<caption>
<?php echo $this->Html->image('iconSearch.png', array('style' => 'width:16px;height:16px;')) . 'Search'; ?>
</caption>
<tr>
	<td class="search-label">Office:</td>
	<td>
		<div style="float:left;margin-right:20px;">
		<?php
			if ($userinfo['role'] != 2) {
				echo $this->Form->input('Stats.companyid',
					array('label' => '',
						'options' => $coms, 'type' => 'select',
						'value' => $selcom,
						'style' => 'width:110px;'
					)
				);
				$this->Js->get("#StatsCompanyid")->event("change", $this->Js->request(
					array(
						'controller' => 'stats', 'action' => 'switchagent'
					),
					array(
						'update' => '#ViewChatLogAgentid',
						'before' => 'Element.hide(\'divAgentid\');Element.show(\'divAgentidLoading\');',
						'complete' => 'Element.show(\'divAgentid\');Element.hide(\'divAgentidLoading\');',
						'async' => true,
						'dataExpression' => true,
						'method' => 'post',
						'data' => $this->Js->serializeForm(array('isForm' => false, 'inline' => true))
					)
				));
			} else {
				echo $this->Form->input('Stats.companyid',
					array('label' => '',
						'type' => 'hidden',
						'value' => $selcom
					)
				);
				echo $coms[$selcom];
			}
		?>
		</div>
	</td>
	<td class="search-label">Agent:</td>
	<td>
		<div style="float:left;margin-right:20px;">
		<?php
			if ($userinfo['role'] != 2) {
				echo $this->Form->input('ViewChatLog.agentid',
					array('label' => '',
						'options' => $ags, 'type' => 'select',
						'value' => $selagent,
						'style' => 'width:110px;',
						'div' => array('id' => 'divAgentid')
					)
				);
			} else {
				echo $this->Form->input('ViewChatLog.agentid',
					array('label' => '',
						'type' => 'hidden',
						'value' => $selagent
					)
				);
				echo $ags[$selagent];
			}
		?>
		</div>
		<div id="divAgentidLoading" style="float:left;width:100px;margin-right:20px;display:none;">
		<?php echo $this->Html->image('iconAttention.gif') . '&nbsp;Loading...'; ?>
		</div>
	</td>
	<td class="search-label">Site:</td>
	<td>
		<div style="float:left;margin-right:20px;">
		</div>
		<?php
			echo $this->Form->input('ViewChatLog.siteid',
				array('label' => '',
					'options' => $sites, 'type' => 'select',
					'value' => $selsite,
					'style' => 'width:150px;'
				)
			);
		?>
	</td>
</tr>
<tr>
	<td class="search-label" style="width:65px;">Date:</td>
	<td colspan="3">
		<div style="float:left;width:40px;">
			<b>Start:</b>
		</div>
		<div style="float:left;margin-right:20px;">
		<?php
		echo $this->Form->input('ViewChatLog.startdate',
			array('label' => '', 'id' => 'datepicker_start', 'style' => 'width:110px;', 'value' => $startdate));
		?>
		</div>
		<div style="float:left;width:40px;">
			<b>End:</b>
		</div>
		<div style="float:left;margin-right:46px;">
		<?php
		echo $this->Form->input('ViewChatLog.enddate',
			array('label' => '', 'id' => 'datepicker_end', 'style' => 'width:110px', 'value' => $enddate));
		?>
		</div>
	</td>
	<td colspan="2">
	<?php
	echo $this->Form->submit('Search', array('style' => 'width:110px;'));
	?>
	</td>
</tr>
</table>
</div>
<?php
echo $this->Form->end();
?>

<br/>
<div style="margin-bottom:3px">
<?php
if (in_array($userinfo['role'], array(2))) {//means an agent
	echo $this->Form->button('Submit Chat Log',
		array(
			'onclick' => 'javascript:location.href="' .
				$this->Html->url(array('controller' => 'accounts', 'action' => 'addchatlogs')) . '"',
			'style' => 'width:160px;'
		)
	);
}
?>
</div>
<?php
if (!empty($rs)) {
?>
	<table style="width:100%">
	<thead>
	<tr>
		<th><b><?php echo $this->ExPaginator->sort('ViewChatLog.officename', 'Office'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewChatLog.username4m', 'Agent'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewChatLog.sitename', 'Site'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewChatLog.clientusername', 'Client Name'); ?></b></th>
		<th><b><?php echo 'Conversation'; ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewChatLog.submittime', 'Submit Time'); ?></b></th>
	</tr>
	</thead>
	<?php
	$i = 0;
	foreach ($rs as $r) {
	?>
	<tr <?php echo $i % 2 == 0? '' : 'class="odd"'; ?>>
		<td align="center"><?php echo $r['ViewChatLog']['officename']; ?></td>
		<td align="center"><?php echo $r['ViewChatLog']['username']; ?></td>
		<td align="center"><?php echo $r['ViewChatLog']['sitename']; ?></td>
		<td align="center"><?php echo $r['ViewChatLog']['clientusername']; ?></td>
		<td><?php echo str_replace("\n", "<br/>", $r['ViewChatLog']['conversation']); ?></td>
		<td align="center"><?php echo $r['ViewChatLog']['submittime']; ?></td>
	</tr>
	<?php
	$i++;
	}
	?>
	</table>
<?php
}
?>

<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<?php
echo $this->element('paginationblock');
?>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(function() {
		jQuery('#datepicker_start').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
});
</script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(function() {
		jQuery('#datepicker_end').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
});
</script>