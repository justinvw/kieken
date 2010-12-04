<?php
class KiekenPicturesController extends KiekenAppController {
	var $name = 'KiekenPictures';
	var $uses = array('Kieken.KiekenPicture');
	
	function beforeFilter(){
		parent::beforeFilter();
		// CSRF Protection
        if (in_array($this->params['action'], array('admin_edit'))) {
            $this->Security->validatePost = false;
        }
	}
	
	function admin_index($album = 'all') {
		$this->set('title_for_layout', sprintf(__('Pictures', true)));
	}
	
	function admin_album_index(){
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', 0);
		}
		
		$albums = $this->KiekenPicture->KiekenAlbum->albumList();
		
		$all_pictures_count = $this->KiekenPicture->find('count');
		
		$pictures_in_albums = $this->KiekenPicture->KiekenAlbumsPicture->find('list', array(
			'fields' => array('KiekenAlbumsPicture.picture_id')
		));
		$pictures_not_in_albums_count = $this->KiekenPicture->find('count', array(
			'conditions' => array(
				'NOT' => array('KiekenPicture.id' => $pictures_in_albums)
			)
		));
		
		$this->set(compact('albums', 'all_pictures_count', 'pictures_not_in_albums_count'));
	}
	
	function admin_add($album_id = null) {
		$this->set('title_for_layout', sprintf(__('Upload pictures', true)));
		
		$albums = $this->KiekenPicture->KiekenAlbum->generatetreelist();
		
		$this->set(compact('album_id', 'albums'));
	}
	
	function admin_upload() {
		require_once(APP.'plugins'.DS.'kieken'.DS.'vendors'.DS.'phpthumb'.DS.'ThumbLib.inc.php');
		
		if(!$this->RequestHandler->isAjax()) {
			$this->redirect(array('action' => 'add'));
		}
		
		Configure::write('debug', 0);
		$this->autoRender = false;
		
		if(empty($this->params['url']['album'])) {
			echo json_encode(array('success' => false, 'error' => "No album selected."));
			exit();
		}
		
		if(!is_writable(WWW_ROOT.Configure::read('Kieken.uploadDirectory'))) {
			echo json_encode(array('success' => false, 'error' => 'The upload directory is not writeable'));
			exit();
		}
		
		$uploadInfo = pathinfo($this->params['url']['qqfile']);
		$uploadInfo['uploadFilename'] = String::uuid().'.'.$uploadInfo['extension'];
		$uploadData = fopen("php://input", "r");
		
		$storeUpload = fopen(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$uploadInfo['uploadFilename'], "w");
		while ($data = fread($uploadData, 1024)) {
			fwrite($storeUpload, $data);
		}
		fclose($storeUpload);
		fclose($uploadData);
		
		$this->KiekenPicture->create();
		$this->data['KiekenPicture']['title'] = $uploadInfo['filename'];
		$this->data['KiekenPicture']['user_id'] = $this->Session->read('Auth.User.id');
		$this->data['KiekenPicture']['status'] = 1;
		$this->data['KiekenAlbum']['id'] = $this->params['url']['album'];
		$this->KiekenPicture->save($this->data);
		
		// If there is no thumbnail picture assigned to the album, use this picture as thumbnail
		$album = $this->KiekenPicture->KiekenAlbum->find('first', array(
			'conditions' => array(
				'KiekenAlbum.id' => $this->params['url']['album'],
				'KiekenAlbum.thumbnail_picture_id' => null
			)
		));
		if($album){
			$this->KiekenPicture->KiekenAlbum->id = $this->params['url']['album'];
			$this->KiekenPicture->KiekenAlbum->saveField('thumbnail_picture_id', $this->KiekenPicture->id);
		}
		
		// Generate thumbnails
		foreach(Configure::read('Kieken.thumbnails') as $thumbnail) {
			$thumbnailFilename = String::uuid().'.'.$uploadInfo['extension'];
			$manipulateImage = PhpThumbFactory::create(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$uploadInfo['uploadFilename']);
			if($thumbnail['resizeMethod'] == 'normal') {
				$manipulateImage->resize($thumbnail['width'], $thumbnail['height']);
			}
			elseif($thumbnail['resizeMethod'] == 'adaptive') {
				$manipulateImage->adaptiveResize($thumbnail['width'], $thumbnail['height']);
			}
			$newDimensions = $manipulateImage->getNewDimensions();
			$manipulateImage->save(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$thumbnailFilename);
			
			unset($this->data);
			$this->KiekenPicture->KiekenFile->create();
			$this->data['KiekenFile']['picture_id'] = $this->KiekenPicture->id;
			$this->data['KiekenFile']['filename'] = $thumbnailFilename;
			$this->data['KiekenFile']['width'] = $newDimensions['newWidth'];
			$this->data['KiekenFile']['height'] = $newDimensions['newHeight'];
			$this->data['KiekenFile']['thumbname'] = $thumbnail['thumbName'];
			$this->KiekenPicture->KiekenFile->save($this->data);
		}
		
		// If the original file must be kept, create a DB entry for it, else delete the file
		if(Configure::read('Kieken.keepOriginal') == 1) {
			$originalImage = PhpThumbFactory::create(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$uploadInfo['uploadFilename']);
			$dimensions = $originalImage->getCurrentDimensions();

			unset($this->data);
			$this->KiekenPicture->KiekenFile->create();
			$this->data['KiekenFile']['picture_id'] = $this->KiekenPicture->id;
			$this->data['KiekenFile']['filename'] = $uploadInfo['uploadFilename'];
			$this->data['KiekenFile']['width'] = $dimensions['width'];
			$this->data['KiekenFile']['height'] = $dimensions['height'];
			$this->data['KiekenFile']['thumbname'] = 'original';
			$this->KiekenPicture->KiekenFile->save($this->data);
		}
		else {
			unlink(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$uploadInfo['uploadFilename']);
		}
		
		echo json_encode(array('success' => true));
	}
	
	function admin_edit($id = null) {
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', 0);
		}
		
		if(empty($this->data)){
			$albums = $this->KiekenPicture->KiekenAlbum->generatetreelist();
			$picture = $this->KiekenPicture->findById($id);
			$picture['KiekenFile'] = Set::combine($picture['KiekenFile'], '{n}.thumbname', '{n}');
			$picture['KiekenAlbum'] = Set::combine($picture['KiekenAlbum'], '{n}.id', '{n}');
			
			$this->data = $picture;
			$this->set(compact('picture', 'albums'));
		}
		else {
			$this->KiekenPicture->id = $this->data['KiekenPicture']['id'];
			if($this->KiekenPicture->save($this->data)){
				if($this->RequestHandler->isAjax()){
					$this->autoRender = false;
					$this->RequestHandler->respondAs('text/x-json');
					echo json_encode($this->data);
				}
			}
		}
	}
	
	function admin_delete($id = null, $scope = null, $album_id = null) {
		if(!$id) {
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$picture = $this->KiekenPicture->findById($id);
		
		# Get list of albums that use is picture as thumbnail
		$albums = $this->KiekenPicture->KiekenAlbum->find('list', array(
			'fields' => array('KiekenAlbum.id'),
			'conditions' => array('KiekenAlbum.thumbnail_picture_id' => $id)
		));
		
		# Delete picture and references from all albums
		if($scope == 'all'){
			if($this->KiekenPicture->delete($id, true)){
				# Delete files from disk
				foreach($picture['KiekenFile'] as $imageFile) {
					unlink(WWW_ROOT.Configure::read('Kieken.uploadDirectory').$imageFile['filename']);
				}
				
				# Select new pictures for albums that used this picture as thumbnail
				foreach($albums as $album_id){
					$album = $this->KiekenPicture->KiekenAlbum->findById($album_id);
					$this->KiekenPicture->KiekenAlbum->id = $album_id;
					$this->KiekenPicture->KiekenAlbum->saveField('thumbnail_picture_id', $album['KiekenPicture'][0]['id']);
				}
			}
		}
		
		# Delete picture reference from certain album ($album_id)
		elseif($scope == 'album'){
			if($this->KiekenPicture->KiekenAlbumsPicture->deleteAll(array('KiekenAlbumsPicture.album_id' => $album_id, 'KiekenAlbumsPicture.picture_id' => $id))){
				# If album in list of albums that use the picture as thumbnail, select other picture in album as thumbnail
				if(in_array($album_id, $albums)){
					$album = $this->KiekenPicture->KiekenAlbum->findById($album_id);
					$this->KiekenPicture->KiekenAlbum->id = $album_id;
					$this->KiekenPicture->KiekenAlbum->saveField('thumbnail_picture_id', $album['KiekenPicture'][0]['id']);
				}
			}
		}
		
		# Delete picture references from all albums, but keep the picture itself
		else {
			if($this->KiekenPicture->KiekenAlbumsPicture->deleteAll(array('KiekenAlbumsPicture.picture_id' => $id))){
				# Select new pictures for albums that used this picture as thumbnail
				foreach($albums as $album_id){
					$album = $this->KiekenPicture->KiekenAlbum->findById($album_id);
					$this->KiekenPicture->KiekenAlbum->id = $album_id;
					$this->KiekenPicture->KiekenAlbum->saveField('thumbnail_picture_id', $album['KiekenPicture'][0]['id']);
				}
			}
		}
		
		if(!$this->RequestHandler->isAjax()) {
			if($album_id){
				$this->redirect(array('controller'=> 'KiekenAlbums', 'action' => 'view', $album_id));
			}
			else {
				$this->redirect(array('action' => 'index'));
			}
		}
	}
}
?>