<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
<title><?php echo $title_for_layout; ?>
</title>
<?php
echo $this->Html->meta('icon', $this->Html->url('/../fav.png'));
/*for default whole page layout*/
echo $this->Html->css('main');

/*for jQuery*/
echo $this->Html->script('jQuery/Datepicker/jquery-1.3.2.min');

echo $scripts_for_layout;

?>
</head>
<body bgcolor="#ffffff">
	<div class="wrapper">
		<!-- Start Border-->
		<div id="border">
			<div style="text-align:center">
				<b><font color="red"><?php echo $this->Session->flash(); ?></font> </b>
			</div>
			
			<!-- Start log in table layout -->
			<table style="border:0">
				<tbody>
					<tr>
						<td rowspan=2 style="vertical-align:top;">
							<?php 
							echo $this->Html->image(
								'main/potatoes.gif', array('style' => 'border:0px;height:170px;margin-left:45px;')
							);
							?>
						</td>
						<td>
							<?php 
							echo $this->Html->image(
								'main/crushedpotato.jpg', array('style' => 'border:0px;height:60px;')
							);
							?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;width:100%;">
							<?php echo $content_for_layout; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- End log in table layout -->
			
		</div>
		<!-- End Border -->
		<!-- Start Footer -->
		<div id="footer">
			<div style="color:white;font-size:1;float:left;">
				378 Fleet Street, London, EC4A 2AG
			</div>
			<div style="color:white;font-size:1;float:right;">
				Copyright &copy; 2019-2020 www.CrushedPotato.com All Rights Reserved.
			</div>
		</div>
		<!-- End Footer -->
	</div>
	
</body>
</html>
