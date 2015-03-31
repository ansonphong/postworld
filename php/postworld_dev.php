<?php
///// DEV TESTING /////

function pw_test_related_posts(){
	//$test1 = pw_related_query( array('post_id'=>250803) );
	//pw_log( 'pw_related_query : ' . json_encode($test1, JSON_PRETTY_PRINT) );
	$test2 = pw_related_posts_by_taxonomy( array(
		'post_id' 	=> 	250803,
		'depth' 	=> 	10000,
		'number'	=>	10,
		'output'	=>	'ids',
		'order_by'	=>	'score',
		'query' => array(
			'post_type' => array('feature','blog')
			),
		'taxonomies' => array(
			array(
				'taxonomy' => 'post_tag',
				'weight' => 1.5,
				),
			array(
				'taxonomy' => 'topic',
				'weight' => 1,
				),
			),
		));
	pw_log( 'pw_related_posts_by_taxonomy : ' . json_encode($test2, JSON_PRETTY_PRINT) );

}
add_action('wp_loaded', 'pw_test_related_posts');


?>