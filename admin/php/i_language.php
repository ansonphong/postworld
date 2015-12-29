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

	'backgrounds'	=>	array(
		'item_title'		=>	'Background Settings',
		'add_new' 			=>	'Add New Background',
		'delete'			=>	'Delete Background',
		'duplicate'			=>	'Duplicate Background',

		'name'				=>	'Background Name',
		'name_info'			=>	'The name makes it easy to find.',
		'id'				=>	'Background ID',
		'id_info'			=>	'The ID is the unique name for the vackground. It contains only letters, numbers, and hyphens.',
		'id_edit_info'		=>	'Editing the ID may cause instances of the background to disappear.',	
		
		'description'		=>	'Description',
		'description_info'	=>	'The description describes the intended use of the Background.',
		
		'edit'				=>	'Edit Background',
		'delete' 			=> 	'Delete Background',
		'update' 			=> 	'Update Background',


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

	'shortcodes'	=>	array(
		'shortcode'			=>	'Shortcode',
		'item_title'		=>	'Shortcode Settings',
		'add_new' 			=>	'Add New Shortcode',
		'delete'			=>	'Delete Shortcode',
		'duplicate'			=>	'Duplicate Shortcode',

		'name'				=>	'Shortcode Snippet Name',
		'id'				=>	'Shortcode ID',
		'id_info'			=>	'The ID is the unique name for the shortcode. This is used to invoke the shortcode.',
		'id_edit_info'		=>	'Editing the ID may cause instances of the shortcode to disappear.',	
		
		'edit'				=>	'Edit Shortcode Snippet',
		'delete' 			=> 	'Delete Shortcode Snippet',
		'update' 			=> 	'Update Shortcode Snippet',

		'before_content'	=>	'Before Content',
		'after_content'		=>	'After Content',

		'content'			=>	'Content',

		'enclosing'			=>	'Enclosing',
		'self_enclosing'	=>	'Self-enclosing',

		'enclosing_description'			=>	'Contains two parts, a beginning and end, which enclose content.',
		'self_enclosing_description'	=>	'Contains one part, which is self-contained.',

		),

	'iconsets'	=>	array(
		'icons'				=>	'Icons',
		'icon_shortcode'	=>	'Icon Shortcode',
		'icon_shortcode_description'	=>	'Select an icon to get it\'s shortcode',
		'enabled_iconsets'	=>	'Enabled Iconsets',
		'shortcode_how_to'	=>	'To use the shortcode, paste the following text into a post.'
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