<?php

if ( isset( $OPTIONS[ 'title' ] ) ) {
  $title = $OPTIONS[ 'title' ];
}
else {
  $title = __( 'Menu', 'text_domain' );
}

/*
// GLOBAL SETTINGS : LOAD
$show_title = $OPTIONS['show_title'];
$menu_type = $OPTIONS['menu_type'];


// PAGES SETTINGS : LOAD
$show_parent_pages = $OPTIONS['show_parent_pages'];
$show_sibling_pages = $OPTIONS['show_sibling_pages'];
$show_child_pages = $OPTIONS['show_child_pages'];

// TAXONOMY SETTINGS : LOAD
$taxonomy = $OPTIONS['taxonomy'];
*/


extract($OPTIONS);

?>

<style>
	#menu-kit-options .type-wrapper {
		border:1px solid #ccc; border-radius: 4px;
		margin-top:5px;
		padding: 10px;
		
		}
	
	#menu-kit-options .radio-type-select {
		 margin-right:5px;
		}
		
	#menu-kit-options .type-title { font-size:14px; letter-spacing:1px; color:#666; }
	#menu-kit-options .type-options { margin:10px 0 0 10px; }
		
</style>

<div id="menu-kit-options">

	<!-- TITLE -->
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		
		<input type="checkbox" value="1" title="Show Title" name="<?php echo $this->get_field_name('show_title'); ?>" id="<?php echo $this->get_field_id('show_title'); ?>" <?php if($show_title == '1'){ echo 'checked="checked"'; } ?> >
		<?php _e( 'Title:' ); ?>
	</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

	<!-- PAGES -->
	<div class="type-wrapper">
	
		<div class="type-title">
			<input type="radio" value="pages" class="radio-type-select" id="<?php echo $this->get_field_id('menu_type'); ?>" name="<?php echo $this->get_field_name('menu_type'); ?>" <?php if($menu_type == 'pages'){ echo 'checked="checked"'; } ?> />
			<b>Pages</b>
		</div>
		
		<div class="type-options">
			<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_parent_pages'); ?>" id="<?php echo $this->get_field_id('show_parent_pages'); ?>" <?php if($show_parent_pages == '1'){ echo 'checked="checked"'; } ?> > Show Parent Pages<br>
			<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_sibling_pages'); ?>" id="<?php echo $this->get_field_id('show_sibling_pages'); ?>" <?php if($show_sibling_pages == '1'){ echo 'checked="checked"'; } ?> >  Show Sibling Pages<br>
			<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_child_pages'); ?>" id="<?php echo $this->get_field_id('show_child_pages'); ?>" <?php if($show_child_pages == '1'){ echo 'checked="checked"'; } ?> >  Show Child Pages<br>
		</div>
		
	</div>
   
   
   <!-- TAXONOMIES -->
   <div class="type-wrapper">
   
		<div class="type-title">
			<input type="radio" value="categories" class="radio-type-select" id="<?php echo $this->get_field_id('menu_type'); ?>" name="<?php echo $this->get_field_name('menu_type'); ?>" <?php if($menu_type === 'categories'){ echo 'checked="checked"'; } ?> />
			<label><b>Categories</b></label>
		</div>
		
		<div class="type-options">
		
			Taxonomy: 
			
			<select name="<?php echo $this->get_field_name('taxonomy'); ?>"  id="<?php echo $this->get_field_id('taxonomy'); ?>">
				<?php
				$the_taxonomies = get_taxonomies();
				
				// EXTRACT JUST SUPPORTED POST TYPES
				$taxonomy_names = array();
				foreach( $the_taxonomies as $key => $value){
					array_push($taxonomy_names, $key); 
				}
				
				foreach($taxonomy_names as $the_taxonomy) {
					$selected = '';
					if ( $the_taxonomy == $taxonomy ){ $selected = 'selected="selected"'; }
					echo '<option value="'.$the_taxonomy.'" '.$selected.' >'.$the_taxonomy.'</option>';
				}
				?>
			</select>
			
			<?php
			/* // EXPERIMENT WITH EXTRCATING ONLY HEIRARCHICAL TAXONOMIES
				$taxonomies_object = get_taxonomies(array(), 'object');
				echo print_r($taxonomies_object);
			*/
			?>
			<br>
			Post Types:
			
			<select name="<?php echo $this->get_field_name('post_type'); ?>"  id="<?php echo $this->get_field_id('post_type'); ?>">
				<?php
				$the_post_types = get_post_types();
				
				// EXTRACT JUST SUPPORTED POST TYPES
				$post_type_names = array();
				foreach( $the_post_types as $key => $value){
					array_push($post_type_names, $key); 
				}
				
				foreach($post_type_names as $the_post_type) {
					$selected = '';
					if ( $the_post_type == $post_type ){ $selected = 'selected="selected"'; }
					echo '<option value="'.$the_post_type.'" '.$selected.' >'.$the_post_type.'</option>';
				}
				?>
			</select>
		
			<br><br>
			Layout:
			<select name="<?php echo $this->get_field_name('taxonomy_layout'); ?>"  id="<?php echo $this->get_field_id('taxonomy_layout'); ?>">
				<option value="vertical" <?php if($taxonomy_layout == 'vertical'){ echo 'selected="selected"'; }?> >Vertical</option>
				<option value="horizontal" <?php if($taxonomy_layout == 'horizontal'){ echo 'selected="selected"'; }?>>Horizontal</option>
			</select>
			
			<br><br>
			<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('taxonomy_hierarchical'); ?>" id="<?php echo $this->get_field_id('taxonomy_hierarchical'); ?>" <?php if($taxonomy_hierarchical == '1'){ echo 'checked="checked"'; } ?> >
			Hierarchical<br>
			<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('taxonomy_hide_empty'); ?>" id="<?php echo $this->get_field_id('taxonomy_hide_empty'); ?>" <?php if($taxonomy_hide_empty == '1'){ echo 'checked="checked"'; } ?> >
			Hide Empty<br>
		
		
		</div>
		
   </div>

   <!-- AUTHORS -->
   <div class="type-wrapper">
	   
	   <div class="type-title">
		   <input type="radio" value="authors" class="radio-type-select" id="<?php echo $this->get_field_id('menu_type'); ?>" name="<?php echo $this->get_field_name('menu_type'); ?>" <?php if($menu_type === 'authors'){ echo 'checked="checked"'; } ?> />
		   <label><b>Authors</b></label>
	   </div>
	   
	   <div class="type-options">
	   
		   <input type="checkbox" value="1"  name="<?php echo $this->get_field_name('authors_hide_empty'); ?>" id="<?php echo $this->get_field_id('authors_hide_empty'); ?>" <?php if($authors_hide_empty == '1'){ echo 'checked="checked"'; } ?> >
				Hide Empty<br>
				
		   <input type="checkbox" value="1"  name="<?php echo $this->get_field_name('authors_show_admins'); ?>" id="<?php echo $this->get_field_id('authors_show_admins'); ?>" <?php if($authors_show_admins == '1'){ echo 'checked="checked"'; } ?> >
				Show Admins<br>
				
				Avatar Size :
		   <input type="text" value="<?php if($authors_avatar_size) echo $authors_avatar_size; else echo '32'; ?>" size="3" name="<?php echo $this->get_field_name('authors_avatar_size'); ?>" id="<?php echo $this->get_field_id('authors_avatar_size'); ?>" >
				
		   <br><br>
			Role:
			<select name="<?php echo $this->get_field_name('authors_role'); ?>"  id="<?php echo $this->get_field_id('authors_role'); ?>">
				<option value="all" <?php if($authors_role == 'all'){ echo 'selected="selected"'; }?> >All</option>
				<option value="administrator" <?php if($authors_role == 'administrator'){ echo 'selected="selected"'; }?> >Administrator</option>
				<option value="editor" <?php if($authors_role == 'editor'){ echo 'selected="selected"'; }?> >Editor</option>
				<option value="author" <?php if($authors_role == 'author'){ echo 'selected="selected"'; }?> >Author</option>
				<option value="contributor" <?php if($authors_role == 'contributor'){ echo 'selected="selected"'; }?> >Contributor</option>
				<option value="subscriber" <?php if($authors_role == 'subscriber'){ echo 'selected="selected"'; }?> >Subscriber</option>
			</select>
			
			<br>
			Order By:
			<select name="<?php echo $this->get_field_name('authors_order_by'); ?>"  id="<?php echo $this->get_field_id('authors_order_by'); ?>">
				<option value="display_name" <?php if($authors_order_by == 'display_name'){ echo 'selected="selected"'; }?> >Display Name</option>
				<option value="nicename" <?php if($authors_order_by == 'nicename'){ echo 'selected="selected"'; }?> >Nicename</option>
				<option value="ID" <?php if($authors_order_by == 'ID'){ echo 'selected="selected"'; }?> >ID</option>
				<option value="post_count" <?php if($authors_order_by == 'post_count'){ echo 'selected="selected"'; }?> >Post Count</option>
			</select>
			
			<br>
			Order:
			<select name="<?php echo $this->get_field_name('authors_order'); ?>"  id="<?php echo $this->get_field_id('authors_order'); ?>">
				<option value="ASC" <?php if($authors_order == 'ASC'){ echo 'selected="selected"'; }?> >Ascending</option>
				<option value="DESC" <?php if($authors_order == 'DESC'){ echo 'selected="selected"'; }?> >Descending</option>
			</select>
				
	   </div>
	   
	   <!--/*
		avatar_size
		role
		order_by
	   
	   */-->
	   
	   
   </div>
	  
   
   <!-- CUSTOM MENUS -->
	<div class="type-wrapper">
		<div class="type-title">
			<input type="radio" value="custom_menu" class="radio-type-select" id="<?php echo $this->get_field_id('menu_type'); ?>" name="<?php echo $this->get_field_name('menu_type'); ?>" <?php if($menu_type === 'custom_menu'){ echo 'checked="checked"'; } ?> />
			<label><b>Custom Menu</b></label>

			<select name="<?php echo $this->get_field_name('menu_slug'); ?>"  id="<?php echo $this->get_field_id('menu_slug'); ?>">
				<?php
					$menus = get_terms( 'nav_menu' );
					foreach( $menus as $menu ){?>

						<option value="<?php echo $menu->slug; ?>" <?php if($menu_slug == $menu->slug){ echo 'selected="selected"'; }?> ><?php echo $menu->name; ?></option>

					<?php
					}
				?>

			</select>

		</div>
   
   </div>

</div>

<script>
jQuery(document).ready(function() {
	
	
	
	});

</script>
	   
