<?php
/*
////////// MENU KIT : AUTHORS //////////
The main function for rendering Menu Kit Taxonomies.

*/


function menu_kit_authors($OPTIONS){
	
	extract($OPTIONS);
	
	$display_admins = $AUTHORS_SHOW_ADMINS;
	$order = $AUTHORS_ORDER; 
	
	// ORDER BY : 'nicename', 'email', 'url', 'registered', 'display_name', or 'post_count'
	if ($AUTHORS_ORDER_BY == '') $order_by = 'display_name';
		else $order_by = $AUTHORS_ORDER_BY;
	
	// ROLE : 'subscriber', 'contributor', 'editor', 'author' - leave blank for 'all'
	if ($AUTHORS_ROLE == 'all') $role = '';
		else $role = $AUTHORS_ROLE; 
	
	if ($AUTHORS_AVATAR_SIZE == '') $avatar_size = 32;
		else $avatar_size = $AUTHORS_AVATAR_SIZE;
	$hide_empty = $AUTHORS_HIDE_EMPTY; // hides authors with zero posts

	if(!empty($display_admins)) {
		$blogusers = get_users('orderby='.$order_by.'&order='.$order.'&role='.$role);
	} else {
		$admins = get_users('role=administrator');
		$exclude = array();
		foreach($admins as $ad) {
			$exclude[] = $ad->ID;
		}
		$exclude = implode(',', $exclude);
		$blogusers = get_users('exclude='.$exclude.'&orderby='.$order_by.'&order='.$order.'&role='.$role);
	}
	$authors = array();
	foreach ($blogusers as $bloguser) {
		$user = get_userdata($bloguser->ID);
		if(!empty($hide_empty)) {
			$numposts = count_user_posts($user->ID);
			if($numposts < 1) continue;
		}
		$authors[] = (array) $user;
	}
	
	global $current_user;
    get_currentuserinfo();
	
	$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
	
	//echo $curauth->user_nicename; //$current_user->display_name;
	
	
	echo '<div class="authors">';
	foreach($authors as $author) {
		$display_name = $author['data']->display_name;
		$avatar = get_avatar($author['ID'], $avatar_size);
		$author_profile_url = get_author_posts_url($author['ID']);
	
		if ( $author['ID'] == $curauth->ID )
			echo '<a href="', $author_profile_url, '" class="selected">', $avatar , '<span class="author-name">', $display_name, '</span><div style="clear:both"></div></a>';
		else
			echo '<a href="', $author_profile_url, '">', $avatar , '<span class="author-name">', $display_name, '</span><div style="clear:both"></div></a>';
			
	}
	echo '</div>';
	
	
	

}


?>