<?php
class KiekenAlbum extends KiekenAppModel {
	var $name = 'KiekenAlbum';
	var $actsAs = array('Tree');
	var $validate = array(
		'title' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
		'slug' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This slug has already been taken.',
            ),
            'minLength' => array(
                'rule' => array('minLength', 1),
                'message' => 'Slug cannot be empty.',
            ),
        )
	);
	
	var $hasAndBelongsToMany = array(
		'KiekenPicture' => array(
			'className' => 'Kieken.KiekenPicture',
			'joinTable' => 'kieken_albums_pictures',
			'foreignKey' => 'album_id',
			'associationForeignKey' => 'picture_id',
			'dependent' => true
		)
	);
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => false
		),
		'KiekenThumbnail' => array(
			'className' => 'Kieken.KiekenPicture',
			'foreignKey' => 'thumbnail_picture_id',
			'dependent' => false
		)
	);
	
	/* 
	* Function returns all albums with the number of images associated with 
	* them. Currently there is no 'nice' way to do this because counterCache 
	* is not implemented for HABTM associations
	* */
	function albumList(){
		return $this->query('SELECT `KiekenAlbum`.`id`, `KiekenAlbum`.`title`, `KiekenAlbum`.`thumbnail_picture_id`, count(`KiekenAlbumsPicture`.`picture_id`) AS `count` FROM `kieken_albums` AS `KiekenAlbum` JOIN `kieken_albums_pictures` AS `KiekenAlbumsPicture` ON (`KiekenAlbumsPicture`.`album_id` = `KiekenAlbum`.`id`) GROUP BY `KiekenAlbumsPicture`.`album_id` ORDER BY `KiekenAlbum`.`title`');
	}
}
?>