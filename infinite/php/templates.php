<?php

// DEPRECIATED : Use pw_get_templates()
function i_get_templates( $args = array() ){

	global $i_paths;
	extract($args);


	// Set Defaults
	if( !isset($templates_object) )
		$templates_object = array(); // TODO - Add handling for this for performance, folder or file specifics
	if( !isset( $path_type ) )
		$path_type = 'dir';
	if( !isset( $source ) )
		$source = 'merge';

	// Check to see if there is a templates folder in the child folder
	$has_override_templates_dir = is_dir( $i_paths['templates']['dir']['override'] );

	// Setup Variables
	$default_template_dir = $i_paths['templates']['dir']['default'];
	$default_template_url = $i_paths['templates']['url']['default'];
	$override_template_dir = $i_paths['templates']['dir']['override'];
	$override_template_url = $i_paths['templates']['url']['override'];

	// DEFAULT Templates Object
	$default_template_obj_args = array(
		'dir'	=>	$default_template_dir,
		'url'	=>	$default_template_url,
		'ext'	=>	".php",
		'path_type'	=>	$path_type,
		);

	if( $source == 'default' || $source == 'merge' )
		$default_template_obj = i_construct_template_obj( $default_template_obj_args );

	// OVERRIDE Templates Object
	$override_template_obj_args = array(
		'dir'	=>	$override_template_dir,
		'url'	=>	$override_template_url,
		'ext'	=>	".php",
		'path_type'	=>	$path_type,
		);

	if( $source == 'override' || $source == 'merge' )
		$override_template_obj = i_construct_template_obj( $override_template_obj_args );

	// Merge Results
	if( $source == 'merge' ){
		// Start with Default Template Object
		$merge_template_obj = $default_template_obj;

		// Iterate over the Override Template Object
		foreach( $override_template_obj as $subdir => $templates ){

			// Iterate over the Templates
			foreach( $templates as $template_id => $template_value ){
				
				// Create the Subobject if it doesn't exist
				if( !isset($merge_template_obj[$subdir]) )
					$merge_template_obj[$subdir] = array();

				// Add the Override Value
				$merge_template_obj[$subdir][$template_id] = $template_value;

			}

		}

	}

	return $merge_template_obj;

}

function i_get_dirs($path = '.') {
    $dirs = array();
   
    if( file_exists($path) ){
    	$subpaths = new DirectoryIterator($path);
	    foreach ( $subpaths as $file) {
	        if ($file->isDir() && !$file->isDot()) {
	            $dirs[] = $file->getFilename();
	        }
	    }	
    }
  
    return $dirs;
}


function i_construct_template_obj( $args ){

	extract($args);

	// Set Defaults
	if( !isset( $path_type ) )
		$path_type = 'dir';
	if( !isset( $ext ) )
		$ext = '.php';
	if( !isset( $url ) )
		$url = '';

	$subdirs = i_get_dirs( $dir );
	$template_object = array();

	// Iterate through each Directory
	foreach( $subdirs as $subdir ){
		$template_object[$subdir] = array();
		$files = glob( trailingslashit($dir) . $subdir . '/*'.$ext );

		// Iterate through each File
		foreach( $files as $file ){
			$basename = basename($file);
			$basename_noext = basename($file, $ext);

			// Output Directory Path
			if( $path_type == 'dir' )
				$template_object[$subdir][$basename_noext] = $file;
			else if ( $path_type == 'url' )
			// Output URL Path
				$template_object[$subdir][$basename_noext] = trailingslashit($url) . $basename;
			
		}
	
	}

	return $template_object;
}






?>