<?php
	echo $html->script('/kieken/js/jeditable.js', array('inline' => false));
?>
<script type="text/javascript" charset="utf-8">
	function setAsAlbumThumbnail(id, album_id){
		options = {
			'data': {
				'id': album_id,
				'thumbnail_picture_id': id
			}	
		}
		$.ajax({
			url: Croogo.basePath + 'admin/kieken/kieken_albums/edit',
			data: $.param(options),
			type: 'POST',
			success: function (j) {
				
			}
		});
	}
	
	function removePicture(id, album_id){
		$('#dialog-confirm').dialog({
			resizable: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			draggable: false,
			buttons: {
				'Remove from this album' : function(){
					window.location = Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + '/album/' + album_id;
				},
				'Remove from all albums' : function(){
					window.location = Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + '/albums/' + album_id;
				},
				'Entirely delete' : function (){
					window.location = Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + 'all/' + album_id;
				},
				'Cancel' : function (){
					$(this).dialog('close');
				}
			}
		});
		$('#dialog-confirm').dialog('open');
	}
	
	$(document).ready(function() {
		$('a.delete').click(function(){
			removePicture($(this).attr('id'), <?php echo $album['KiekenAlbum']['id']; ?>);
			return false;
		});
		
		$('a.setasalbumthumbnail').click(function(){
			setAsAlbumThumbnail($(this).attr('id'), <?php echo $album['KiekenAlbum']['id']; ?>);
			return false;
		});
				
		$('.edit_title').editable(Croogo.basePath + 'admin/kieken/kieken_pictures/edit', { 
			type: 'text',
			cancel: 'Cancel',
			submit : 'Save',
			name: 'title',
			submitdata : { edit: "title" },
			indicator: 'Saving...',
			tooltip: 'Click to edit...'
		});
		
		$('.edit_description').editable(Croogo.basePath + 'admin/kieken/kieken_pictures/edit', { 
			type: 'text',
			cancel: 'Cancel',
			submit : 'Save',
			name: 'description',
			submitdata : { edit: "description" },
			indicator: 'Saving...',
			tooltip: 'Click to edit...'
		});
	});
</script>
<div id="dialog-confirm" title="Delete picture" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 7px 0;"></span>Are you sure you want to delete this picture?</p>
</div>
<div class="kieken_album view">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php if(count($album['KiekenPicture']) == 0): ?>
		<div id="no-pictures">
			<p><?php __('This album does not contain any pictures.'); ?></p>
			<?php echo $html->link(__('Upload pictures', true), array('controller' => 'kieken_pictures', 'action' => 'add', $album['KiekenAlbum']['id'])); ?>
			<?php echo $html->link(__('Add existing pictures', true), array('controller' => 'kieken_pictures', 'action' => 'index')); ?>
		</div>
	<?php else: ?>
	<div id="album-photos">
		<table>
			<tbody>
				<tr>
				<?php
					$count = 0;
					foreach($album['KiekenPicture'] as $picture):
					$count++;
				?>
						<td>
							<h4 class="editable edit_title" id="<?php echo $picture['id']; ?>"><?php echo $picture['title']; ?></h4>
							<?php echo $html->image(DS.Configure::read('Kieken.uploadDirectory').$picture['KiekenFile']['small']['filename'], array('width' => '200px')); ?>
							<p class="editable edit_description" id="<?php echo $picture['id']; ?>"><?php 
									if(!empty($picture['description'])) {
										echo $picture['description'];
									}
									else {
										echo __('(no description)', true);
									}
							?></p>
							<ul class="actions">
								<li><?php echo $html->link(__('Delete', true), array(
											'controller' => 'kieken_pictures', 
											'action' => 'delete', 
											$picture['id'], 
											'all'), 
										array('class' => 'delete', 'id' => $picture['id'])); ?></li>
								<li><?php echo $html->link(__('Use as thumbnail for this album', true), array(
											'controller' => 'kieken_pictures',
											'action' => 'edit',
											$album['KiekenAlbum']['id']),
										array('class' => 'setasalbumthumbnail', 'id' => $picture['id'])); ?></li>
							<ul>
						</td>
				<?
						if($count % 4 == 0){
							echo '</tr><tr>';
						}
					endforeach;
				?>
			</tbody>
		</table>
	</div>
	<?php endif;?>
</div>