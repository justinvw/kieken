<h3><?php echo $title_for_layout; ?></h3>
<?php if(count($pictures['KiekenPicture']) == 0): ?>
	<div id="no-pictures">
		<p><?php __('This album does not contain any pictures.'); ?></p>
	</div>
<?php else: ?>
<div id="<?php echo $id; ?>">
	<table>
		<tbody>
			<tr>
			<?php
				$count = 0;
				foreach($pictures['KiekenPicture'] as $picture):
				$count++;
			?>
					<td id="<?php echo $picture['id']; ?>">
						<h4 class="editable edit_title"><?php echo $picture['title']; ?></h4>
						<?php echo $html->image(DS.Configure::read('Kieken.uploadDirectory').$picture['KiekenFile']['small']['filename'], array('class' => 'thumbnail', 'id' => $picture['id'],'width' => '200px')); ?>
						<p class="editable edit_description <?php 
							if(empty($picture['description'])){
								echo 'no_description">';
								echo __('(no description)', true);	
							}
							else {
								echo '">'.$picture['description'];
								
							}
								
						?></p>
						<ul class="actions">
							<li><?php echo $html->link(__('Delete', true), array(
										'controller' => 'kieken_pictures', 
										'action' => 'delete', 
										$picture['id'], 
										'all'), 
									array('class' => 'delete')); ?></li>
							<?php 
								if($id && $id != 'no-album' && $id != 'all'):
									if($picture['id'] == $pictures['KiekenAlbum']['thumbnail_picture_id']){
										$class = 'isalbumthumbnail';
									}
									else {
										$class = 'setasalbumthumbnail';
									}
							?>
							<li><?php echo $html->link(__('Use as album\'s thumbnail', true), array(
										'controller' => 'kieken_pictures',
										'action' => 'edit',
										$pictures['KiekenAlbum']['id']),
									array('class' => $class)); ?></li>
							<?php endif; ?>
						</ul>
					</td>
			<?
					if($count % 3 == 0){
						echo '</tr><tr>';
					}
				endforeach;
			?>
		</tbody>
	</table>
</div>
<?php endif;?>