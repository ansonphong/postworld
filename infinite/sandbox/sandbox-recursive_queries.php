<?php // Template Name: Sandbox [ Recursive Queries ] ?>
<!-- INFINITE HEADER -->
<?php
	i_header();
	global $post;
?>
<?php
////////// PAGE ////////// ?>

<div style="margin-top:200px;"></div>

<?php echo apply_filters( 'the_content', $post->post_content ); ?>

<?php
	$vars = array(
			'terms' => array(
				'taxonomies'    =>  array( 'post_tag' ),
				'args'          =>  array(),
			),
			'query'  =>  array(
				'fields'    =>	'preview',    
			),
		);

	//$recursive_term_query_results = pw_get_terms_feed( $vars );

	//$taxonomies = array( 'post_tag' );
	//$get_terms_results = get_terms( $taxonomies );
?>

<pre><?php //echo "TERM QUERY : " . json_encode( $recursive_term_query_results, JSON_PRETTY_PRINT ); ?></pre>

<pre><?php //echo "GET TERMS : " . json_encode( $get_terms_results, JSON_PRETTY_PRINT ); ?></pre>



<!-- INFINITE FOOTER -->
<?php i_footer(); ?>