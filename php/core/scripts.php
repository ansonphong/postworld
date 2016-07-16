<?php
/**
 * POSTWORLD SCRIPTS
 * Manages the concatination of multiple Javascript files.
 *
 * @todo Make function to flush all cached JS files, call it on admin settings save.
 * @todo Detect when settings are changed and clear.
 * @todo Manage the deletion of cached JS files based on time, so they auto-renew.
 * 			- ie. Delete all files older than 24 hours.
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
 * Deletes all the cached script files matching the
 * specified pattern of prefix and extension.
 *
 * @param string $prefix The beginning of the filenames to delete.
 * @param string $extension The file extension of postfix of the files to delete.
 */
function pw_scripts_flush( $prefix = null, $extension = '.js' ){
	if( $prefix === null )
		$prefix = '';

	$pw_scripts = new PW_Scripts();
	$pw_scripts->flush( $prefix, $extension );
}

/**
 * Flush cached scripts on common actions which might alter JS configurations
 */
add_action( 'wp_update_nav_menu', 'pw_scripts_flush_action' );
add_action( 'wp_update_nav_menu_item', 'pw_scripts_flush_action' );
add_action( 'wp_create_nav_menu', 'pw_scripts_flush_action' );
add_action( 'wp_delete_nav_menu', 'pw_scripts_flush_action' );
add_action( 'update_option', 'pw_scripts_flush_action' );
add_action( 'pw_set_option', 'pw_scripts_flush_action' );
function pw_scripts_flush_action(){
	pw_scripts_flush();
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
			'minify'		=> false,		// Whether or not to minify the file
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
	 * Gets the registered scripts in the defined group slug.
	 */
	public function get_scripts_in_group( $group ){
		return _get( $GLOBALS['postworld_scripts'], $group );
	}

	/**
	 * Finally combines and enqueues all registered files
	 * In the selected context.
	 * @todo This function is a bit of a spaghetti factory, refactor into modular methods
	 */
	public function enqueue( $vars = array() ){
		global $pw;
		
		// Set default variables
		$default_vars = array(
			'group' 	=> 'default', 	// Unique group to enqueue
			'in_footer' => true,		// Whether or not to put in footer
			'deps'		=> array(),		// Dependencies
			'version'	=> pw_site_version(),
			'expire'	=> 60*60*24,	// After how many seconds to expire
			'merge_files'	=> true 	// If false, enqueue all files and do not merge into single file
			);
		$vars = array_replace( $default_vars, $vars );

		// Get the registered scripts in the specified group
		$scripts = $this->get_scripts_in_group( $vars['group'] );
		// Get the scripts directory
		$scripts_dir = $this->get_scripts_dir();

		/**
		 * If we are merging all the files
		 */
		if( $vars['merge_files'] ){

			/**
			 * Check if a cached file already exists
			 */
			// Generate hash from array
			$scripts_hash = hash( 'adler32', json_encode( $scripts ) );

			// Generate the filename as "[group]--[hash]--[time].js"
			$group_prefix = $vars['group'] . '--';
			$hash_prefix =  $group_prefix . $scripts_hash . '--';
			$filename = $hash_prefix . time() . '.js';
			$file_path = $scripts_dir . '/' . $filename;

			/**
			 * Check if the current file version exists in the cache directory.
			 * If files are found, check if they haven't expired yet
			 * And if they're good, get the file path.
			 */
			$files = $this->glob( $hash_prefix, '.js' );
			$regenerate = false;
			if( !empty( $files ) ){
				foreach( $files as $file ){
					/**
					 * Get the timestamp component of the filename
					 * and compare it to the current time 
					 */
					$basename = basename( $file, '.js' );
					$parts = explode( '--', $basename );
					// Get the last [time] segment of the filename
					$time_index = count($parts)-1;
					$file_time = intval( $parts[ $time_index ] );

					// If any of the files in the group are expired
					if( time() > $file_time + $vars['expire'] ){
						// Regenerate the file
						$regenerate = true;
						// Flush all existing JS files in the current group
						$this->flush( $vars['group'] . '--', '.js' );
						break;
					}
					// Otherwise, set the current filename as the file.
					else{
						$file_path = $file;
					}
				}
			} else{
				$regenerate = true;
			}


			/**
			 * If no file is found which matches
			 * Combine the scripts and save them to a new JS file.
			 */
			if( $regenerate && $vars['merge_files'] ){

				// If the scripts directory doesn't exist, create it
				if (!file_exists($scripts_dir)){
					$mkdir = mkdir($scripts_dir, 0777, true);
					// If the directory can't be created, end here 
					if( !$mkdir ){
						$error_message = 'Postworld unable to create directory to cache scripts at ' . $scripts_dir . ' Destination has file permissions code: ' . fileperms($scripts_dir );
						error_log( $error_message );
						pw_log(  $error_message );
						$vars['merge_files'] = false;
					}
				}

				// Sort all scripts by order of priority
				pw_sort_array_of_arrays( $scripts, 'priority', 'ASC' );

				// Get the contents of all the scripts and add them to a string
				$content = '';
				foreach( $scripts as $handle => $script ){
					if( file_exists( $script['file'] ) ){
						$content .= "// SCRIPT : " . $script['handle'] . " / PRIORITY : " . $script['priority'];
						$content .= PHP_EOL;

						// Get the contents of the file
						$file_contents = file_get_contents( $script['file'] );

						// If minify is enabled, minify it
						$content .= ( $script['minify'] ) ? 
							$this->minify( $file_contents ) :
							$file_contents;
					
						$content .= PHP_EOL . PHP_EOL . PHP_EOL;
					}
				}
				// Write all the scripts to a new file
				$put_contents = file_put_contents( $file_path, $content );

				// If the file put operation was not successful, send an error
				if( $put_contents === false ){
					$error_message = 'Postworld unable to create file to cache scripts at ' . $file_path . ' Destination has file permissions code: ' . fileperms($file_path );
					error_log( $error_message );
					pw_log( $error_message );
					$vars['merge_files'] = false;
				}

			}

		}

		/**
		 * If all files have been merged
		 * Then include just the single merged file
		 */
		if( $vars['merge_files'] ){
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
		 * Enqueue all the files seperately
		 */
		else{
			foreach($scripts as $script_slug => $val) {
				// Generate URL from system path
				$script_url = str_replace( ABSPATH, get_site_url(), $val['file']  ) ;
				wp_enqueue_script(
					$script_slug,
					$script_url,
					$vars['deps'],
					$vars['version'],
					$vars['in_footer'] );
			}
		}

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

		return _get( wp_upload_dir(), $key ) . '/'. apply_filters( 'pw_scripts_cache_dir', pw_theme_slug() . '-cache' );
	}

	public function glob( $prefix = '', $extension = '.js' ){
		$scripts_dir = $this->get_scripts_dir();
		return glob( $scripts_dir . '/' . $prefix . '*' . $extension );
	}

	/**
	 * Deletes all the matching files in the scripts directory.
	 */
	public function flush( $prefix = '', $extension = '.js' ){
		$files = $this->glob( $prefix, $extension );
		foreach( $files as $file ){
			if( file_exists($file) )
				unlink( $file );
		}
		return;
	}

	/**
	 * Minify the input content
	 * @link https://github.com/matthiasmullie/minify/issues/83
	 */
	public function minify( $content ){
		$path = POSTWORLD_DIR . '/lib';
		include_once( $path. '/JShrink/Minifier.php');
		return \JShrink\Minifier::minify($content);
	}

}
