<?php

/*
////////// MENU KIT : PAGES //////////
The main function for rendering Menu Kit Pages.

These are the input settings for options :

$OPTIONS = array(
	'show_parent_pages'		=> 1/0,
	'show_sibling_pages'	=> 1/0,
	'show_child_pages'  	=> 1/0,
	)

*/


function menu_kit_pages($OPTIONS){
	
	///// SETUP OPTIONS /////
	extract ($OPTIONS);
	
	$current_page_id = get_the_ID();
	$current_page = get_page($current_page_id);
	
	// GET PARENT PAGE ID
	$parent_page_id = $current_page->post_parent;
	
	// IF PARENT PAGE ID EXISTS
	if ($parent_page_id){
		$parent_page = get_page($parent_page_id);
		$grandparent_page_id = $parent_page->post_parent;
		
		// IF GRANDPARENT PAGE ID EXISTS
		if ($grandparent_page_id){
			$grandparent_page = get_page($grandparent_page_id);
			}
	}
	
	// FUNCTION : GET CHILD PAGES // Returns an object with the child pages of $context
	function get_child_pages($context) {
		$page_args = array(
			'title_li'    	=> '',
			'parent'      	=> $context,
			'child_of'      => $context,
			'sort_order'	=> 'ASC',
			'sort_column'	=> 'menu_order',
			'hierarchical'	=> 0
		);
		$child_pages = get_pages( $page_args );
		return $child_pages;
	}
	
	
	///// RENDER PAGE MENU /////
	function render_page_menu($OPTIONS, $grandparent_page, $parent_page, $current_page){
	
		///// SETUP MENU DATA /////
		
		// ENABLE PARENT MENU
		if ($OPTIONS['show_parent_pages'] == 1 && $grandparent_page){
			$parent_menu_exists = 1;
			// DEFINE PARENT PAGES
			$parent_pages = get_child_pages($grandparent_page);
		}
			
		// ENABLE SIBLING MENU
		if ($OPTIONS['show_sibling_pages'] == 1 && $parent_page){
			$sibling_menu_exists = 1;
			// DEFINE SIBLING PAGES
			$sibling_pages = get_child_pages($parent_page);
		}
			
		// ENABLE CHILD MENU
		if ($OPTIONS['show_child_pages'] == 1){
			$child_menu_exists = 1;
			// DEFINE CHILD PAGES
			$child_pages = get_child_pages($current_page);
		}
		
		
		///// BUILD EXISTANT MENUS /////
		
		// BUILD CHILD MENU
		$child_menu = '';
		if ($child_menu_exists){
			$child_menu .= "<ul>";
			foreach($child_pages as $page){
				$child_menu .= "<li class='child_menu_item'><a href='" . get_permalink( $page->ID ) . "'>" .  $page->post_title . "</a></li>";
			}
			$child_menu .= "</ul>";
		}
		
		// BUILD SIBLING MENU
		$sibling_menu = '';
		if ($sibling_menu_exists){
			$sibling_menu .= "<ul>";
			foreach($sibling_pages as $page){
				if ($page->ID == $current_page){
					$sibling_menu .= "<li class='sibling_menu_item selected'><a href='" . get_permalink( $page->ID ) . "'>" .  $page->post_title . "</a></li>";
					$sibling_menu .= "<div style='margin-left:20px;'>".$child_menu."</div>";
				}
				else
					$sibling_menu .= "<li class='sibling_menu_item'><a href='" . get_permalink( $page->ID ) . "'>" .  $page->post_title . "</a></li>";
			}
			$sibling_menu .= "</ul>";
		}
		
		// BUILD PARENT MENU
		$parent_menu = '';
		if ($parent_menu_exists){
			$parent_menu .= "<ul>";
			foreach($parent_pages as $page){
				if ($page->ID == $parent_page){
					$parent_menu .= "<li class='parent_menu_item selected'><a href='" . get_permalink( $page->ID ) . "'>" .  $page->post_title . "</a></li>";
					$parent_menu .= "<li style='margin-left:20px;'>".$sibling_menu."</div>";
				}
				else
					$parent_menu .= "<li class='parent_menu_item'><a href='" . get_permalink( $page->ID ) . "'>" .  $page->post_title . "</a></li>";
			}
			$parent_menu .= "</ul>";
		}
		
		
		///// INSERT THE RIGHT MENU /////
		if ($parent_menu_exists)
			echo $parent_menu;
		else if ($sibling_menu_exists)
			echo $sibling_menu;
		else if ($child_menu_exists)
			echo $child_menu;
			
	}
	
	echo render_page_menu($OPTIONS, $grandparent_page->ID, $parent_page->ID, $current_page->ID);

}


?>