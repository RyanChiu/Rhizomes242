<center>
	<b><font color="red"><?php echo $this->Session->flash('auth'); ?> </font> </b>
	<font color="red"><?php echo $this->Session->flash(); ?> </font>
</center>
<?php
echo $this->Form->create(null, array('url' => array('controller' => 'accounts', 'action' => 'login')));
?>
<table style="border:0;width:100%;">
	<tr>
		<td align="right">
			<b><font style="color:#0066ff;margin-left:145px;" size="2">USER:</font></b>
		</td>
		<td align="left">
			<?php
			echo $this->Form->input('Account.username', array('label' => '', 'style' => 'width:112px;'));
			?> 
			<script type="text/javascript">
			jQuery("#AccountUsername").focus();
			</script>
		</td>
	</tr>
	<tr>
		<td align="right">
			<b><font style="color:#0066ff;margin-left:145px;" size="2">PASS:</font></b>
		</td>
		<td align="left">
			<?php
			echo $this->Form->input(
				'Account.password', 
				array('label' => '', 'style' => 'width:112px;margin-top:8px;', 'type' => 'password')
			);
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<b><font style="color:#0066ff;margin-left:145px;" size="2">VERIFY HUMAN:</font></b>
		</td>
		<td align="left">
			<div style="float:left;margin-right:10px;">
				<?php
				echo $this->Form->input(
					'Account.vcode', 
					array(
						'label' => '', 
						'style' => 'width:112px;', 
						'div' => array('style' => 'margin-top:8px;')
					)
				);
				?>
			</div>
			<div style="float: left;">
				<script type="text/javascript">
				function __chgVcodes() {
					document.getElementById("imgVcodes").src =
						"<?php echo $this->Html->url(array('controller' => 'accounts', 'action' => 'phpcaptcha')); ?>"
						+ "?" + Math.random();
				}
				</script>
				<?php
				echo $this->Html->link(
						$this->Html->image(array('controller' => 'accounts', 'action' => 'phpcaptcha'),
								array('style' => 'width:100px; height:35; border: 1px solid #222222;', 'id' => 'imgVcodes', 'onclick' => 'javascript:__chgVcodes();')
						),
						'#',
						array('escape' => false, 'title' => 'Click to try another one.(By entering this code you help yourself prevent spam and fake login.)'),
						false
				);
				?>
			</div>
			<!--
			<div style="float:left;">
				<div id="playPhpcaptcha">
					<object type="application/x-shockwave-flash"
						data="../img/securimage_play.swf?bgcol=#ffffff&amp;icon_file=../img/audio_icon.png&amp;audio_file=<?php
							echo $this->Html->url(array('controller' => 'accounts', 'action' => 'playPhpcaptcha')); 
						?>"
						style="width: 35px; height: 35px; border: 1px solid #666666; margin-top: 1px; margin-left: 2px;">
						<param name="movie"
							value="../img/securimage_play.swf?bgcol=#ffffff&amp;icon_file=../img/audio_icon.png&amp;audio_file=<?php
								echo $this->Html->url(array('controller' => 'accounts', 'action' => 'playPhpcaptcha')); 
							?>"/>
					</object>
				</div>
			</div>
			-->
		</td>
	</tr>
	<!--
	<tr>
		<td colspan=2 style="padding-left:356px;color:#0066ff;">Example: 2+4=6, the captcha answer is 6.</td>
	</tr>
	-->
	<tr>
		<td colspan="2" style="padding-left:0;">
		<br/>
		<div style="width:100%;height:35px;text-align:center;">
		<?php
		echo $this->Form->submit('ENTER', array('style' => 'border:0;width:150px;color:#0066ff;font-size:16px;background:white;text-decoration:underline;'));
		?>
		</div>
		</td>
	</tr>
	<!--
	<tr>
		<td  colspan="2" style="padding-left:70px;">
			<br/> 
			<?php
			echo $this->Html->link(
					'<b><font size="2">Lost your password?</font></b>',
					array('controller' => 'accounts', 'action' => 'forgotpwd'),
					array('escape' => false), false
			);
			?>
			<br/><br/>
			<?php
			echo $this->Html->image("main/money.png", array('style' => "border:0;margin-top:7px;width:450px;float:left;"));
			?>
		</td>
	</tr>
	-->
</table>
<?php
echo $this->Form->end();
?>

<div style="margin: 0px 15px 0px 15px">
	<?php
	echo $this->element('frauddefblock');
	?>
</div>

<script type="text/javascript">
for (var i = 0; i < 10; i++) {
	jQuery(".suspended-warning").animate({opacity: 'toggle'}, "slow");
}
</script>
