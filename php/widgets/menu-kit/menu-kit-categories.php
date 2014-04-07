<?php
/*
////////// MENU KIT : CATEGORIES //////////
The main function for rendering Menu Kit Taxonomies.

*/

function menu_kit_categories($OPTIONS){
	
	extract($OPTIONS);
	
	global $wpdb;
	$POST_TYPE_IDS = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = '$POST_TYPE' AND post_status = '$POST_STATUS'");
	
	if($POST_TYPE_IDS){
		$POST_TYPE_CATS = wp_get_object_terms( $POST_TYPE_IDS, $TAXONOMY,array('fields' => 'ids') );
		
		if($POST_TYPE_CATS){
			$POST_TYPE_CATS = array_unique($POST_TYPE_CATS);
			$POST_TYPE_CATS = implode(',',$POST_TYPE_CATS);

			$args = array(
				'include'		=> $POST_TYPE_CATS,
				'taxonomy'		=> $TAXONOMY,
				'hierarchical'	=> $HIERARCHICAL,
				'title_li'		=> $TITLE,
				'depth'			=> 2,
				'show_count'	=> 0,
				'hide_empty'	=> $HIDE_EMPTY
				);

			if( !empty($INCLUDE_) )
				$args['include'] = $INCLUDE_;

			if( !empty($EXCLUDE_) )
				$args['exclude'] = $EXCLUDE_;

			wp_list_categories($args);
			
		}
		
	}
	///// END LIST CATEGORIES OUTPUT /////


}


?>