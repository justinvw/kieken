<?php
	Croogo::hookRoutes('Kieken');
	#Croogo::hookBehavior('Node', 'Example.Example', array());
	#Croogo::hookComponent('*', 'Kieken.Kieken');
	Croogo::hookComponent('Nodes', 'Kieken.KiekenFeatured');
	Croogo::hookComponent('Dolfify.Frontpage', 'Kieken.KiekenFeatured');
	#Croogo::hookHelper('Nodes', 'Example.Example');
	Croogo::hookAdminMenu('Kieken');
	#Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');
	#Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
	#Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
	
	# Load the Kieken settings
	if(file_exists(APP.'plugins'.DS.'kieken'.DS.'config'.DS.'settings.yml')){
		 $settings = Spyc::YAMLLoad(file_get_contents(APP.'plugins'.DS.'kieken'.DS.'config'.DS.'settings.yml'));
	}
	
	foreach($settings AS $settingKey => $settingValue){
		Configure::write($settingKey, $settingValue);
	}
?>