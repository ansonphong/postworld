<?php
/**
 * Template Name: Search Results
 *
 * This Template is a Sandbox for Post World Search Results - 
 * Using AngularJS Framework
 *
 */
 
 /* These JS Files can be kept separately during development, 
  * then upon release, put all together minified and in one file, to minimize the number of http requests
  use this http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/ to modify script inclusion
  */
  
/* TODO - Bootstrap style included for testing purposes, remove in final version, add ,array('twentytwelve-style') if you want to place after style.css */
wp_enqueue_style( 'Bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css', array('twentytwelve-style') );

postworld_includes();

wp_register_script( "BootStrap-JS", '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js' );
wp_enqueue_script( 'BootStrap-JS' );


get_header('postworld'); ?>
<div ng-app='postworld' data-nonce="<?php echo wp_create_nonce("postworld_nonce");?>" >
	<ng-view></ng-view>
</div>

<?php get_footer('postworld'); ?>