<?php
/**
 * Gets an array of options for use in admin settings panels,
 * allowing Postworld and Themes to filter the options based on context.
 *
 * @param string $context The context of the options to retreive.
 * @param array $options A set of default options.
 * 
 * @return array The options.
 */
function pw_admin_options( $context = 'default', $options = array() ){
	$options['context'] =  $context;
	$options = apply_filters( 'pw_admin_options', $options );
	return $options;
}


/**
 * Add transition admin options
 */
add_filter('pw_admin_options', 'pw_admin_filter_add_transitions', 8);
function pw_admin_filter_add_transitions( $options ){

	$context = _get( $options, 'context' );
	$add_to = array( 'slider' );

	if( in_array( $context, $add_to ) )
		$options['transition'] = array(
			array(
				'value' => false,
				'name' => 'None',
				),
			array(
				'value' => 'slide',
				'name' => 'Slide',
				),
			array(
				'value' => 'fade',
				'name' => 'Fade',
				),
			);

	return $options;

}

?>