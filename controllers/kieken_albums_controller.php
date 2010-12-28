<?php
class KiekenAlbumsController extends KiekenAppController {
	var $name = 'KiekenAlbums';
	var $uses = array('Kieken.KiekenAlbum');
	
	function beforeFilter(){
		parent::beforeFilter();
		// CSRF Protection
        if (in_array($this->params['action'], array('admin_add', 'admin_edit'))) {
            $this->Security->validatePost = false;
        }
	}
	
	function index($parent_album_id = null){
		$this->set('title_for_layout', sprintf(__('Albums', true)));
		$this->KiekenAlbum->recursive = 3;
		$this->KiekenAlbum->unbindModel(array(
				'hasAndBelongsToMany' => array('KiekenPicture'),
			), false
		);
		
		if($parent_album_id){
			$albums = $this->paginate('KiekenAlbum', array(
				'KiekenAlbum.status' => 1,
				'KiekenAlbum.parent_id' => $parent_album_id
			));
			$albumsTree = $this->KiekenAlbum->generatetreelist( array(
				'KiekenAlbums.id' => array_keys($albums),
				'KiekenAlbum.parent_id' => $parent_album_id
			));
		}
		else {
			$albums = $this->paginate('KiekenAlbum', array(
				'KiekenAlbum.status' => 1
			));
			
			$albums = Set::combine($albums, '{n}.KiekenAlbum.id', '{n}');
			
			$albumsTree = $this->KiekenAlbum->generatetreelist(array(
				'KiekenAlbums.id' => array_keys($albums)
			));
		}
		
		foreach($albums as $albumKey => $album) {
			$albums[$albumKey]['KiekenThumbnail']['KiekenFile'] = Set::combine($album['KiekenThumbnail']['KiekenFile'], '{n}.thumbname', '{n}');
		}
		
		$this->set(compact('albums', 'albumsTree'));
	}
	
	function view($album_slug = null){
		$this->KiekenAlbum->recursive = 2;
		$this->KiekenAlbum->unbindModel(array(
			'belongsTo' => array('KiekenThumbnail'),
		));
		$album = $this->KiekenAlbum->find('first', array(
			'conditions' => array(
				'KiekenAlbum.status' => 1,
				'KiekenAlbum.slug' => $album_slug,
			)
		));
		
		foreach($album['KiekenPicture'] as $pictureKey => $picture) {
			$album['KiekenPicture'][$pictureKey]['KiekenFile'] = Set::combine($picture['KiekenFile'], '{n}.thumbname', '{n}');
		}
				
		$this->set('title_for_layout', $album['KiekenAlbum']['title']);
		$this->set(compact('album'));
	}
	
	function admin_index() {
		$this->set('title_for_layout', sprintf(__('Albums', true)));
		
		$this->KiekenAlbum->recursive = 3;
		$this->KiekenAlbum->KiekenThumbnail->unbindModel(array('hasAndBelongsToMany' => array('KiekenAlbum')), false);
		$this->KiekenAlbum->unbindModel(
			array('hasAndBelongsToMany' => array('KiekenPicture')), false
		);
		
		$albums = $this->paginate('KiekenAlbum');
		
		$albums = Set::combine($albums, '{n}.KiekenAlbum.id', '{n}');
		foreach($albums as $albumKey => $album) {
			$albums[$albumKey]['KiekenThumbnail']['KiekenFile'] = Set::combine($album['KiekenThumbnail']['KiekenFile'], '{n}.thumbname', '{n}');
		}
		
		$albumsTree = $this->KiekenAlbum->generatetreelist(array('KiekenAlbum.id' => array_keys($albums)));

		$this->set(compact('albums', 'albumsTree'));
	}
	
	function admin_add() {
		$this->set('title_for_layout', sprintf(__('Add Album', true)));
		
		if(!empty($this->data)){
			$this->data['KiekenAlbum']['user_id'] = $this->Session->read('Auth.User.id');
			
			$this->KiekenAlbum->create();
			if($this->KiekenAlbum->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['KiekenAlbum']['title']));
                $this->redirect(array('controller' => 'kieken_pictures', 'action' => 'index', 'album_id' => $this->KiekenAlbum->id));
			}
			else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['KiekenAlbum']['title']));
			}
		}
		
		$albums = $this->KiekenAlbum->generatetreelist();
		$this->set(compact('albums'));
	}
	
	function admin_view($id = 'all'){
		if($id == 'all'){
			$this->set('title_for_layout', sprintf(__('All pictures', true)));
			$items = $this->KiekenAlbum->KiekenPicture->find('all');
			
			$pictures = array();
			foreach($items as $item){
				$picture = $item['KiekenPicture'];
				$picture['KiekenFile'] = $item['KiekenFile'];
				$pictures['KiekenPicture'][] = $picture;
			}
		}
		elseif($id == 'no-album'){
			$this->set('title_for_layout', sprintf(__('Pictures not in an album', true)));
			$pictures_in_albums = $this->KiekenAlbum->KiekenAlbumsPicture->find('list', array(
				'fields' => array('KiekenAlbumsPicture.picture_id')
			));
			$items = $this->KiekenAlbum->KiekenPicture->find('all', array(
				'conditions' => array(
					'NOT' => array('KiekenPicture.id' => $pictures_in_albums)
				)
			));
			
			$pictures = array();
			foreach($items as $item){
				$picture = $item['KiekenPicture'];
				$picture['KiekenFile'] = $item['KiekenFile'];
				$pictures['KiekenPicture'][] = $picture;
			}
		}
		else {
			$this->KiekenAlbum->recursive = 3;
			$pictures = $this->KiekenAlbum->findById($id);
			$this->set('title_for_layout', $pictures['KiekenAlbum']['title']);
		}
		
		if($pictures){
			foreach($pictures['KiekenPicture'] as $pictureKey => $picture) {
				$pictures['KiekenPicture'][$pictureKey]['KiekenFile'] = Set::combine($picture['KiekenFile'], '{n}.thumbname', '{n}');
			}
		}
		
		$this->set(compact('pictures', 'id'));
	}
	
	function admin_edit($id = null) {
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', 0);
			$this->autoRender = false;
			
			if($this->KiekenAlbum->save($this->data)) {
				echo json_encode(array('result' => 'success'));
			}
			else {
				echo json_encode(array('result' => 'failed'));
			}
		}
		else {
			if(!$id) {
				$this->Session->setFlash(__('Invalid content', true));
				$this->redirect(array('action' => 'index'));
			}
		
			$album = $this->KiekenAlbum->findById($id);
			if(!$album) {
				$this->Session->setFlash(__('Album does not exist.', true));
	            $this->redirect(array('action' => 'index'));
			}
		
			$this->set('title_for_layout', sprintf(__('Edit album: %s', true), $album['KiekenAlbum']['title']));
		
			if(!empty($this->data)) {
				$this->KiekenAlbum->id = $id;
				if($this->KiekenAlbum->save($this->data)) {
					$this->Session->setFlash(sprintf(__('%s has been saved', true), $album['KiekenAlbum']['title']));
	                $this->redirect(array('controller' => 'kieken_pictures', 'action' => 'index', 'album_id' => $id));	                
				}
				else {
					$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $album['KiekenAlbum']['title']));
				}
			}
		
		
			$this->data = $album;
			$albums = $this->KiekenAlbum->generatetreelist();
			$this->set(compact('albums'));
		}
	}
	
	function admin_delete($id = null, $scope = null) {
		if(!$id) {
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		if($scope == 'all'){
			$this->KiekenAlbum->recursive = 2;
			$pictures = $this->KiekenAlbum->findById($id);
			
			foreach($pictures['KiekenPicture'] as $picture) {
				$pictureIds[] = $picture['id'];
			}
			
			$picturesInMultipleAlbums = $this->KiekenAlbum->KiekenAlbumsPicture->find('list', array(
				'conditions' => array(
					'KiekenAlbumsPicture.picture_id' => $pictureIds,
						'AND' => array('KiekenAlbumsPicture.album_id !=' => $id)
				),
				'fields' => 'KiekenAlbumsPicture.picture_id'
			));

			if($this->KiekenAlbum->delete($id, true)){
				foreach($pictures['KiekenPicture'] as $picture) {
					if(!in_array($picture['id'], $picturesInMultipleAlbums)) {
						foreach($picture['KiekenFile'] as $imageFile) {
							unlink(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$imageFile['filename']);
						}
						
						$picturesToDelete[] = $picture['id'];
					}
				}
				
				$this->KiekenAlbum->KiekenPicture->deleteAll(array('KiekenPicture.id' => $picturesToDelete), true);
				
				$this->Session->setFlash(__('Album and pictures deleted', true));
				$this->redirect(array('action' => 'index'));
			};
		}
		else {
			if($this->KiekenAlbum->delete($id, false)) {
				if($this->KiekenAlbum->KiekenAlbumsPicture->deleteAll(array('KiekenAlbumsPicture.picture_id' => $id))) {
					$this->Session->setFlash(__('Album deleted', true));
					$this->redirect(array('action' => 'index'));
				}
			}
		}
	}
	
	function admin_move($id, $direction = 'up', $step = 1){
		$album = $this->KiekenAlbum->findById($id);
		if(!isset($album['KiekenAlbum']['id'])){
            $this->Session->setFlash(__('Invalid id for Album', true), 'default', array('class' => 'error'));
            $this->redirect(array(
				'controller' => 'kiekenalbums',
				'action' => 'index',
 			));
		}
		$this->KiekenAlbum->Behaviors->attach('Tree');
		if($direction == 'up'){
			if($this->KiekenAlbum->moveup($id, 1)){
				$this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
			}
			else {
				$this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
			}
		}
		elseif($direction == 'down'){
			if($this->KiekenAlbum->movedown($id, 1)){
				$this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
			}
			else {
				$this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>