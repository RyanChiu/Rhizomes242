<?php 
$this->Js->JqueryEngine->jQueryObject = 'jQuery';
echo $this->Html->scriptBlock(
		array('inline' => false)
);
?>

<?php
$userinfo = $this->Session->read('Auth.User.Account');
//echo str_replace("\n", "<br>", print_r($rs[0], true));
?>
<h1>Click Logs</h1>

<?php
echo $this->element('timezoneblock');
?>

<div style="width:100%;margin-top:5px;" id="search">
<?php
echo $this->Form->create(null, array('url' => array('controller' => 'links', 'action' => 'lstclickouts')));
?>
<table style="width:100%">
<caption>
<?php echo $this->Html->image('iconSearch.png', array('style' => 'width:16px;height:16px;')) . 'Search'; ?>
</caption>
<tr>
	<td class="search-label" style="width:105px;">Office:</td>
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
						'update' => '#StatsAgentid',
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
	<td class="search-label" style="width:65px;">Agent:</td>
	<td>
		<div style="float:left;margin-right:20px;">
		<?php
			if ($userinfo['role'] != 2) {
				echo $this->Form->input('Stats.agentid',
					array('label' => '',
						'options' => $ags, 'type' => 'select',
						'value' => $selagent,
						'style' => 'width:110px;',
						'div' => array('id' => 'divAgentid')
					)
				);
			} else {
				echo $this->Form->input('Stats.agentid',
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
	<td class="search-label" style="width:65px;">Site:</td>
	<td>
		<?php
		echo $this->Form->input('Stats.siteid',
			array('label' => '',
				'options' => $sites, 'type' => 'select',
				'value' => $selsite,
				'style' => 'width:110px;',
				'div' => array('id' => 'divSiteid')
			)
		);
		?>
	</td>
	<td class="search-label" style="width:65px;">IP From:</td>
	<td>
		<?php
		echo $this->Form->input('ViewClickout.fromip',
			array(
				'label' => '',
				'value' => $fromip,
				'style' => 'width: 130px;',
				'div' => array('id' => 'divIpfrom')
			)
		);
		?>
	</td>
</tr>
<tr>
	<td class="search-label" style="width:65px;">Date:</td>
	<td colspan="5">
		<div style="float:left;width:50px;">
			<b>Start:</b>
		</div>
		<div style="float:left;margin-right:20px;">
		<?php
		echo $this->Form->input('ViewClickout.startdate',
			array('label' => '', 'id' => 'datepicker_start', 'style' => 'width:80px;', 'value' => $startdate));
		?>
		</div>
		<div style="float:left;width:40px;">
			<b>End:</b>
		</div>
		<div style="float:left;margin-right:46px;">
		<?php
		echo $this->Form->input('ViewClickout.enddate',
			array('label' => '', 'id' => 'datepicker_end', 'style' => 'width:80px', 'value' => $enddate));
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
<?php
echo $this->Form->end();
?>
</div>
<br/>

<table style="width:100%">
<caption>
Start Date:<?php echo $startdate; ?>&nbsp;&nbsp;End Date:<?php echo $enddate; ?>&nbsp;&nbsp;|&nbsp;&nbsp;
Office:<?php echo $coms[$selcom]; ?>&nbsp;&nbsp;Agent:<?php echo $ags[$selagent]; ?>
<br/>
<font color="red" size="2"><b>(Click on IP to see an address for a world map, where your link was opened.)</b></font>
</caption>
<thead>
<tr>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.officename', 'Office'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.username', 'Agent'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.sitename', 'Site'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.typename', 'Type'); ?></b></th>
	<th><b>Link</b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.clicktime', 'Click Time'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewClickout.fromip', 'IP From'); ?></b></th>
	<th <?php echo $userinfo['role'] == 0 ? '' : 'class="naClassHide"'; // HARD CODES?>>
		<b><?php echo $this->ExPaginator->sort('ViewClickout.referer', 'Referer'); ?></b>
	</th>
</tr>
</thead>
<?php
$i = 0;
foreach ($rs as $r):
?>
<tr <?php echo $i % 2 == 0 ? '' : 'class="odd"'; ?>>
	<td><?php echo $r['ViewClickout']['officename']; ?></td>
	<td><?php echo $r['ViewClickout']['username']; ?></td>
	<td><?php echo $r['ViewClickout']['sitename']; ?></td>
	<td><?php echo $r['ViewClickout']['typename']; ?></td>
	<td>
	<?php
		if ($r['ViewClickout']['typename'] != '') {
			echo 'http://'. $_SERVER['HTTP_HOST']
				. $this->Html->url(array('controller' => 'accounts', 'action' => 'go'))
				. '/' . $r['ViewClickout']['siteid']
				. '/' . $r['ViewClickout']['typeid']
				. '/' . $r['ViewClickout']['username'];
		} else {
			echo '-';
		}
	?>
	</td>
	<td><?php echo $r['ViewClickout']['clicktime']; ?></td>
	<td>
		<a href="http://whatismyipaddress.com/ip/<?php echo $r['ViewClickout']['fromip']; ?>" target="findip_window">
			<?php echo $r['ViewClickout']['fromip']; ?>
		</a>
	</td>
	<td><?php echo $r['ViewClickout']['referer']; ?></td>
</tr>
<?php
$i++;
endforeach;
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

	jQuery(function() {
		jQuery('#datepicker_end').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});

	var obj;
	obj = jQuery(".naClassHide");
	tbl = obj.parent().parent().parent();
	obj.each(function(i){
		idx = jQuery("th", obj.parent()).index(this);
		this.hide();
		jQuery("td:eq(" + idx + ")", jQuery("tr", tbl)).hide();
	});
});
</script>