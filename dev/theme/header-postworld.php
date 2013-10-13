<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<style>
BODY {
	padding-top:50px;
}
.navbar-fixed-top {
	top:20px;
}
.infinite-scroll {
	height: 400px;
	overflow-y: scroll;
}
.shift {
	top: 20px;	
}
</style>
<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">POST WORLD</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#/home">Home</a></li>
			<li class="dropdown">
		        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Live Feed<b class="caret"></b></a>
		        <ul class="dropdown-menu">
		          <li><a href="#/live-feed-2">Live Feed - 1 Panel</a></li>
		          <li><a href="#/live-feed-1">Live Feed - 2 Panels</a></li>
		          <li><a href="#/live-feed-3">Live Feed - URL Params</a></li>
		        </ul>
		    </li>
			<li class="dropdown">
		        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Load Feed<b class="caret"></b></a>
		        <ul class="dropdown-menu">
		          <li><a href="#/load-feed-1">Load Feed</a></li>
		          <li><a href="#/load-feed-2">Load Feed - Cached Outline</a></li>
		        </ul>
		    </li>
            <li ><a href="#/register-feed">Register Feed</a></li>
            <li><a href="#/load-panel">Load Panel</a></li>
            <li><a href="#/edit-post">Edit Post</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</div>
<div class="container">
