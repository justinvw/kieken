<?php
class KiekenFeaturedComponent extends Object {
	public function startup(&$controller){
		$KiekenPicture = ClassRegistry::init('Kieken.KiekenPicture');
		$pictures = $KiekenPicture->find('all', array(
		    'conditions' => array('KiekenPicture.promote' => 1),
		    'order' => 'RAND()',
			'limit' => 5
		));
        
        foreach($pictures as $pictureKey => $picture) {
			$pictures[$pictureKey]['KiekenFile'] = Set::combine($picture['KiekenFile'], '{n}.thumbname', '{n}');
		}
		
		$controller->set('kieken_featured', $pictures);
	}
}
?>