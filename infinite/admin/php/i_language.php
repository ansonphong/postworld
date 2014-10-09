<?php

///// LANGUAGE ARRAY /////
global $i_language;
$i_language = array(
	'general'	=>	array(
		'save'		=>	'Save',
		'cancel'	=>	'Cancel',
		'back'		=>	'‹ Back',
		),
	'sidebars'	=>	array(
		'settings'			=>	'Sidebar Settings',
		'add_new' 			=>	'Add New Sidebar',
		'delete'			=>	'Delete Sidebar',
		'duplicate'			=>	'Duplicate Sidebar',

		'name'				=>	'Name',
		'name_info'			=>	'The name is how it appears on the widgets options page.',
		'id'				=>	'ID',
		'id_info'			=>	'The ID is the unique name for the sidebar. It is all lowercase and contains only letters, numbers, and hyphens.',
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
		'settings'			=>	'Feed Settings',
		'add_new' 			=>	'Add New Feed',
		'delete'			=>	'Delete Feed',
		'duplicate'			=>	'Duplicate Feed',
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