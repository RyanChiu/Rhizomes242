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
<body style="background:white;">
	<div class="wrapper">
		<!-- Start Border-->
		<div id="border">
			<div style="text-align:center">
				<b><font color="red"><?php echo $this->Session->flash(); ?></font> </b>
			</div>
			
			<!-- Start log in table layout -->
			<table style="border:0;background:white;">
				<tbody>
					<tr>
						<td  style="vertical-align:top;">
							<?php 
							echo $this->Html->image(
								'GLOBALNETTXT.jpg', array('style' => 'border:0;')
							);
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php 
							echo $this->Html->image(
								'HEADER.jpg', array('style' => 'border:0;width:100%;')
							);
							?>
						</td>
					</tr>
					<tr>
						<td style="width:100%;">
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
			<div style="background:black;color:white;padding:6px;">
			Our affiliate program is based on a years of success in the adult industry advertising. 
			Our understanding of the affilaite needs, Years of marketing experience has allowed us 
			to create the most advanced Internet affiliate program in the industry. 
			<a href="www.globalnetadvertising.com">www.globalnetadvertising.com</a> takes affiliate 
			marketing programs to the next level.
			</div>
			<div style="background:white;color:black;font-size:1;font-weight:bold;">
			<center>
				<br/>De Kleetlaan 12a 2331 Diegem Brussels Belgium<br/>
				Copyright &copy; 2016 <a href="www.globalnetadvertising.com">www.globalnetadvertising.com</a> All Rights Reserved.
			</center>
			</div>
		</div>
		<!-- End Footer -->
	</div>
	
</body>
</html>
