<?php
///// LANGUAGE ARRAY /////
global $i_language;
$i_language = array(
	'general'	=>	array(
		'save'		=>	'Save',
		'cancel'	=>	'Cancel',
		'back'		=>	'‹ Back',
		'none'		=>	'None',
		'default'	=>	'Default',

		'views'		=>	'Views',
		'view'		=>	'View',

		'grid'		=>	'Grid',
		'columns'	=> 	'Columns',

		'number'	=>	'Number',
		'depth'		=>	'Depth',

		'within'	=>	'Within',
		'period'	=>	'Period',
		
		),

	'taxonomy'	=>	array(
		'singular' => 'Taxonomy',
		'plural' => 'Taxonomies',
		),

	'related_posts' => array(
		'number_info' => 'The number of related posts to show',
		'depth_info' => 'Higher number for slower, more accurate results. 0 searches all posts.',
		'sub_clause' => 'Sub Clause',
		),

	'shortcodes'	=>	array(
		'shortcode'			=>	'Shortcode',
		'item_title'		=>	'',
		'add_new' 			=>	'Add New Shortcode',
		'delete'			=>	'Delete Shortcode',
		'duplicate'			=>	'Duplicate Shortcode',

		'name'				=>	'',
		'id'				=>	'',
		'id_info'			=>	'',
		'id_edit_info'		=>	'Editing the ID may cause instances of the shortcode to disappear.',	
		
		'edit'				=>	'Edit Shortcode Snippet',
		'delete' 			=> 	'Delete Shortcode Snippet',
		'update' 			=> 	'Update Shortcode Snippet',

		'before_content'	=>	'Before Content',
		'after_content'		=>	'After Content',

		'content'			=>	'Content',

		'enclosing'			=>	'Enclosing',
		'self_enclosing'	=>	'',

		'enclosing_description'			=>	'.',
		'self_enclosing_description'	=>	'.',

		),

	'feeds'	=>	array(
		'item_title'		=>	'',
		'add_new' 			=>	'Add New Feed',
		'delete'			=>	'Delete Feed',
		'duplicate'			=>	'Duplicate Feed',

		'name'				=>	'',
		'name_info'			=>	'',
		'id'				=>	'Feed ID',
		'id_info'			=>	'',
		'id_edit_info'		=>	'',	
		
		'preload'			=>	'',
		'preload_info'		=>	'',

		'increment'			=>	'',
		'increment_info'	=>	'',

		'offset'			=>	'',
		'offset_info'		=>	'',

		'feed_template'		=>	'Feed Template',
		'aux_template'		=>	'Auxiliary Template',

		'settings'	=>	array(
			'loading_icon'	=>	'Loading Icon',
			),

		'view'	=>	array(
			'title'		=>	'Template',
			'current'	=>	'View',
			'options'	=>	'View Options',
			'options_none'	=>	'None',
			),

		),


	'query'		=>	array(
		'post_type'				=>	'Post Type',
		'post_status'			=>	'Post Status',
		'post_class'			=>	'Post Class',

		'offset'				=>	'Offset',
		'offset_info'			=>	'How many posts to skip at the MySQL level',
		'orderby'				=>	'Order By',

		'order'					=>	'Order',

		'posts_per_page'		=>	'Maximum Posts',
		'posts_per_page_info'	=>	'Maximum number of posts',

		'event_filter'			=>	'Event Filter',
		'event_filter_none'		=>	'None',

		'post_parent'			=>	'Post Parent',
		'post_parent_id'		=>	'Post Parent ID',
		'post_parent_id_info'	=>	'Enter the ID of the parent post',
		'post_parent_selector'	=>	'Select Post Parent',

		'exclude_posts'			=>	'Exclude Posts',
		'include_posts'			=>	'Include Posts',

		'author_from'			=>	'Author',
		'author_id'				=>	'Author ID',
		'author_id_info'		=>	'The user ID of the author',

		'taxonomy'				=>	'Taxonomy',
		'term'					=>	'Term',

		),

	'gallery' => array(
		'template'	=>	array(
			'inline' =>	array(
				'name' => 'Inline',
				'description' => 'Galleries appear inline with the post content as a grid of images.',
				),
			'frame' => array(
				'name' => 'Frame',
				'description' => 'All galleries in the post are merged into a single frame gallery.',
				),
			'horizontal' => array(
				'name' => 'Horizontal',
				'description' => 'All galleries in the post are merged into a single horizontal infinite scrolling gallery.',
				),
			'vertical' => array(
				'name' => 'Vertical',
				'description' => 'All galleries in the post are merged into a single vertical infinite scrolling gallery.',
				),
			),
		),

	);

function ___( $item, $return = false ){
	global $i_language;
	$message = _get( $i_language, $item );
	if( !$return )
		echo $message;
	else
		return $message;
}

?>