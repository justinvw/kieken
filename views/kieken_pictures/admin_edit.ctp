<script type="text/javascript" charset="utf-8">
	$('#picture-details-popup .submit').click(function(){
		if($('#picture-details-popup #KiekenPictureStatus').is(':checked')){
			picturestatus = 1;
		}
		else {
			picturestatus = 0;
		}
		
		var selectedalbums = new Array();
		$('#picture-details-popup #KiekenAlbum :checked').each(function(){
			selectedalbums.push($(this).val());
		});

		options = {
			'KiekenPicture': {
				'id': $('#picture-details-popup #KiekenPictureId').val(),
				'title': $('#picture-details-popup #KiekenPictureTitle').val(),
				'description': $('#picture-details-popup #KiekenPictureDescription').val(),
				'licence': $('#picture-details-popup #KiekenPictureLicense').val(),
				'status': picturestatus,
				'description': $('#picture-details-popup #KiekenPictureDescription').val(),
			},
			'KiekenAlbum': {
				'KiekenAlbum': selectedalbums
			}
		}
		
		editPictureDetails(options)
		return false;
	});
</script>
<div id="picture-details-popup" class="container_13">
	<div class="grid_6">
		<?php echo $html->image(DS.Configure::read('Kieken.uploadDirectory').$picture['KiekenFile']['medium']['filename'], array('class' => 'thumbnail', 'width' => '360px')); ?>
		<table>
			<tr>
				<td>Uploaded:</td>
				<td><?php echo $time->niceShort($picture['KiekenPicture']['created']); ?></td>
			</tr>
			<tr>
				<td>Edited:</td>
				<td><?php echo $time->niceShort($picture['KiekenPicture']['updated']); ?></td>
			</tr>
			<tr>
				<td>Available formats:</td>
				<td>
					<ul>
						<?php foreach($picture['KiekenFile'] as $file): ?>
							<li><?php echo $file['thumbname']; ?> (<?php echo $file['width'].'x'.$file['height']; ?>)</li>
						<?php endforeach; ?>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<div class="grid_7">
		<?php echo $form->create('KiekenPicture', array('url' => array('action' => 'edit')));?>
			<fieldset>
				<?php
					echo $form->input('id');
					echo $form->input('title');
					echo $form->input('description');
					echo $form->input('license', array('label' => 'Licence information'));
					echo $form->input('status');
					echo $form->input('KiekenAlbum.id', array('div' => array('id' => 'KiekenAlbum'), 'label' => 'Albums', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $albums, 'selected' => array_keys($picture['KiekenAlbum'])));
				?>
			</fieldset>
		<?php echo $form->end('Submit'); ?>
	</div>
</div>