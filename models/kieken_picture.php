<?php
class KiekenPicture extends KiekenAppModel {
	var $name = 'KiekzenPicture';
	var $useTable = 'kieken_pictures';
	
	var $hasAndBelongsToMany = array(
		'KiekenAlbum' => array(
			'className' => 'Kieken.KiekenAlbum',
			'joinTable' => 'kieken_albums_pictures',
			'foreignKey' => 'picture_id',
			'associationForeignKey' => 'album_id',
		)
	);	
	var $hasMany = array(
		'KiekenFile' => array(
			'className' => 'Kieken.KiekenFile',
			'foreignKey' => 'picture_id',
			'dependent' => true
		)
	);
}
?>