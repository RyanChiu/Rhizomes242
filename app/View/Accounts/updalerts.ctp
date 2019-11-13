<h1>Update A Popup</h1>
<?php
//echo print_r($results, true);
$userinfo = $this->Session->read('Auth.User.Account');
echo $this->Form->create(null, array('url' => array('controller' => 'accounts', 'action' => 'updalerts')));
?>
<table style="width:100%">
	<tr>
		<td align="center">
		Content
		</td>
		<td>
		<div style="float:left">
		<?php
		echo $this->Form->input('Admin.notes', array('label' => '', 'rows' => '30', 'cols' => '80'));
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
echo $this->Form->input('Admin.id', array('type' => 'hidden'));
echo $this->Form->end();
?>

<script type="text/javascript">
	CKEDITOR.replace('AdminNotes',
		{
	        filebrowserUploadUrl : '/GNA/accounts/upload',
	        filebrowserWindowWidth : '640',
	        filebrowserWindowHeight : '480'
	    }
	);
	CKEDITOR.config.height = '300px';
	CKEDITOR.config.width = '830px';
	CKEDITOR.config.resize_maxWidth = '830px';
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
