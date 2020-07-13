<h1>Profile</h1>
<br/>
<?php
$userinfo = $this->Session->read('Auth.User.Account');
echo $this->Form->create(
	null, 
	array(
		'url' => array('controller' => 'accounts', 'action' => 'regadmin')
	)
);
?>
<table style="width:100%">
	<caption>Fields marked with an asterisk (*) are required.</caption>
	<tr>
		<td>Username : </td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('Account.username', 
			array(
				'label' => '', 
				'style' => 'width:390px;'
			)
		);
		?>
		</div>
		<div style="float:left"><font color="red">*</font></div>
		</td>
		</td>
	</tr>
	<tr>
		<td>Password : </td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('Account.password', array('label' => '', 'type' => 'password', 'style' => 'width:390px;'));
		?>
		</div>
		<div style="float:left"><font color="red">*</font></div>
		</td>
	</tr>
	<tr>
		<td>Confirm password :</td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('Account.originalpwd', array('label' => '', 'type' => 'password', 'style' => 'width:390px;'));
		?>
		</div>
		<div style="float:left"><font color="red">*</font></div>
		</td>
	</tr>
	<tr>
		<td>Email Address :</td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('Admin.email', array('label' => '', 'style' => 'width:390px;'));
		?>
		</div>
		<div style="float:left"><font color="red">*</font></div>
		</td>
	</tr>
	<?php 
	if (in_array($userinfo['id'], array(1, 2))) {
	?>
	<tr>
		<td>
		<label>Earning visible :</label>
		<?php
		echo $this->Form->checkbox('Account.level');
		?>
		</td>
		<td>
		<div style="float:left"><font color="red">*</font></div>
		</td>
	</tr>
	<?php 
	}
	?>
	<tr>
		<td></td>
		<td><?php echo $this->Form->submit('Add', array('style' => 'width:112px;')); ?></td>
	</tr>
</table>
<?php
echo $this->Form->input('Account.id', array('type' => 'hidden'));
echo $this->Form->input('Account.role', array('type' => 'hidden', 'value' => 0));
echo $this->Form->input('Account.status', array('type' => 'hidden', 'value' => 1));
echo $this->Form->input('Admin.id', array('type' => 'hidden'));
echo $this->Form->input('Admin.notes', array('type' => 'hidden', 'value' => ''));
echo $this->Form->end();
?>

<script type="text/javascript">
jQuery(":checkbox").attr({
	style: "border: 0px; width: 16px; margin-left: 2px; vertical-align: middle;"
});
</script>