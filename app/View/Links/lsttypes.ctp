<h1>Types</h1>
<br/>
<table style="width:100%">
<caption>Host Name:<?php if (!empty($rs)) echo $rs[0]['ViewType']['hostname']; ?></caption>
<thead>
	<tr>
		<th><?php echo $this->ExPaginator->sort('ViewType.typename', 'Type Name'); ?></th>
		<th><?php echo $this->ExPaginator->sort('ViewType.url', 'Type URL'); ?></th>
		<th><?php echo $this->ExPaginator->sort('ViewType.price', 'Payout'); ?></th>
		<th><?php echo $this->ExPaginator->sort('ViewType.earning', 'Earning'); ?></th>
		<th>Start</th>
		<th>End</th>
		<th><?php echo $this->ExPaginator->sort('ViewType.status', 'Status'); ?></th>
		<th>Operation</th>
	</tr>
</thead>
	<?php
	foreach ($rs as $r) :
	?>
	<tr>
		<td align="center"><?php echo $r['ViewType']['typename']; ?></td>
		<td align="center"><?php echo $r['ViewType']['url']; ?></td>
		<td align="center"><?php echo $r['ViewType']['price']; ?></td>
		<td align="center"><?php echo $r['ViewType']['earning']; ?></td>
		<td align="center"><?php echo $r['ViewType']['start']; ?></td>
		<td align="center"><?php echo $r['ViewType']['end']; ?></td>
		<td align="center"><?php echo $r['ViewType']['status'] == 0 ? 'Suspended' : 'Activated'; ?></td>
		<td align="center">
		<?php
		echo $this->Html->link(
			$this->Html->image('iconEdit.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;',
			array('controller' => 'links', 'action' => 'updtype', 'id' => $r['ViewType']['id']),
			array('title' => 'Click to edit this type.', 'escape' => false),
			false
		);
		?>
		</td>
	</tr>
	<?php
	endforeach;
	?>
</table>
