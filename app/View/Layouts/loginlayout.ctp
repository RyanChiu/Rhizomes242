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
								'main/bamboo.jpg', array('style' => 'border:0px;height:170px;margin-left:45px;')
							);
							?>
						</td>
						<td>
							<?php 
							echo $this->Html->image(
								'main/banner.jpg', array('style' => 'border:0px;height:120px;')
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
			<div style="color:#99cc66;font-size:1;float:left;">
				2333 Dean Path, Edinburgh, EH3 7DX, UK
			</div>
			<div style="color:#99cc66;font-size:1;float:right;">
				Copyright &copy; 2016 www.TheRhizomes.com All Rights Reserved.
			</div>
		</div>
		<!-- End Footer -->
	</div>
	
</body>
</html>
