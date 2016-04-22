<?php
/**
 * POSTWORLD SCRIPTS
 * Manages the concatination of multiple Javascript files.
 */
$GLOBALS['postworld_scripts'] = array();

/**
 * @see PW_Scripts::register()
 */
function pw_register_script( $script ){
	$pw_scripts = new PW_Scripts();
	$pw_scripts->register( $script );
}

/**
 * @see PW_Scripts::enqueue()
 */
function pw_enqueue_script( $vars ){
	$pw_scripts = new PW_Scripts();
	$pw_scripts->enqueue( $vars );
}


/**
 * Used for combining multiple JS script files
 * Into a single file to be included on the site. 
 */
class PW_Scripts{

	function __construct(){}

	/**
	 * Registers a file to be combined and enqueued.
	 */
	public function register( $script = array() ){

		// If the file isn't defined, or doesn't exist, end here
		if( !isset( $script['file'] ) || !file_exists( $script['file'] ) )
			return false;

		// Set the default variables
		$default_script = array(
			'group'			=> 'default',	// Which handle it's grouped with
			'handle'		=> null,		// Unique handle for this script
			'version' 		=> $GLOBALS['wp_version'],
			'priority' 		=> 100, 		// Order which it's placed within the group
			'file'			=> null, 		// Path to file
			);
		$script = array_replace($default_script, $script);

		// Require handle as string
		if( !is_string( $script['handle'] ) )
			return false;

		// If this is the first script being registered
		if( !isset( $GLOBALS['postworld_scripts'] ) ||
			!is_array( $GLOBALS['postworld_scripts'] ) )
			$GLOBALS['postworld_scripts'] = array();

		// If this is the first script in the group
		if( !isset( $GLOBALS['postworld_scripts'][ $script['group'] ] ) )
			$GLOBALS['postworld_scripts'][ $script['group'] ] = array();

		// Add it to the group of registered scripts
		$GLOBALS['postworld_scripts'][ $script['group'] ][ $script['handle'] ] = $script;

	}

	/**
	 * Finally combines and enqueues all registered files
	 * In the selected context.
	 */
	public function enqueue( $vars = array() ){
		global $pw;
		
		// Set default variables
		$default_vars = array(
			'group' 	=> 'default', 	// Unique group to enqueue
			'in_footer' => true,		// Whether or not to put in footer
			'deps'		=> array(),		// Dependencies
			'version'	=> pw_site_version(),
			);
		$vars = array_replace( $default_vars, $vars );

		// Get the registered scripts in the specified group
		$scripts = _get( $GLOBALS['postworld_scripts'], $vars['group'] );
		if( empty( $scripts ) )
			return false;

		/**
		 * Check if a cached file already exists
		 */
		$scripts_dir = $this->get_scripts_dir();

		// Generate hash from array
		$scripts_hash = hash( 'adler32', json_encode( $scripts ) );

		// Generate the filename
		$filename = $vars['group'] . '-' . $scripts_hash . '.js';
		$file_path = $scripts_dir . '/' . $filename;

		/**
		 * Combine the scripts and save them to a JS file
		 */
		if( !file_exists($file_path) ){

			// If the scripts directory doesn't exist, create it
			if (!file_exists($scripts_dir)){
				$mkdir = mkdir($scripts_dir, 0777, true);
				// If the directory can't be created, end here 
				if( !$mkdir )
					return false;
			}

			// Sort all scripts by order of priority
			pw_sort_array_of_arrays( $scripts, 'priority', 'ASC' );

			// Get the contents of all the scripts and add them to a string
			$scripts_contents = '';
			foreach( $scripts as $handle => $script ){
				if( file_exists( $script['file'] ) ){
					$scripts_contents .= "// SCRIPT : " . $script['handle'] . " / PRIORITY : " . $script['priority'] . PHP_EOL;
					$scripts_contents .= file_get_contents( $script['file'] ) . PHP_EOL . PHP_EOL . PHP_EOL;
				}
			}
			// Write all the scripts to a new file
			$put_contents = file_put_contents( $file_path, $scripts_contents );

		}

		// Generate the URL of the file from the Path
		$file_url = str_replace( _get( wp_upload_dir(), 'basedir' ), _get( wp_upload_dir(), 'baseurl' ), $file_path );

		// Enqueue the script in WordPress
		wp_enqueue_script(
			$vars['group'],
			$file_url,
			$vars['deps'],
			$vars['version'],
			$vars['in_footer']
			);

	}

	/**
	 * Gets the location of the scripts directory
	 *
	 * @param string $type Type of path. Options: dir | url
	 * @return string Absolute system path to the scripts directory.
	 */
	static function get_scripts_dir( $type = 'dir' ){

		if( $type === 'dir' )
			$key = 'basedir';
		elseif( $type === 'url' )
			$key = 'baseurl';

		return _get( wp_upload_dir(), $key ) . '/'. apply_filters( 'pw_scripts_cache_dir', pw_admin_submenu_slug() . '-cache' );
	}

}

