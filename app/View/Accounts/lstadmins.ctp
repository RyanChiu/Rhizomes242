<h1>Administrators</h1>

<?php
/*showing the results*/
?>

<table style="width:100%">
	<thead>
	<tr>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.username', 'Username'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.originalpwd', 'Password'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.email', 'Email'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.regtime', 'Registered'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.regtime', 'Earnings'); ?></b></th>
		<th><b><?php echo $this->ExPaginator->sort('ViewAdmin.status', 'Status'); ?></b></th>
		<th><b>Operation</b></th>
	</tr>
	</thead>
<?php
$i = 0;
foreach ($rs as $r):
	if (!in_array($r['ViewAdmin']['id'], array(1,2))) {
?>
	<tr <?php echo $i % 2 == 0 ? '' : 'class="odd"'; ?>>
		<td><?php echo $r['ViewAdmin']['username']; ?></td>
		<td><?php echo $r['ViewAdmin']['originalpwd']; ?></td>
		<td><?php echo $r['ViewAdmin']['email']; ?></td>
		<td><?php echo $r['ViewAdmin']['regtime']; ?></td>
		<td><?php echo $r['ViewAdmin']['level'] == 1 ? 'Visible' : 'Invisible'; ?></td>
		<td><?php echo $status[$r['ViewAdmin']['status']]; ?></td>
		<td align="center">
		<?php
		echo $this->Html->link(
			$this->Html->image('iconEdit.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;',
			array('controller' => 'accounts', 'action' => 'updadmin', 'id' => $r['ViewAdmin']['id']),
			array('title' => 'Click to edit this record.', 'escape' => false),
			false
		);
		?>
		</td>
	</tr>
<?php
	$i++;
	}
endforeach;
?>
</table>

<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<?php
echo $this->element('paginationblock');
?>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->