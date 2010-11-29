<a href="#"><?php __('Kieken'); ?></a>
<ul>
	<li><?php echo $html->link(__('Albums', true), array('plugin' => 'kieken', 'controller' => 'kieken_albums', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Pictures', true), array('plugin' => 'kieken', 'controller' => 'kieken_pictures', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Upload pictures', true), array('plugin' => 'kieken', 'controller' => 'kieken_pictures', 'action' => 'add')); ?></li>
	<li><?php echo $html->link(__('Settings', true), '#'); ?></li>
</ul>