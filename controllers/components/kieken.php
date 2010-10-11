<?php
/**
 * Kieken Component
 * 
 * @package Kieken
 * @author Justin van Wees <justin@vwees.net>
 * @version 0.1
 **/

class KiekenHookComponent extends Object {
	function onActivate(&$controller) {
		$controller->Croogo->addPluginBootstrap('kieken');
	}
	
	function onDeactivate(&$controller) {
		
	}
}
?>