<script type="text/javascript" charset="utf-8">
	$(function() {
		function removeAlbum(id){
			$('#dialog-confirm').dialog({
				resizable: false,
				height: 'auto',
				width: 'auto',
				modal: true,
				draggable: false,
				buttons: {
					'Delete album and pictures' : function(){
						window.location = Croogo.basePath + 'admin/kieken/kieken_albums/delete/' + id + '/all';
					},
					'Delete album' : function(){
						window.location = Croogo.basePath + 'admin/kieken/kieken_albums/delete/' + id + '/album';
					},
					'Cancel' : function (){
						$(this).dialog('close');
					}
				}
			});
			$('#dialog-confirm').dialog('open');
		}
		
		$('a.delete').click(function(){
			removeAlbum($(this).attr('id'));
			return false;
		});
	});
</script>
<div id="dialog-confirm" title="Delete album" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 7px 0;"></span>Are you sure you want to delete this album?</p>
</div>
<div class="kieken_album index">
    <h2><?php echo $title_for_layout; ?></h2>
	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add album', true), array('action' => 'add')); ?></li>
		</ul>
	</div>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $html->tableHeaders(array(
            '',
			__('ID', true),
            __('Title', true),
            __('Excerpt', true),
            __('Created by', true),
            __('Status', true),
            __('Created', true),
            __('Actions', true),
        ));
        echo $tableHeaders;
		
		$rows = array();
		
		foreach($albums AS $album) {
			$actions = $html->link(__('Edit', true), array('action' => 'edit', $album['KiekenAlbum']['id']));
			$actions .= ' '.$html->link(__('Delete', true), array('action' => 'delete', $album['KiekenAlbum']['id']), array('class' => 'delete', 'id' => $album['KiekenAlbum']['id']));
			
			$rows[] = array(
				'',
				$album['KiekenAlbum']['id'],
				$html->link($album['KiekenAlbum']['title'], array('action' => 'view', $album['KiekenAlbum']['id'])),
				$album['KiekenAlbum']['excerpt'],
				$album['User']['username'],
				$layout->status($album['KiekenAlbum']['status']),
				$album['KiekenAlbum']['created'],
				$actions
			);
		}
		
		echo $html->tableCells($rows);
        echo $tableHeaders;
	?>	
	</table>
</div>
<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>