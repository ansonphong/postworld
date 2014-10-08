<?php
// TODO : Add security wrappers

///// MAIN SCREEN /////
function infinite_postworld_main(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	//include 'page-options.php';
	echo "<h1>".$theme_admin['main']['page_title']."</h1>";
}

///// THEME OPTIONS SCREEN /////
function infinite_options_main(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-options.php';
} 

///// LAYOUT SCREEN /////
function infinite_options_layout(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-layout.php';
}

///// STYLES SCREEN /////
function infinite_options_styles(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-styles.php';
}

///// SOCIAL SCREEN /////
function infinite_options_social(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-social.php';
}

///// SIDEBARS SCREEN /////
function infinite_options_sidebars(){
	global $theme_admin;
	global $i_language;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-sidebars.php'; 
} 

///// FEEDS SCREEN /////
function infinite_options_feeds(){
	global $theme_admin;
	i_include_admin_styles();
	i_include_scripts();
	include 'page-feeds.php';
}


/*
///// DISPLAY MESSAGES /////
function i_display_messages($message = null){
	if( isset( $_GET['message'] ) ){
		$message = $_GET['message'];
	}
	else if( !isset($message) ){
		$message = "";
	}
	// Messages
	$message_content = "";
	switch ($message) {
		case "add-success":
			$message_content = 	"New sidebar added.";
			$message_type = 	"success";
			break;
		case "add-error":
			$message_content = 	"No sidebar added.";
			$message_type = 	"error";
			break;
		case "update-success":
			$message_content = 	"Sidebar updated.";
			$message_type = 	"success";
			break;
		case "delete-success":
			$message_content = 	"Sidebar deleted.";
			$message_type = 	"success";
			break;
		case "error":
			$message_content = 	"Error.";
			$message_type = 	"error";
			break;
		case "error-exist":
			$message_content = 	"Error: specified object does not exist.";
			$message_type = 	"error";
			break;
		default:
			$message_type = "";
			break;
	}

	if( !empty($message_content) ){
		echo "<div id=\"message\" class=\"message $message_type\"><strong>$message_content</strong></div>";
	}
}
*/
?>