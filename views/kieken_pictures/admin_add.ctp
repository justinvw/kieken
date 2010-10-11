<?php
	echo $html->script('/kieken/js/fileuploader.js', array('inline' => false));
	echo $html->css('/kieken/css/fileuploader.css', 'stylesheet', array('inline' => false));
?>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	var uploader = new qq.FileUploader({
	    element: $('#file-uploader')[0],
	    action: Croogo.basePath + 'admin/kieken/kieken_pictures/upload',
		onSubmit: function () {
			$('#album').attr('disabled', 'disabled');
		},
		onComplete: function () {
			$('#album').removeAttr('disabled');
		}
	});
	
	uploader.setParams({ album: $('#album').val()});
	$('#album').change(function(){
		uploader.setParams({ album: $('#album').val()});
	});
});
</script>
<div class="kieken_add pictures">
	<h2><?php echo $title_for_layout; ?></h2>
	<form>
		<fieldset>
			<?php echo $form->input('album', array('type' => 'select', 'options' => $albums, 'selected' => $album_id, 'empty' => true));?>
		</fieldset>
	</form>
	<div id="file-uploader">       
	    <noscript>          
	        <p>Please enable JavaScript to use file uploader.</p>
	    </noscript>         
	</div>
</div>