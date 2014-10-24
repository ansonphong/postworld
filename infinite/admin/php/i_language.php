<?php

///// LANGUAGE ARRAY /////
global $i_language;
$i_language = array(
	'general'	=>	array(
		'save'		=>	'Save',
		'cancel'	=>	'Cancel',
		'back'		=>	'‹ Back',
		'none'		=>	'None',
		),
	'sidebars'	=>	array(
		'item_title'		=>	'Sidebar Settings',
		'add_new' 			=>	'Add New Sidebar',
		'delete'			=>	'Delete Sidebar',
		'duplicate'			=>	'Duplicate Sidebar',

		'name'				=>	'Sidebar Name',
		'name_info'			=>	'The name is how it appears on the widgets options page.',
		'id'				=>	'Sidebar ID',
		'id_info'			=>	'The ID is the unique name for the sidebar. It contains only letters, numbers, and hyphens.',
		'id_edit_info'		=>	'Editing the ID may cause instances of the sidebar to disappear.',	
		
		'description'		=>	'Description',
		'description_info'	=>	'The description describes the intended use of the sidebar in the widgets options.',
		'edit'				=>	'Edit Sidebar',
		'delete' 			=> 	'Delete Sidebar',
		'update' 			=> 	'Update Sidebar',
		
		'class'				=> 	'Class',
		'class_info'		=> 	'The CSS class which is applied to each widget',

		'before_widget'		=> 	'Before Widget',
		'before_widget_info'=> 	'HTML that goes before the widget',

		'after_widget'		=> 	'After Widget',
		'after_widget_info'	=> 	'HTML that goes after the widget',

		'before_title'		=> 	'Before Title',
		'before_title_info'	=> 	'HTML that goes before the title of each widget',

		'after_title'		=> 	'After Title',
		'after_title_info'	=> 	'HTML that goes after the title of each widget',
		),

	'feeds'	=>	array(
		'item_title'		=>	'Feed Settings',
		'add_new' 			=>	'Add New Feed',
		'delete'			=>	'Delete Feed',
		'duplicate'			=>	'Duplicate Feed',

		'name'				=>	'Feed Name',
		'name_info'			=>	'The name is an aesthetic label for the feed.',
		'id'				=>	'Feed ID',
		'id_info'			=>	'The ID is the unique identifier for the feed. It contains only letters, numbers, and hyphens.',
		'id_edit_info'		=>	'Editing the ID may cause instances of the feed to disappear.',	
		
		'preload'			=>	'Preload',
		'preload_info'		=>	'How many posts to preload',

		'increment'			=>	'Load Increment',
		'increment_info'	=>	'How many posts to load each infinite scroll',

		'offset'			=>	'Offset',
		'offset_info'		=>	'How many posts to skip at the Javascript level',

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

		),

	);

function ___( $item, $return = false ){
	global $i_language;
	$message = pw_get_obj( $i_language, $item );
	if( !$return )
		echo $message;
	else
		return $message;
}

?>