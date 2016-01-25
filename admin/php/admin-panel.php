<?php
// TODO : Add security wrappers

///// MAIN SCREEN /////
function postworld_postworld_modules(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-modules.php';
}

///// THEME OPTIONS SCREEN /////
function postworld_options_site(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-site.php';
} 

///// LAYOUT SCREEN /////
function postworld_options_layout(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-layout.php';
}

///// STYLES SCREEN /////
function postworld_options_styles(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-styles.php';
}

///// SOCIAL SCREEN /////
function postworld_options_social(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-social.php';
}

///// SIDEBARS SCREEN /////
function postworld_options_sidebars(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-sidebars.php'; 
} 

///// FEEDS SCREEN /////
function postworld_options_feeds(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-feeds.php';
}

/*
///// FEEDS SCREEN /////
function postworld_options_term_feeds(){
	global $theme_admin;
	i_include_scripts();
	include 'page-term-feeds.php';
}
*/

///// BACKGROUNDS SCREEN /////
function postworld_options_backgrounds(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-backgrounds.php';
}

///// ICONSETS SCREEN /////
function postworld_options_iconsets(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-iconsets.php';
}

///// CACHE SCREEN /////
function postworld_options_database(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-database.php';
}

///// SHORTCODES SCREEN /////
function postworld_options_shortcodes(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-shortcodes.php';
}

///// COMMENTS SCREEN /////
function postworld_options_comments(){
	global $theme_admin;
	//i_include_scripts();
	include 'page-comments.php';
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