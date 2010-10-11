<?php
class KiekenAlbumsController extends KiekenAppController {
	var $name = 'KiekenAlbums';
	var $uses = array('Kieken.KiekenAlbum');
	
	function admin_index() {
		$this->set('title_for_layout', sprintf(__('Albums', true)));
		
		$this->KiekenAlbum->recursive = 0;

		$albums = $this->paginate('KiekenAlbum');
		$this->set(compact('albums'));
	}
	
	function admin_add() {
		$this->set('title_for_layout', sprintf(__('Add Album', true)));
		
		if(!empty($this->data)){
			$this->data['KiekenAlbum']['user_id'] = $this->Session->read('Auth.User.id');
			
			$this->KiekenAlbum->create();
			if($this->KiekenAlbum->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['KiekenAlbum']['title']));
                $this->redirect(array('action' => 'view', $this->KiekenAlbum->id));
			}
			else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['KiekenAlbum']['title']));
			}
		}
		
		$albums = $this->KiekenAlbum->generatetreelist();
		$this->set(compact('albums'));
	}
	
	function admin_view($id = null) {
		if(!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->KiekenAlbum->recursive = 3;
		$album = $this->KiekenAlbum->findById($id);
		foreach($album['KiekenPicture'] as $pictureKey => $picture) {
			$album['KiekenPicture'][$pictureKey]['KiekenFile'] = Set::combine($picture['KiekenFile'], '{n}.thumbname', '{n}');
		}

		if($album) {
			$this->set('title_for_layout', sprintf(__('Album: %s', true), $album['KiekenAlbum']['title']));
			$this->set(compact('album'));
		}
		else {
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function admin_edit($id = null) {
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
                $this->redirect(array('action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $album['KiekenAlbum']['title']));
			}
		}
		
		$this->data = $album;
		$albums = $this->KiekenAlbum->generatetreelist();
		$this->set(compact('albums'));
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
}
?>