<?php
switch ($bywhat) {
	case 0:
		echo '<h1>Stats (By Date)</h1>';
		break;
	case 1:
		echo '<h1>Stats (By Office)</h1>';
		break;
	case 2:
		echo '<h1>Stats (By Agent)</h1>';
		break;
	case 3:
		echo '<h1>Stats (Details)</h1>';
		break;
	default:
		echo '<h1>No such stats</h1>';
		break;
}
?>

<?php
echo $this->element('timezoneblock');
?>

<?php
//echo print_r($rs, true);
$userinfo = $this->Session->read('Auth.User.Account');
?>
<br/>
<?php
	echo $this->element(
		'searchblock', 
		array(
			'bywhat' => $bywhat,
			'startdate' => $startdate,
			'enddate' => $enddate,
			'sites' => $sites,
			'selsite' => $selsite,
			'periods' => $periods,
			compact('types'),
			compact('seltype'),
			compact('coms'),
			compact('selcom'),
			compact('ags'),
			compact('selagent')
		)
	); 
?>
<br/>

<?php
$_show_pay_ = ($userinfo['role'] == 0 && in_array($userinfo['id'], array(1, 2, 3)));
if (!empty($rs)) {
?>
<table style="width:100%">
	<caption>
	<font style="color:red;">
	<?php
	if ($startdate != $enddate) {
	?>
	From<?php echo '&nbsp;' . $startdate . '&nbsp;'; ?>To<?php echo '&nbsp;' . $enddate; ?>
	<?php
	} else {
	?>
	Date&nbsp;<?php echo $startdate; ?>
	<?php
	}
	?>
	&nbsp;&nbsp;&nbsp;
	<?php
	echo '(';
	echo 'Site:' . $sites[$selsite];
	echo ', Type:' . $types[$seltype];
	if ($userinfo['role'] == 0) {//means an administrator
		echo ', Office:';
		if (!empty($selcoms) && $selcoms[0] != 0) {
			foreach ($selcoms as $selcom) {echo $coms[$selcom] . ' ';};
		} else {
			echo 'All';
		}
		echo ', Agent:' . $ags[$selagent];
	} else if ($userinfo['role'] == 1) {//means an office
		echo ', Agent:' . $ags[$selagent];
	} else if ($userinfo['role'] == 2) {//means an agent
	}
	echo ')';
	?>
	<?php
	if ($selsite != 1) {
	?>
        <br/>
        <!--<font style="background-color:#80ff00">*On free signup (or free), no comission till it converts</font>-->
    <?php
	}
    ?>
    </font>
    <br/>
    <font style="font-size:12px;font-weight:lighter;">
    <?php
    if ($this->Session->check('crumbs_stats')) {
		$crumbs = $this->Session->read('crumbs_stats');
		//#debug echo str_replace("\n", "<br/>", print_r($crumbs, true));
		$j = 0;
		foreach ($crumbs as $k => $v) {
			$j++;
			if ($j == count($crumbs)) {
				$this->Html->addCrumb($k);
			} else {
				$this->Html->addCrumb($k, $v);
			}
		}    
	    echo $this->Html->getCrumbs(" >> ");
    }
    ?>
    </font>
	</caption>
	<thead>
	<tr>
		<th><!-- numbered --></th>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.trxtime', 'Date') . '</th>';
				break;
			case 1:
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.officename', 'Office Name') . '</th>';
				break;
			case 2:
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.username4m', 'Agent') . '</th>';
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.officename', 'Office Name') . '</th>';
				break;
			case 3:
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.trxtime', 'Date') . '</th>';
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.officename', 'Office Name') . '</th>';
				echo '<th>' . $this->ExPaginator->sort('ViewTStats.username4m', 'Agent') . '</th>';
				break;
			default:
				echo '<th></th>';
				break;
		}
		?>	
		<th><?php echo $this->ExPaginator->sort('ViewTStats.raws', 'Raws'); ?></th>
		<th <?php echo !in_array($selsite, array(-1, -2)) ? '' : 'class="naClassHide"'; ?>>
		<?php echo $this->ExPaginator->sort('ViewTStats.uniques', 'Uniques'); ?>
		</th>
		<th <?php echo $selsite != 7 ? '' : 'class="naClassHide"'; ?>>
		<?php echo $this->ExPaginator->sort('ViewTStats.signups', 'Free*'); ?>
		</th>
		<th class="naClassHide">
		<?php //echo $this->ExPaginator->sort('Frauds', 'ViewTStats.frauds'); ?>
		<?php
			echo '<font size="1">'; 
			echo $this->ExPaginator->sort('ViewTStats.frauds', 'Frauds');
			echo '</font>';
			echo '<br/><font size="1">(for revise)</font>';
		?>
		</th>
		<th <?php echo $selsite == 2 ? '' : 'class="naClassHide"'; ?>>
		<?php
			echo $this->ExPaginator->sort('ViewTStats.chargebacks', 'Frauds');
		?>
		</th>
		<?php
		$typesv = $types;
		ksort($typesv);
		reset($typesv);
		$typesv = array_values($typesv);
		?>
		<th <?php echo count($typesv) > 10 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type10', (count($typesv) > 10 ? $typesv[10] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 9 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type9', (count($typesv) > 9 ? $typesv[9] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 8 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type8', (count($typesv) > 8 ? $typesv[8] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 7 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type7', (count($typesv) > 7 ? $typesv[7] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 6 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type6', (count($typesv) > 6 ? $typesv[6] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 5 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type5', (count($typesv) > 5 ? $typesv[5] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 4 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type4', (count($typesv) > 4 ? $typesv[4] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 3 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type3', (count($typesv) > 3 ? $typesv[3] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 2 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type2', (count($typesv) > 2 ? $typesv[2] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo count($typesv) > 1 ? '' : 'class="naClassHide"'; ?>>
		<?php
		echo $this->ExPaginator->sort('ViewTStats.sales_type1', (count($typesv) > 1 ? $typesv[1] : 'N/A'))
		?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<th <?php echo in_array($selsite, array(-1, -2)) ? 'class="naClassHide"' : ''; // just do not show for the some site?>>
		<?php echo $this->ExPaginator->sort('ViewTStats.net', 'Net'); ?>
		<br/><i style="font-size:12px;">Sale</i>
		</th>
		<?php
		if ($_show_pay_) {
		?>
		<th><?php echo $this->ExPaginator->sort('ViewTStats.earnings', 'Earnings'); ?></th>
		<th><?php echo $this->ExPaginator->sort('ViewTStats.payouts', 'Payouts'); ?></th>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<th <?php echo in_array($selsite, array(-1, -2)) ? 'class="naClassHide"' : ''; // just do not show for the some site?>>Payments</th>
		<?php
		}
		?>
	</tr>
	</thead>
	<?php
	$pagetotals = array(
		'raws' => 0, 'uniques' => 0, 'chargebacks' => 0, 'signups' => 0, 'frauds' => 0,
		'sales_type1' => 0, 'sales_type2' => 0, 'sales_type3' => 0, 'sales_type4' => 0,
		'sales_type5' => 0, 'sales_type6' => 0, 'sales_type7' => 0, 'sales_type8' => 0,
		'sales_type9' => 0, 'sales_type10' => 0,
		'net' => 0, 'payouts' => 0, 'earnings' => 0
	);
	$i = 0;
	foreach ($rs as $r) {
		$pagetotals['raws'] += $r['ViewTStats']['raws'];
		$pagetotals['uniques'] += $r['ViewTStats']['uniques'];
		$pagetotals['chargebacks'] += $r['ViewTStats']['chargebacks'];
		$pagetotals['signups'] += $r['ViewTStats']['signups'];
		$pagetotals['frauds'] += $r['ViewTStats']['frauds'];
		$pagetotals['sales_type1'] += $r['ViewTStats']['sales_type1'];
		$pagetotals['sales_type2'] += $r['ViewTStats']['sales_type2'];
		$pagetotals['sales_type3'] += $r['ViewTStats']['sales_type3'];
		$pagetotals['sales_type4'] += $r['ViewTStats']['sales_type4'];
		$pagetotals['sales_type5'] += $r['ViewTStats']['sales_type5'];
		$pagetotals['sales_type6'] += $r['ViewTStats']['sales_type6'];
		$pagetotals['sales_type7'] += $r['ViewTStats']['sales_type7'];
		$pagetotals['sales_type8'] += $r['ViewTStats']['sales_type8'];
		$pagetotals['sales_type9'] += $r['ViewTStats']['sales_type9'];
		$pagetotals['sales_type10'] += $r['ViewTStats']['sales_type10'];
		$pagetotals['net'] += $r['ViewTStats']['net'];
		$pagetotals['payouts'] += $r['ViewTStats']['payouts'];
		$pagetotals['earnings'] += $r['ViewTStats']['earnings'];
	?>
	<tr<?php echo ($i % 2 == 0 ? '' : ' class="odd"'); ?>>
		<td><font size="1"><?php echo ($i + 1 + $limit * ($this->Paginator->current() - 1)); ?></font></td>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<td>'
					. $this->Html->link(
							substr($r['ViewTStats']['trxtime'], 0, 10),
							array('controller' => 'stats', 'action' => 'statscompany',
								'startdate' => substr($r['ViewTStats']['trxtime'], 0, 10),
								'enddate' => substr($r['ViewTStats']['trxtime'], 0, 10),
								'siteid' => $selsite,
								'typeid' => $seltype,
								'companyid' => empty($selcoms) ? implode(',', array_keys($coms)) : implode(',', $selcoms),
								'agentid' => $selagent
							)
						)
					. '</td>';
				break;
			case 1:
				echo '<td>'
					. $this->Html->link(
						$r['ViewTStats']['officename'],
						array('controller' => 'stats', 'action' => 'statsagent',
							'startdate' => $startdate,
							'enddate' => $enddate,
							'siteid' => $selsite,
							'typeid' => $seltype,
							'companyid' => $r['ViewTStats']['companyid'],
							'agentid' => $selagent
						)
					)
					. '</td>';
				break;
			case 2:
				echo '<td>'
					/*
					. $r['ViewTStats']['username']
					*/
					. $this->Html->link(
						$r['ViewTStats']['username'],
						array('controller' => 'stats', 'action' => 'statsagdetail',
							'startdate' => $startdate,
							'enddate' => $enddate,
							'siteid' => $selsite,
							'typeid' => $seltype,
							'companyid' => empty($selcoms) ? implode(',', array_keys($coms)) : implode(',', $selcoms),
							'agentid' => $r['ViewTStats']['agentid']
						)
					)
					. '&nbsp;(' . $r['ViewTStats']['ag1stname'] . '&nbsp;' . $r['ViewTStats']['aglastname'] . ')'
					. '</td>';
				echo '<td>' . $r['ViewTStats']['officename'] . '</td>';
				break;
			case 3:
				echo '<td>' . substr($r['ViewTStats']['trxtime'], 0, 10) . '</td>';
				echo '<td>' . $r['ViewTStats']['officename'] . '</td>';
				echo '<td>' . $r['ViewTStats']['username'] . '&nbsp;(' . $r['ViewTStats']['ag1stname'] . '&nbsp;' . $r['ViewTStats']['aglastname'] . ')' . '</td>';
				break;
			default:
				echo '<td></td>';
				break;
		}
		?>
		<td><?php echo $r['ViewTStats']['raws']; ?></td>
		<td><?php echo $r['ViewTStats']['uniques']; ?></td>
		<td><?php echo $r['ViewTStats']['signups']; ?></td>
		<td><?php echo $r['ViewTStats']['frauds']; ?></td>
		<td><?php echo $r['ViewTStats']['chargebacks']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type10']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type9']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type8']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type7']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type6']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type5']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type4']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type3']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type2']; ?></td>
		<td><?php echo $r['ViewTStats']['sales_type1']; ?></td>
		<td><?php echo $r['ViewTStats']['net']; ?></td>
		<?php
		if ($_show_pay_) {
		?>
		<td><?php echo '$' . $r['ViewTStats']['earnings']; ?></td>
		<td><?php echo '$' . $r['ViewTStats']['payouts']; ?></td>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<td><?php echo '$' . ($r['ViewTStats']['earnings'] - $r['ViewTStats']['payouts'])?></td>
		<?php
		}
		?>
	</tr>
	<?php
		$i++;
	}
	?>
	<tr>
		<td class="totals"></td>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<td class="totals" align="right">Page Total</td>';
				break;
			case 1:
				echo '<td class="totals" align="right">Page Total</td>';
				break;
			case 2:
				echo '<td class="totals" align="right">Page Total</td>';
				echo '<td class="totals"></td>';
				break;
			case 3:
				echo '<td class="totals" align="right">Page Total</td>';
				echo '<td class="totals"></td>';
				echo '<td class="totals"></td>';
				break;
			default:
				echo '<td class="totals"></td>';
				break;
		}
		?>
		<td class="totals"><?php echo $pagetotals['raws']; ?></td>
		<td class="totals"><?php echo $pagetotals['uniques']; ?></td>
		<td class="totals"><?php echo $pagetotals['signups']; ?></td>
		<td class="totals"><?php echo $pagetotals['frauds']; ?></td>
		<td class="totals"><?php echo $pagetotals['chargebacks']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type10']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type9']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type8']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type7']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type6']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type5']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type4']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type3']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type2']; ?></td>
		<td class="totals"><?php echo $pagetotals['sales_type1']; ?></td>
		<td class="totals"><?php echo $pagetotals['net']; ?></td>
		<?php
		if ($_show_pay_) {
		?>
		<td class="totals"><?php echo '$' . sprintf('%.2f', $pagetotals['earnings']); ?></td>
		<td class="totals"><?php echo '$' . sprintf('%.2f', $pagetotals['payouts']); ?></td>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<td class="totals"><?php echo '$' . sprintf('%.2f', ($pagetotals['earnings'] - $pagetotals['payouts'])); ?></td>
		<?php
		}
		?>
	</tr>
	<tr>
		<td class="totals"></td>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<td class="totals" align="right">Overall Total</td>';
				break;
			case 1:
				echo '<td class="totals" align="right">Overall Total</td>';
				break;
			case 2:
				echo '<td class="totals" align="right">Overall Total</td>';
				echo '<td class="totals"></td>';
				break;
			case 3:
				echo '<td class="totals" align="right">Overall Total</td>';
				echo '<td class="totals"></td>';
				echo '<td class="totals"></td>';
				break;
			default:
				echo '<td class="totals"></td>';
				break;
		}
		?>
		<td class="totals"><?php echo $totals['raws']; ?></td>
		<td class="totals"><?php echo $totals['uniques']; ?></td>
		<td class="totals"><?php echo $totals['signups']; ?></td>
		<td class="totals"><?php echo $totals['frauds']; ?></td>
		<td class="totals"><?php echo $totals['chargebacks']; ?></td>
		<td class="totals"><?php echo $totals['sales_type10']; ?></td>
		<td class="totals"><?php echo $totals['sales_type9']; ?></td>
		<td class="totals"><?php echo $totals['sales_type8']; ?></td>
		<td class="totals"><?php echo $totals['sales_type7']; ?></td>
		<td class="totals"><?php echo $totals['sales_type6']; ?></td>
		<td class="totals"><?php echo $totals['sales_type5']; ?></td>
		<td class="totals"><?php echo $totals['sales_type4']; ?></td>
		<td class="totals"><?php echo $totals['sales_type3']; ?></td>
		<td class="totals"><?php echo $totals['sales_type2']; ?></td>
		<td class="totals"><?php echo $totals['sales_type1']; ?></td>
		<td class="totals"><?php echo $totals['net']; ?></td>
		<?php
		if ($_show_pay_) {
		?>
		<td class="totals"><?php echo '$' . sprintf('%.2f', $totals['earnings']); ?></td>
		<td class="totals"><?php echo '$' . sprintf('%.2f', $totals['payouts']); ?></td>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<td class="totals"><?php echo '$' . sprintf('%.2f', ($totals['earnings'] - $totals['payouts'])); ?></td>
		<?php 
		}
		?>
	</tr>
	<tr>
		<td class="totals"></td>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<td class="totals" align="right">Unique to Sale Ratio</td>';
				break;
			case 1:
				echo '<td class="totals" align="right">Unique to Sale Ratio</td>';
				break;
			case 2:
				echo '<td class="totals" align="right">Unique to Sale Ratio</td>';
				echo '<td class="totals"></td>';
				break;
			case 3:
				echo '<td class="totals" align="right">Unique to Sale Ratio</td>';
				echo '<td class="totals"></td>';
				echo '<td class="totals"></td>';
				break;
			default:
				echo '<td class="totals"></td>';
				break;
		}
		?>
		<td class="totals"></td>
		<td class="totals">
		<?php
		$sales_total = $totals['sales_type1'] + $totals['sales_type2'] + $totals['sales_type3'] + $totals['sales_type4']
			+ $totals['sales_type5'] + $totals['sales_type6'] + $totals['sales_type7'] + $totals['sales_type8']
			+ $totals['sales_type9'] + $totals['sales_type10'];
		if ($sales_total) {
			echo '1:' . sprintf('%.2f', $totals['uniques'] / $sales_total);
		} else {
			echo '-';
		}
		?>
		</td>
		<!--<td class="totals"></td>-->
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<?php
		if ($_show_pay_) {
		?>
		<td class="totals"></td>
		<td class="totals"></td>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<td class="totals"></td>
		<?php 
		}
		?>
	</tr>
	<tr>
		<td class="totals"></td>
		<?php
		switch ($bywhat) {
			case 0:
				echo '<td class="totals" align="right">Signup to Sale Ratio</td>';
				break;
			case 1:
				echo '<td class="totals" align="right">Signup to Sale Ratio</td>';
				break;
			case 2:
				echo '<td class="totals" align="right">Signup to Sale Ratio</td>';
				echo '<td class="totals"></td>';
				break;
			case 3:
				echo '<td class="totals" align="right">Signup to Sale Ratio</td>';
				echo '<td class="totals"></td>';
				echo '<td class="totals"></td>';
				break;
			default:
				echo '<td class="totals"></td>';
				break;
		}
		?>
		<td class="totals"></td>
		<td class="totals">
		<?php
		if ($sales_total) {
			echo '1:' . sprintf('%.2f', $totals['signups'] / $sales_total);
		} else {
			echo '-';
		}
		?>
		</td>
		<!--<td class="totals"></td>-->
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<td class="totals"></td>
		<?php
		if ($_show_pay_) {
		?>
		<td class="totals"></td>
		<td class="totals"></td>
		<?php
		} else if ($userinfo['role'] == -1) {
		?>
		<td class="totals"></td>
		<?php 
		}
		?>
	</tr>
</table>

<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<?php
echo $this->element('paginationblock');
?>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<?php
} else {
?>
<b><font color="red">No stats under the conditions you picked.</font></b>
<?php
}
?>

<script type="text/javascript">
jQuery(document).ready(function(){
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
