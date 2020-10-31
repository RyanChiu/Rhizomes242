<?php 

$userinfo = $this->Session->read('Auth.User.Account');

//debug($rs);
?>

<br/>
<?php
	/*
	 * show searching area here if needed
	 */
?>
<br/>

<table style="width:100%">
<caption></caption>
<thead>
<tr>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.date', 'Date'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.email', 'Email'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.office', 'Office'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.agent', 'Agent'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.site', 'Site'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.link', 'Link'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.earning', 'Earning'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewSaleLog.payout', 'Payout'); ?></b></th>
	<th style="display:none;"><b><?php echo $this->ExPaginator->sort('ViewSaleLog.clickid', 'Click ID'); ?></b></th>
</tr>
</thead>
<?php
$i = 0;
foreach ($rs as $r):
?>
<tr <?php echo $i % 2 == 0 ? '' : 'class="odd"'; ?>>
	<td><?php echo $r['ViewSaleLog']['date']; ?></td>
	<td><?php echo $r['ViewSaleLog']['email']; ?></td>
	<td><?php echo $r['ViewSaleLog']['office']; ?></td>
	<td><?php echo $r['ViewSaleLog']['agent']; ?></td>
	<td><?php echo $r['ViewSaleLog']['site']; ?></td>
	<td><?php echo $r['ViewSaleLog']['link']; ?></td>
	<td><?php echo $r['ViewSaleLog']['earning']; ?></td>
	<td><?php echo $r['ViewSaleLog']['payout']; ?></td>
	<td style="display:none;"><?php echo $r['ViewSaleLog']['clickid']; ?></td>
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