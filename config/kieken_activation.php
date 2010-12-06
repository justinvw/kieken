<?php
class KiekenActivation {
	public function beforeActivation(&$controller) {
		return true;
	}

	public function onActivation(&$controller) {
		// ACL: set ACOs with permissions
		$controller->Croogo->addAco('KiekenAlbums');
		$controller->Croogo->addAco('KiekenAlbums/admin_index');
		$controller->Croogo->addAco('KiekenAlbums/admin_add');
		$controller->Croogo->addAco('KiekenAlbums/admin_view');
		$controller->Croogo->addAco('KiekenAlbums/admin_edit');
		$controller->Croogo->addAco('KiekenAlbums/admin_delete');
		$controller->Croogo->addAco('KiekenAlbums/index', array('registered', 'public'));
		$controller->Croogo->addAco('KiekenAlbums/view', array('registered', 'public'));
		
		$controller->Croogo->addAco('KiekenPictures');
		$controller->Croogo->addAco('KiekenPictures/admin_index');
		$controller->Croogo->addAco('KiekenPictures/admin_album_index');
		$controller->Croogo->addAco('KiekenPictures/admin_add');
		$controller->Croogo->addAco('KiekenPictures/admin_edit');
		$controller->Croogo->addAco('KiekenPictures/admin_delete');
		$controller->Croogo->addAco('KiekenPictures/admin_upload');

		// Main menu: add an Example link
		#$mainMenu = $controller->Link->Menu->findByAlias('main');
		#$controller->Link->Behaviors->attach('Tree', array(
		#	 'scope' => array(
		#		 'Link.menu_id' => $mainMenu['Menu']['id'],
		#	 ),
		#));
		#$controller->Link->save(array(
		#	 'menu_id' => $mainMenu['Menu']['id'],
		#	 'title' => 'Example',
		#	 'link' => 'plugin:example/controller:example/action:index',
		#	 'status' => 1,
		#));
	}

	public function beforeDeactivation(&$controller) {
		return true;
	}

	public function onDeactivation(&$controller) {
		// ACL: remove ACOs with permissions
		$controller->Croogo->removeAco('KiekenAlbums');
		$controller->Croogo->removeAco('KiekenPicutres');

		// Main menu: delete Example link
		#$link = $controller->Link->find('first', array(
		#	 'conditions' => array(
		#		 'Menu.alias' => 'main',
		#		 'Link.link' => 'plugin:example/controller:example/action:index',
		#	 ),
		#));
		#$controller->Link->Behaviors->attach('Tree', array(
		#	 'scope' => array(
		#		 'Link.menu_id' => $link['Link']['menu_id'],
		#	 ),
		#));
		#if (isset($link['Link']['id'])) {
		#	 $controller->Link->delete($link['Link']['id']);
		#}
	}
}
?>