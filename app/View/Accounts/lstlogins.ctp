<?php 
$this->Js->JqueryEngine->jQueryObject = 'jQuery';
echo $this->Html->scriptBlock(
		array('inline' => false)
);
?>

<h1>Log in/out Logs</h1>

<?php
echo $this->element('timezoneblock');
?>

<?php
$userinfo = $this->Session->read('Auth.Account');
?>

<?php
echo $this->Form->create(null, array('url' => array('controller' => 'accounts', 'action' => 'lstlogins')));
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
						'update' => '#ViewOnlineLogAgentid',
						'before' => 'Element.hide(\'divAgentid\');Element.show(\'divAgentidLoading\');',
						'complete' => 'Element.show(\'divAgentid\');Element.hide(\'divAgentidLoading\');',
						'async' => true,
						'dataExpression' => true,
						'method' => 'post',
						'data' => $this->Js->serializeForm(array('isForm' => false, 'inline' => true))
					)
				));
			?>
			</div>
		</td>
		<td class="search-label">Agent:</td>
		<td>
			<div style="float:left;margin-right:20px;">
			<?php
				echo $this->Form->input('ViewOnlineLog.agentid',
					array('label' => '',
						'options' => $ags, 'type' => 'select',
						'value' => $selagent,
						'style' => 'width:110px;',
						'div' => array('id' => 'divAgentid')
					)
				);
			?>
			</div>
			<div id="divAgentidLoading" style="float:left;width:100px;margin-right:20px;display:none;">
			<?php echo $this->Html->image('iconAttention.gif') . '&nbsp;Loading...'; ?>
			</div>
		</td>
		<td class="search-label" style="width:65px;">IP:</td>
		<td colspan="3">
			<?php
			echo $this->Form->input('ViewOnlineLog.inip',
				array(
					'label' => '',
					'value' => $inip,
					'style' => 'width: 130px;',
					'div' => array('id' => 'divInip')
				)
			);
			?>
		</td>
	</tr>
	<tr>
				<td class="search-label" style="width:65px;">Date:</td>
		<td colspan="3">
			<div style="float:left;width:50px;">
				<b>Start:</b>
			</div>
			<div style="float:left;margin-right:20px;">
			<?php
			echo $this->Form->input('ViewOnlineLog.startdate',
				array('label' => '', 'id' => 'datepicker_start', 'style' => 'width:110px;', 'value' => $startdate));
			?>
			</div>
			<div style="float:left;width:40px;">
				<b>End:</b>
			</div>
			<div style="float:left;margin-right:46px;">
			<?php
			echo $this->Form->input('ViewOnlineLog.enddate',
				array('label' => '', 'id' => 'datepicker_end', 'style' => 'width:110px', 'value' => $enddate));
			?>
			</div>
		</td>
		<td colspan="6">
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
<table style="width:100%">
<thead>
<tr>
	<th><b><?php echo $this->ExPaginator->sort('ViewOnlineLog.username', 'Username'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewOnlineLog.inip', 'IP'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewOnlineLog.intime', 'Login'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewOnlineLog.outtime', 'Logout'); ?></b></th>
</tr>
</thead>
<?php
$i = 0;
foreach ($rs as $r) {
?>
<tr <?php echo $i % 2 == 0? '' : 'class="odd"'; ?>>
	<td align="center"><?php echo $r['ViewOnlineLog']['username']; ?></td>
	<td align="center">
		<a href="http://whatismyipaddress.com/ip/<?php echo $r['ViewOnlineLog']['inip']; ?>" target="findip_window">
			<?php echo $r['ViewOnlineLog']['inip']; ?>
		</a>
	</td>
	<td align="center"><?php echo $r['ViewOnlineLog']['intime']; ?></td>
	<td align="center"><?php echo $r['ViewOnlineLog']['outtime']; ?></td>
</tr>
<?php
$i++;
}
?>
</table>

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