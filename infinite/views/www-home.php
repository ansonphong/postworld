<?php // Template Name: WWW [ Home Page ] ?>

<!-- INFINITE HEADER -->
<?php i_header(); ?>


SLIDER

<!-- INFINITE LAYOUT -->
<?php
	//global $post;
	//$queried_object = get_queried_object();

	// Callback function for the page content (optional)
	function page_content_function(){
		?>
		<h3>Home.</h3>
		<?
	}

	// Create the main layout
	global $iGlobals;
	$layout_args = array(
		'layout'	=>	$iGlobals['layout']['layout'],
		//'content'	=>	apply_filters( 'the_content', $post->post_content ),
		'function'	=>	'page_content_function',
		);
	i_print_layout( $layout_args );
?>

<!-- INFINITE FOOTER -->
<?php i_footer(); ?>