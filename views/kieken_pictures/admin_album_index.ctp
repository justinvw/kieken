<ul id="totals">
	<li><a href="#all"><?php __('All pictures'); ?> <em>(<?php echo $all_pictures_count; ?>)</em></a></li>
	<li><a href="#no-album"><?php __('Pictures not in an album'); ?> <em>(<?php echo $pictures_not_in_albums_count; ?>)</em></a></li>
</ul>
<ul id="albums">
	<?php foreach($albums as $album): ?>
		<li><a href="#<?php echo $album['KiekenAlbum']['id']; ?>"><?php echo $album['KiekenAlbum']['title']; ?> <em>(<?php echo $album[0]['count']; ?>)</em></a></li>
	<?php endforeach; ?>
</ul>