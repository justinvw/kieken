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
}
?>