<h1>
<?php echo $rs['Site']['sitename'] . ' Affiliate'; ?>
</h1>
<?php
/*
echo '<br/>';
echo print_r($url, true);
echo print_r($pass, true);
echo print_r($passedArgs, true);
*/
$userinfo = $this->Session->read('Auth.User.Account');
echo $this->Form->create(null, array('url' => array('controller' => 'accounts', 'action' => 'updtoolbox')));
?>
<table style="width:100%">
	<tr>
		<td align="center">
		Manual
		</td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('SiteManual.html', array('label' => '', 'rows' => '30', 'cols' => '80'));
		?>
		</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo $this->Form->submit('Update', array('style' => 'width:112px;')); ?></td>
	</tr>
</table>
<?php
echo $this->Form->input('SiteManual.id', array('type' => 'hidden'));
echo $this->Form->input('SiteManual.siteid', array('type' => 'hidden', 'value' => $rs['Site']['id']));
echo $this->Form->end();
?>

<script type="text/javascript">
	CKEDITOR.replace('SiteManualHtml',
		{
	        filebrowserUploadUrl : '/GNA/accounts/upload',
	        filebrowserWindowWidth : '640',
	        filebrowserWindowHeight : '480'
	    }
	);
	CKEDITOR.config.height = '630px';
	CKEDITOR.config.width = '840px';
	CKEDITOR.config.resize_maxWidth = '840px';
	CKEDITOR.config.toolbar =
		[
		    ['Source','-','NewPage','Preview','-','Templates'],
		    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
		    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		    '/',
		    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
		    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		    ['Link','Unlink','Anchor'],
		    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		    '/',
		    ['Styles','Format','Font','FontSize'],
		    ['TextColor','BGColor']
		];
</script>