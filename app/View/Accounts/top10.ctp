<h1>Top 10</h1>

<?php
echo $this->element('timezoneblock');
?>

<?php
echo $this->Form->create(
	null, 
	array(
		'url' => array('controller' => 'accounts', 'action' => 'top10'), 
		'id' => 'frmTop10'
	)
);
?>
<div style="margin:6px 20px 6px 2px;">
<table>
	<tr>
		<td>
		<div style="float:left;margin:0px 5px 0px 0px;">
		<?php
		echo $this->Form->input('Top10.period',
			array(
				'id' => 'selPeriod',
				'label' => '', 'type' => 'select',
				'options' => $periods,
				'selected' => isset($start)? ($start . ',' . $end) : null,
				'style' => 'width:210px;'
			)
		);
		?>
		</div>
		<div style="float:left;">
		<?php
		echo $this->Form->submit('>>', array('style' => 'width:30px;'));
		?>
		</div>
		</td>
	</tr>
</table>
</div>
<?php
if (!empty($rs)) {
?>
	<table style="font-size:90%;width:100%;">
		<caption style="font-style:italic;">
		The Period (From <?php echo $start; ?> To <?php echo $end; ?>)
		</caption>
		<thead>
		<tr>
			<th>Top NO.</th>
			<th>Office</th>
			<th>Agent</th>
			<th>Total Sales</th>
		</tr>
		</thead>
		<?php
		$i = 0;
		foreach ($rs as $r) {
			$i++;
		?>
		<tr <?php echo $i <= 3 ? 'style="font-weight:bold;"' : ''; ?>>
			<td align="center"><?php echo $i; ?></td>
			<td align="center"><?php echo $r[0]['sales'] > 0 ? $r['Top10Stats']['officename'] : ''; ?></td>
			<td align="center"><?php echo $r[0]['sales'] > 0 ? $r['Top10Stats']['username'] : ''; ?></td>
			<td align="center"><?php echo $r[0]['sales'] > 0 ? $r[0]['sales'] : ''; ?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<div style="display:none">
	<?php echo $this->Form->submit('go', array('id' => 'iptSubmit'));?>
	</div>
<?php
}
echo $this->Form->input('Top10.start', array('type' => 'hidden', 'id' => 'iptStart', 'value' => isset($start) ? $start : 0));
echo $this->Form->input('Top10.end', array('type' => 'hidden', 'id' => 'iptEnd', 'value' => isset($end) ? $end : 0));
echo $this->Form->end();
?>

<script type="text/javascript">
jQuery("#selPeriod").change(function() {
	__zSetFromTo("selPeriod", "iptStart", "iptEnd");
});
</script>