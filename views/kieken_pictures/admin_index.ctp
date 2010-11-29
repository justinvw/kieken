<?php
	echo $html->css('/kieken/css/kieken_admin.css', 'stylesheet', array('inline' => false));
	echo $html->script('/kieken/js/jeditable.js', array('inline' => false));
?>
<script type="text/javascript" charset="utf-8">
	function editPictureDetails(data){
		options = { 'data' : data }
		
		var returnvalue = null;
		$.ajax({
			url: Croogo.basePath + 'admin/kieken/kieken_pictures/edit',
			data: $.param(options),
			type: 'POST',
			async: false,
			success: function(data){
				returnvalue = data;
			}
		});
		
		return returnvalue;
	}
	
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
	
	function showImageDetails(id){
		dialog = $('<div title="Picture details"></div>');
		$(dialog).dialog({
			resizable: false,
			height: 'auto',
			position: 'center',
			width: '780px',
			modal: true,
			draggable: false,
			open: function(){
				$.ajax({
					url: Croogo.basePath + 'admin/kieken/kieken_pictures/edit/' + id,
					type: 'GET',
					success: function (j) {
						$(dialog).html(j);
						$(dialog).dialog('option', 'position', 'center');
					}
				});
			},
			close: function(){
				$(dialog).empty().remove();
			},
			buttons: {
				'Save image details' : function(){
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

					editPictureDetails(options);
					$(dialog).dialog('close').empty().remove();
					updateAlbumNav();
					updatePicturesContainer($('#pictures_container div').attr('id'));
				},
				'Cancel' : function(){
					$(dialog).dialog('close').empty().remove();
				}
			}
		});
		$(dialog).dialog('open');
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
					$.get(Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + '/album/' + album_id);
					$(this).dialog('close');
					$('td#' + id).fadeOut();
					updateAlbumNav();
				},
				'Remove from all albums' : function(){
					$.get(Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + '/albums/' + album_id);
					$(this).dialog('close');
					$('td#' + id).fadeOut();
					updateAlbumNav();
					
				},
				'Entirely delete' : function (){
					$.get(Croogo.basePath + 'admin/kieken/kieken_pictures/delete/' + id + '/all/' + album_id);
					$(this).dialog('close');
					$('td#' + id).fadeOut();
					updateAlbumNav();
				},
				'Cancel' : function (){
					$(this).dialog('close');
				}
			}
		});
		$('#dialog-confirm').dialog('open');
	}
		
	function updateAlbumNav(album){
		$.ajax({
			url: Croogo.basePath + 'admin/kieken/kieken_pictures/album_index',
			data: '',
			type: 'GET',
			success: function(j){
				$('#album_nav #album_index').html(j);
				$('#album_nav #album_index a[href="#'+album+'"]').addClass('selected');
				$('#album_nav #album_index a').click(function(){
					updatePicturesContainer(this.hash.replace('#', ''));

					return false;
				});
			}
		});
	}
		
	function updatePicturesContainer(album){
		$.ajax({
			url: Croogo.basePath + 'admin/kieken/kieken_albums/view/' + album,
			data: '',
			type: 'GET',
			success: function(j){
				$('#pictures_container').html(j);
				
				$('a.delete').click(function(){
					removePicture($(this).parent('li').parent('ul').parent('td').attr('id'), $('#pictures_container div').attr('id'));
					return false;
				});
				
				$('a.setasalbumthumbnail').live('click', function(){
					setAsAlbumThumbnail($(this).parent('li').parent('ul').parent('td').attr('id'), $('#pictures_container div').attr('id'));

					// Remove 'isalbumthumbnail' class from current thumbnail
					$('a.isalbumthumbnail').removeClass('isalbumthumbnail').addClass('setasalbumthumbnail');

					// Remove 'setasalbumthumbnail' class and add 'isalbumthumbnail' class from the clicked link
					$(this).removeClass('setasalbumthumbnail');
					$(this).addClass('isalbumthumbnail');
					return false;
				});
				
				$('img.thumbnail').click(function(){
					showImageDetails($(this).parent('td').attr('id'));
					return false;
				});
				
				$('.edit_title').editable(function(value, settings){
					$(this).html(settings.indicator);
					returnvalue = editPictureDetails({'KiekenPicture': {'id': $(this).parent('td').attr('id'), 'title': value}});
					return returnvalue['KiekenPicture']['title'];
				}, {
					type: 'text',
					cancel: 'Cancel',
					submit: 'Save',
					tooltip: 'Click to edit...'
				});

				$('.edit_description').editable(function(value, settings){
					$(this).html(settings.indicator);
					returnvalue = editPictureDetails({'KiekenPicture': {'id': $(this).parent('td').attr('id'), 'description': value}})
					return returnvalue['KiekenPicture']['description'];
				},{
					type: 'text',
					cancel: 'Cancel',
					submit: 'Save',
					tooltip: 'Click to edit...'
				});
			}
		});
	}
	
	$(document).ready(function(){
		if(Croogo.params.named.album_id){
			album = Croogo.params.named.album_id;
		}
		else {
			album = 'all';
		}
		updateAlbumNav(album);
		updatePicturesContainer(album);
	});
</script>
<div id="dialog-confirm" title="Delete picture" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 7px 0;"></span>Are you sure you want to delete this picture?</p>
</div>
<div class="kieken kieken_pictures index">
    <h2><?php echo $title_for_layout; ?></h2>
	<div class="container_13">
		<div id="album_nav" class="grid_4">
			<div class="top_nav">
				<h3><?php __('Albums'); ?></h3>
				<?php echo $html->link(__('New album', true), array('controller' => 'kieken_albums', 'action' => 'add')); ?>
			</div>
			<div id="album_index">
				
			</div>
		</div>
		<div id="pictures_container" class="grid_9">
			
		</div>
	</div>
</div>