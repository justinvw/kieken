<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("#KiekenAlbumTitle").slug({
		        slug:'slug',
		        hide: false
		});
	});
</script>
<div class="kieken_album form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $form->create('KiekenAlbum', array('url' => array('action' => 'edit')));?>
		<fieldset>
			<?php echo $form->input('parent_id', array('type' => 'select', 'options' => $albums, 'empty' => true)); ?>
			<?php echo $form->input('title'); ?>
			<?php echo $form->input('slug', array('class' => 'slug')); ?>
			<?php echo $form->input('excerpt'); ?>
			<?php echo $form->input('description', array('class' => 'content')); ?>
			<?php echo $form->input('status', array('label' => __('Published', true), 'checked' => 'checked')); ?>
		</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>