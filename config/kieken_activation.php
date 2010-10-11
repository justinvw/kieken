<?php
class KiekenActivation {
	public function beforeActivation(&$controller) {
		return true;
	}

	public function onActivation(&$controller) {
		// ACL: set ACOs with permissions
		#$controller->Croogo->addAco('Example'); // ExampleController
		#$controller->Croogo->addAco('Example/admin_index'); // ExampleController::admin_index()
		#$controller->Croogo->addAco('Example/index', array('registered', 'public')); // ExampleController::index()

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
		#$controller->Croogo->removeAco('Example'); // ExampleController ACO and it's actions will be removed

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