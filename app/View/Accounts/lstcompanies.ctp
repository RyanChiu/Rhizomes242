<h1>Companies</h1>
<?php
//echo '<br/>';
//echo print_r($rs, true);
?>
<?php
/*searching part*/
?>
<div style="width:100%;margin-top:5px;" id="search">
<?php
echo $this->Form->create(
	null, 
	array(
		"url" => array('controller' => 'accounts', 'action' => 'lstcompanies'), '
		id' => 'frmSearch'
	)
);
?>
<table style="width:100%;border:0;">
	<caption>
	<?php echo $this->Html->image('iconSearch.png', array('style' => 'width:16px;height:16px;')) . 'Search'; ?>
	</caption>
	<tr>
		<td class="search-label" style="width:105px;">Username:</td>
		<td>
		<div style="float:left;width:275px;">
		<?php echo $this->Form->input('ViewCompany.username', array('label' => '', 'style' => 'width:260px;')); ?>
		</div>
		<div style="float:left;width:112px;">
		<?php echo $this->Form->submit('Search', array('style' => 'float:left;width:96px;')); ?>
		</div>
		<div style="float:left;">
		<?php echo $this->Form->submit('Clear', array('style' => 'float:left;width:64px;', 'onclick' => 'javascript:__zClearForm("frmSearch");')); ?>
		</div>
		</td>
	</tr>
</table>
<?php
echo $this->Form->end();
?>
</div>
<br/>

<?php
/*showing the results*/
?>
<script type="text/javascript">
function __setActSusLink() {
	var checkboxes;
	checkboxes = document.getElementsByName("data[ViewCompany][selected]");
	var ids = "";
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked && checkboxes[i].value != 0) {
			ids += checkboxes[i].value + ",";
		}
	}
	document.getElementById("linkActivateSelected").href =
		document.getElementById("linkActivateSelected_").href + "/ids:" + ids + "/status:1/from:0";
	document.getElementById("linkSuspendSelected").href =
		document.getElementById("linkSuspendSelected_").href + "/ids:" + ids + "/status:0/from:0";
}
function __setCurSelectedToBeInformed() {
	var checkboxes;
	checkboxes = document.getElementsByName("data[ViewCompany][selected]");
	var ids = "";
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked && checkboxes[i].value != 0) {
			ids += checkboxes[i].value + ",";
		}
	}
	document.getElementById("hidCurSelectedToBeInformed").value = ids;
}
function __setInfLink() {
	document.getElementById("linkInform").href =
		document.getElementById("linkInform_").href + "/from:0"
			+ "/ids:" + document.getElementById("hidCurSelectedToBeInformed").value
			+ "/notes:" + document.getElementById("txtNotes").value;
}
function __checkAll() {
	var checkboxes;
	checkboxes = document.getElementsByName("data[ViewCompany][selected]");
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = document.getElementById("checkboxAll").checked;
	}
}
</script>

<div style="margin-bottom:3px">
<?php
echo $this->Form->button('Add Office',
	array(
		'onclick' => 'javascript:location.href=\''
			. $this->Html->url(array('controller' => 'accounts', 'action' => 'regcompany')) . '\'',
		'style' => 'width:160px;'
	)
);
?>
</div>
<table style="width:100%">
<thead>
<tr>
	<th><b>
	<?php
	echo $this->Form->checkbox('',
		array('id' => 'checkboxAll', 'value' => -1,
			'style' => 'border:0px;width:16px;',
			'onclick' => 'javascript:__checkAll();__setActSusLink();'
		)
	);
	?>
	</b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.officename', 'Office'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.agenttotal', 'Total Agents'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.username4m', 'Username'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.originalpwd', 'Password'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.manemail', 'Email'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.regtime', 'Registered'); ?></b></th>
	<th><b><?php echo $this->ExPaginator->sort('ViewCompany.status', 'Status'); ?></b></th>
	<th><b>Operation</b></th>
</tr>
</thead>
<?php
$i = 0;
foreach ($rs as $r):
?>
<tr <?php echo $i % 2 == 0 ? '' : 'class="odd"'; ?>>
	<td>
	<?php
	echo $this->Form->checkbox('ViewCompany.selected',
		array('value' => $r['ViewCompany']['companyid'],
			'style' => 'border:0px;width:16px;',
			'onclick' => 'javascript:__setActSusLink();'
		)
	);
	?>
	</td>
	<td>
	<?php
	/*
	echo $this->Html->link(
		$r['ViewCompany']['officename'],
		array('controller' => 'accounts', 'action' => 'lstagents', 'id' => $r['ViewCompany']['companyid']),
		array('title' => 'Click to the agents.')
	);
	*/
	echo $r['ViewCompany']['officename'];
	?>
	</td>
	<td align="center">
	<?php
	echo $this->Html->link(
		$r['ViewCompany']['agenttotal'] . '&nbsp;' . $this->Html->image('iconList.gif', array('border' => 0)),
		array('controller' => 'accounts', 'action' => 'lstagents', 'id' => $r['ViewCompany']['companyid']),
		array('title' => 'Click to the agents.', 'escape' => false),
		false
	);
	?>
	</td>
	<td><?php echo $r['ViewCompany']['username']; ?></td>
	<td><?php echo $r['ViewCompany']['originalpwd']; ?></td>
	<td><?php echo $r['ViewCompany']['manemail']; ?></td>
	<td><?php echo $r['ViewCompany']['regtime']; ?></td>
	<td><?php echo $status[$r['ViewCompany']['status']]; ?></td>
	<td align="center">
	<?php
	echo $this->Html->link(
		$this->Html->image('iconEdit.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;',
		array('controller' => 'accounts', 'action' => 'updcompany', 'id' => $r['ViewCompany']['companyid']),
		array('title' => 'Click to edit this record.', 'escape' => false),
		false
	);
	echo $this->Html->link(
		$this->Html->image('iconActivate.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;',
		array('controller' => 'accounts', 'action' => 'activatem', 'ids' => $r['ViewCompany']['companyid'], 'status' => 1, 'from' => 0),
		array('title' => 'Click to activate the user.', 'escape' => false),
		false
	);
	echo $this->Html->link(
		$this->Html->image('iconSuspend.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;',
		array('controller' => 'accounts', 'action' => 'activatem', 'ids' => $r['ViewCompany']['companyid'], 'status' => 0, 'from' => 0),
		array('title' => 'Click to suspend the user.', 'escape' => false),
		"Are you sure?"
	);
	$iconEye = '';
	$statusEye = -2;
	$styleEye = 'hide';
	if ($r['ViewCompany']['status'] != -2) {
		$iconEye = 'eye-half-icon.png';
		$statusEye = -2;
		$styleEye = 'hide';
	} else {
		$iconEye = 'eye-icon.png';
		$statusEye = 1;
		$styleEye = 'show';
	}
	echo $this->Html->link(
		$this->Html->image($iconEye, array('border' => 0, 'width' => 20, 'height' => 20)) . '&nbsp;',
		array('controller' => 'accounts', 'action' => 'activatem', 'ids' => $r['ViewCompany']['companyid'], 'status' => $statusEye, 'from' => 0),
		array('title' => 'Click to ' . $styleEye . ' the office.', 'escape' => false),
		"Are you sure to " . $styleEye . " this one?"
	);
	?>
	</td>
</tr>
<?php
$i++;
endforeach;
?>
</table>

<div style="margin-top:3px;">
<font color="green">With selected :&nbsp;</font>
<?php
/*activate selected*/
echo $this->Html->link(
	$this->Html->image('iconActivate.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;&nbsp;',
	array('controller' => 'accounts', 'action' => 'activatem'),
	array('id' => 'linkActivateSelected', 'title' => 'Click to activate the selected users.', 'escape' => false),
	false
);
echo $this->Html->link(
	'',
	array('controller' => 'accounts', 'action' => 'activatem'),
	array('id' => 'linkActivateSelected_')
);
/*suspend selected*/
echo $this->Html->link(
	$this->Html->image('iconSuspend.png', array('border' => 0, 'width' => 16, 'height' => 16)) . '&nbsp;&nbsp;',
	array('controller' => 'accounts', 'action' => 'activatem'),
	array('id' => 'linkSuspendSelected', 'title' => 'Click to suspend the selected users.', 'escape' => false),
	"Are you sure?"
);
echo $this->Html->link(
	'',
	array('controller' => 'accounts', 'action' => 'activatem'),
	array('id' => 'linkSuspendSelected_')
);
/*inform selected --*/
/*undim this block to function it
echo $this->Html->link(
	$this->Html->image('iconInform.png',
		array('id' => 'open_message',
			'border' => 0, 'width' => 16, 'height' => 16,
			'onclick' => 'javascript:__setCurSelectedToBeInformed();__setInfLink();'
		)
	),
	'#',
	array('title' => 'Click to inform the selected users.', 'escape' => false),
	false
);
*/
?>
</div>

<!-- ~~~~~~~~~~~~~~~~~~~the floating message box for "inform selected"~~~~~~~~~~~~~~~~~~~ -->
<div id="message_box">
	<table style="width:100%">
	<thead><tr><th>
		<div style="float:left">Please enter your notes below.</div>
		<?php echo $this->Html->image('iconClose.png', array('id' => 'close_message', 'style' => 'float:right;cursor:pointer')); ?>
	</th></tr></thead>
	<tr><td><textarea id="txtNotes" style="width:99%" rows="6" onchange="javascript:__setInfLink();"></textarea></td></tr>
	<tr><td>
		<?php
		echo $this->Form->input('', array('type' => 'hidden', 'id' => 'hidCurSelectedToBeInformed'));
		echo $this->Html->link(
			'',
			array('controller' => 'accounts', 'action' => 'informem'),
			array('id' => 'linkInform_')
		);
		echo $this->Html->link(
			'Inform',
			array('controller' => 'accounts', 'action' => 'informem'),
			array('id' => 'linkInform'),
			false
		);
		?>
	</td></tr>
	</table>
</div>

<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<?php
echo $this->element('paginationblock');
?>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->