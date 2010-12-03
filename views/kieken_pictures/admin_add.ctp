<?php
	echo $html->script('/kieken/js/fileuploader.js', array('inline' => false));
	echo $html->css('/kieken/css/fileuploader.css', 'stylesheet', array('inline' => false));
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		var askuser = false;
		window.onbeforeunload = confirmClose;

		function confirmClose(){
			if(askuser){
				return "You are uploading files, blahblah blah!";
			}
		}
		
		var uploader = new qq.FileUploader({
		    element: $('#file-uploader')[0],
		    action: Croogo.basePath + 'admin/kieken/kieken_pictures/upload',
			allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
			onSubmit: function(id, fileName){
				$('#album').attr('disabled', 'disabled');
			},
			onProgress: function(id, fileName, loaded, total){
				console.log(askuser)
				console.log('ik ben bezig!');
				askuser = true;
				console.log(askuser)
			},
			onComplete: function(id, fileName, responseJSON){
				$('#album').removeAttr('disabled');
				askuser = false;
				console.log(askuser)
			}
		});
	
		uploader.setParams({ album: $('#album').val()});
		
		$('#album').change(function(){
			uploader.setParams({ album: $('#album').val()});
		});
	});
</script>
<div id="dialog-confirm" title="Close window" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 7px 0;"></span>Are you sure you want to close this window?</p>
</div>
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