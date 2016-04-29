<?php
/**
 * Adds support to WP Customize for Postworld settings and methods.
 */
class PW_Customize_Manager{

	function __construct(){
		$this->init();
	}

	public function init(){
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		add_action( 'customize_update_postworld', array( $this, 'customize_update' ), 10, 2 );
		add_action( 'customize_preview_postworld', array( $this, 'customize_preview' ), 10, 2 );
	}

	/**
	 * Make a constant definition so that theme functionality can be altered
	 * Depending on if WordPress is doing a customize preview.
	 */
	public function customize_preview_init(){
		if( !defined( 'PW_DOING_CUSTOMIZER' ) )
			define( 'PW_DOING_CUSTOMIZER', true );
	}

	/**
	 * Automates the process of properly adding a WP Customizer
	 * setting and control for regular setting types.
	 * text | select | checkbox | etc...
	 */
	public function add_control_setting( $wp_customize, $vars ){

		// Required fields
		if( !isset( $vars['option_definition'] ) ||
			!isset( $vars['subkey'] ) ||
			!isset( $vars['type'] ) ||
			!isset( $vars['section'] ) )
			return false;

		// Get the default value from the database
		$default_value = pw_grab_option( constant( $vars['option_definition'] ), $vars['subkey'] );

		// Set default variables
		$default_vars = array(
			'default'			=>	$default_value,
			'capability'		=> 	'edit_theme_options',
			'transport'			=> 	'',
			'sanitize_callback' => 	null,
			'priority'			=> 	null,
			'description'		=>	false,
			'choices'			=>	null,
			'width'				=>	null,
			'height'			=>	null,	
			);
		$vars = array_replace( $default_vars, $vars );

		// Define the shared setting ID
		$setting_id = $vars['option_definition'].'['.$vars['subkey'].']';

		// Add the setting
		$wp_customize->add_setting( $setting_id, array(
			'type' 				=> 'postworld',
			'default' 			=> $vars['default'],
			'capability' 		=> $vars['capability'],
			'transport'			=> $vars['transport'],
			'sanitize_callback' => $vars['sanitize_callback'],
		));

		// Add the coorosponding control
		$wp_customize->add_control( $setting_id, array(
			'type'			=> $vars['type'],
			'priority'		=> $vars['priority'],
			'section'		=> $vars['section'],
			'label'			=> $vars['label'],
			'description'	=> $vars['description'],
			'choices'		=> $vars['choices'],
			'width'			=> $vars['width'],
			'height'		=> $vars['height'],
		));

	}


	/**
	 * Automates the process of properly adding a WP Customizer
	 * setting and control for color values.
	 */
	public function add_color_setting( $wp_customize, $vars ){

		$setting_id = $vars['option_definition'].'['.$vars['subkey'].']';

		$wp_customize->add_setting( $setting_id, array(
			'default' => pw_grab_option( constant( $vars['option_definition'] ), $vars['subkey'] ),
			'type' => 'postworld',
			'capability' => 'edit_theme_options',
			'transport' => '',
			'sanitize_callback' => 'pw_sanitize_hex_color_value',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $vars['subkey'], array(
			'label'    => $vars['label'],
			'description' => _get($vars,'description'),
			'section'  => $vars['section'],
			'settings' => $setting_id,
		)));

	}


	/**
	 * This happens upon saving & publishing customized settings.
	 */
	public function customize_update( $value, $setting ){
		$this->flush_scripts();

		// Extract the option definition from the setting ID
		$option_definition = $this->extract_option_definition( $setting->id );

		// If malformed ID, or undefined option, end here
		if( $option_definition === false )
			return false;

		// Set the value into the Database
		pw_set_option( array(
			'option_name' => $option_definition['option_name'],
			'key' => $option_definition['key'],
			'value' => $value,
			) );

		/*
		pw_log( 'pw_customize_update : value', $value );
		pw_log( 'pw_customize_update : value type', gettype($value) );
		pw_log( 'pw_customize_update : setting', $setting );
		pw_log( 'pw_customize_update : setting->id', $setting->id );
		pw_log( 'pw_customize_update : setting->value()', $setting->value() );
		pw_log( 'pw_customize_update : setting->post_value()', $setting->post_value() );
		*/

	}

	/**
	 * Filters options being read from the database so that they can be
	 * Previewed live with the WordPress native theme customizer.
	 *
	 * Setting IDs must match the following pattern : PW_OPTIONS_THEME[search.show_search]
	 * Where 'PW_OPTIONS_THEME' is a defined constant option name in the wp_options table
	 * And 'search.show_search' is a subkey within that array
	 */
	public function customize_preview( $setting ){
		$this->flush_scripts();

		/**
		 * Get and sanitize the value
		 */
		$setting_value = $setting->post_value();
		if( $setting_value === null )
			return false;

		// Extract the option definition from the setting ID
		$option_definition = $this->extract_option_definition( $setting->id );
		if($option_definition === false)
			return false;

		/**
		 * Filter the value incoming via pw_get_option
		 * @see pw_get_option()
		 */
		add_filter( $option_definition['option_name'], function( $value ) use ( $option_definition, $setting_value ){
			$value = _set( $value, $option_definition['key'], $setting_value );
			return $value;
		});

	}


	/**
	 * Utility to split a setting ID in the form of a string, into an array.
	 * @see pw_get_option(), pw_set_option()
	 * 
	 * @param string $string Example: PW_OPTIONS_THEME[search.show_search]
	 * @return array An array of extracted values, for example:
	 *	array(
	 		'option_definition' => 'PW_OPTIONS_THEME',
	 		'option_name' => 'postworld-theme-artdroid',
	 		'key' => 'search.show_search'
	 		)
	 */
	public function extract_option_definition( $string ){
		
		$parts = explode( '[', $string );
		if( count($parts) > 1 ){
			// Get the first part, which is the option definition, ie. 'PW_OPTIONS_THEME'
			$option_definition = $parts[0];
			if( !defined( $option_definition ) )
				return false;

			// Get the option subkey, ie. 'search.show_search'
			preg_match("/\[(.*?)\]/", $string, $matches);
			if( count( $matches ) == 0 )
				return false;
			$option_subkey = $matches[1];
		}

		return array(
			'option_definition'	=> $option_definition,
			'option_name'		=> constant($option_definition),
			'key'				=> $option_subkey
			);

	}

	/**
	 * Clears all the cached script files.
	 */
	private function flush_scripts(){
		pw_scripts_flush('','.js');
	}

}
