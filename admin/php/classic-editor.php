<?php

if(pw_module_config("admin.enable_classic_editor")){
	// check for plugin using plugin name
	$classic_editor_plugin_enabled = in_array('classic-editor/classic-editor.php', apply_filters('active_plugins', get_option('active_plugins')));
	
	// If the classic editor plugin isn't already installed and activated
	// Force activate a local copy of the plugin
	if($classic_editor_plugin_enabled !== true){ 
		include POSTWORLD_DIR.'/admin/classic-editor/classic-editor.php';
	}
}

?>