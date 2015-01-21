<?php

if ( isset( $OPTIONS[ 'title' ] ) ) {
  $title = $OPTIONS[ 'title' ];
}
else {
  $title = __( 'Menu', 'text_domain' );
}

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
		

	#menu-kit-options td.label{
		font-weight:bold;
	}

	#menu-kit-options .type-title { font-size:14px; letter-spacing:1px; color:#666; }
	#menu-kit-options .type-options { margin:10px 0 0 10px; }
		
</style>

<div id="menu-kit-options" class="postworld">

	<!-- TITLE -->
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		
		<input type="checkbox" value="1" title="Show Title" name="<?php echo $this->get_field_name('show_title'); ?>" id="<?php echo $this->get_field_id('show_title'); ?>"<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
		<?php _e( 'Title:' ); ?>
	</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

	<!-- PAGES -->
	<div class="type-wrapper">
	
		<div class="type-title">
			<label>
				<input
					type="radio"
					value="pages"
					class="radio-type-select"
					id="<?php echo $this->get_field_id('menu_type'); ?>"
					name="<?php echo $this->get_field_name('menu_type'); ?>"
					<?php if( !empty($menu_type) && $menu_type == 'pages'){ echo 'checked="checked"'; } ?>
					/>
				<b>Pages</b>
			</label>
		</div>
		
		<div class="type-options">
			<label>
				<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_parent_pages'); ?>" id="<?php echo $this->get_field_id('show_parent_pages'); ?>" <?php if( !empty($show_parent_pages) && $show_parent_pages == '1'){ echo 'checked="checked"'; } ?> > Show Parent Pages<br>
			</label>
			<label>
				<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_sibling_pages'); ?>" id="<?php echo $this->get_field_id('show_sibling_pages'); ?>" <?php if( !empty($show_sibling_pages) && $show_sibling_pages == '1'){ echo 'checked="checked"'; } ?> >  Show Sibling Pages<br>
			</label>
			<label>
				<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('show_child_pages'); ?>" id="<?php echo $this->get_field_id('show_child_pages'); ?>" <?php if( !empty($show_child_pages) && $show_child_pages == '1'){ echo 'checked="checked"'; } ?> >  Show Child Pages<br>
			</label>
		</div>
		
	</div>
   
   
   <!-- TAXONOMIES -->
   <div class="type-wrapper">
   
		<div class="type-title">
			<label>
				<input
					type="radio"
					value="categories"
					class="radio-type-select"
					id="<?php echo $this->get_field_id('menu_type'); ?>"
					name="<?php echo $this->get_field_name('menu_type'); ?>"
					<?php if( !empty($menu_type) && $menu_type === 'categories'){ echo 'checked="checked"'; } ?>
					/>
				<b>Taxonomy</b>
			</label>
		</div>
		
		<div class="type-options">
		
			<table>
				<tr>
					<td class="label">
						Taxonomy
					</td>
					<td>
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
					</td>
				</tr>

				<tr>
					<td class="label">
						Post Types
					</td>
					<td>
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
					</td>
				</tr>

				<tr>
					<td class="label">
						Layout
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('taxonomy_layout'); ?>"  id="<?php echo $this->get_field_id('taxonomy_layout'); ?>">
							<option value="vertical" <?php if( !empty($taxonomy_layout) &&  $taxonomy_layout == 'vertical'){ echo 'selected="selected"'; }?> >Vertical</option>
							<option value="horizontal" <?php if( !empty($taxonomy_layout) && $taxonomy_layout == 'horizontal'){ echo 'selected="selected"'; }?>>Horizontal</option>
						</select>

						REMOVE
						
					</td>
				</tr>

				<tr>
					<td>
						
					</td>
					<td>
						<label>
							<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('taxonomy_hierarchical'); ?>" id="<?php echo $this->get_field_id('taxonomy_hierarchical'); ?>" <?php if( !empty($taxonomy_hierarchical) && $taxonomy_hierarchical == '1'){ echo 'checked="checked"'; } ?> >
							Hierarchical
						</label>
						<br>
						<label>
							<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('taxonomy_hide_empty'); ?>" id="<?php echo $this->get_field_id('taxonomy_hide_empty'); ?>" <?php if( !empty($taxonomy_hide_empty) && $taxonomy_hide_empty == '1'){ echo 'checked="checked"'; } ?> >
							Hide Empty
						</label>
					</td>
				</tr>

				<tr>
					<td>
						
					</td>
					<td>
						
					</td>
				</tr>


			</table>


			

		
			
		
		
		</div>
		
   </div>

	<!-- AUTHORS -->
	<div class="type-wrapper">
	   
	   	<div class="type-title">
	   		<label>
		  		<input 
		  			type="radio"
		  			value="authors"
		  			class="radio-type-select"
		  			id="<?php echo $this->get_field_id('menu_type'); ?>"
		  			name="<?php echo $this->get_field_name('menu_type'); ?>"
		  			<?php if( !empty($menu_type) && $menu_type === 'authors'){ echo 'checked="checked"'; } ?>
		  			/>
		  		<b>Authors</b>
		  	</label>
	   	</div>
	   
	   	<div class="type-options">
	   
	   		<label>
			   	<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('authors_hide_empty'); ?>" id="<?php echo $this->get_field_id('authors_hide_empty'); ?>" <?php if( !empty($authors_hide_empty) && $authors_hide_empty == '1'){ echo 'checked="checked"'; } ?> >
				Hide Empty
			</label>
			<br>
			<label>
			   	<input type="checkbox" value="1"  name="<?php echo $this->get_field_name('authors_show_admins'); ?>" id="<?php echo $this->get_field_id('authors_show_admins'); ?>" <?php if( !empty($authors_show_admins) && $authors_show_admins == '1'){ echo 'checked="checked"'; } ?> >
				Show Admins
			</label>
			
			<hr class="thin">

			<table>
				<tr>
					<td class="label">
						Avatar Size
					</td>
					<td>
						<input type="text" value="<?php if( !empty($authors_avatar_size) ) echo $authors_avatar_size; else echo '32'; ?>" size="3" name="<?php echo $this->get_field_name('authors_avatar_size'); ?>" id="<?php echo $this->get_field_id('authors_avatar_size'); ?>" >
		   				px
					</td>
				</tr>
				<tr>
					<td class="label">
						Role
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('authors_role'); ?>"  id="<?php echo $this->get_field_id('authors_role'); ?>">
							<option value="all" <?php if( !empty($authors_role) && $authors_role == 'all' ){ echo 'selected="selected"'; }?> >All</option>
							<option value="administrator" <?php if( !empty($authors_role) && $authors_role == 'administrator'){ echo 'selected="selected"'; }?> >Administrator</option>
							<option value="editor" <?php if( !empty($authors_role) && $authors_role == 'editor'){ echo 'selected="selected"'; }?> >Editor</option>
							<option value="author" <?php if( !empty($authors_role) && $authors_role == 'author'){ echo 'selected="selected"'; }?> >Author</option>
							<option value="contributor" <?php if( !empty($authors_role) && $authors_role == 'contributor'){ echo 'selected="selected"'; }?> >Contributor</option>
							<option value="subscriber" <?php if( !empty($authors_role) && $authors_role == 'subscriber'){ echo 'selected="selected"'; }?> >Subscriber</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">
						Order By
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('authors_order_by'); ?>"  id="<?php echo $this->get_field_id('authors_order_by'); ?>">
							<option value="display_name" <?php if( !empty($authors_order_by) && $authors_order_by == 'display_name'){ echo 'selected="selected"'; }?> >Display Name</option>
							<option value="nicename" <?php if( !empty($authors_order_by) && $authors_order_by == 'nicename'){ echo 'selected="selected"'; }?> >Nicename</option>
							<option value="ID" <?php if( !empty($authors_order_by) && $authors_order_by == 'ID'){ echo 'selected="selected"'; }?> >ID</option>
							<option value="post_count" <?php if( !empty($authors_order_by) && $authors_order_by == 'post_count'){ echo 'selected="selected"'; }?> >Post Count</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">
						Order
					</td>
					<td>
						<select name="<?php echo $this->get_field_name('authors_order'); ?>"  id="<?php echo $this->get_field_id('authors_order'); ?>">
							<option value="ASC" <?php if( !empty($authors_order) && $authors_order == 'ASC'){ echo 'selected="selected"'; }?> >Ascending</option>
							<option value="DESC" <?php if( !empty($authors_order) && $authors_order == 'DESC'){ echo 'selected="selected"'; }?> >Descending</option>
						</select>
					</td>
				</tr>
			</table>


	   	</div>
	   
	   <!--/*
		avatar_size
		role
		order_by
	   */-->
	   
	</div>
   
	<?php
		///// MENUS /////
	   $menus = get_terms( 'nav_menu' );

	   // Templates
	   $menu_templates = pw_get_menu_templates();

	   // Views
	   global $pwSiteGlobals;
	   $menu_views = $pwSiteGlobals['post_views'];
	?>


   	<!-- CUSTOM MENU TEMPLATE -->
	<div class="type-wrapper">
		<div class="type-title">

			<label>
				<input
					type="radio"
					value="custom_menu"
					class="radio-type-select"
					id="<?php echo $this->get_field_id('menu_type'); ?>"
					name="<?php echo $this->get_field_name('menu_type'); ?>"
					<?php if( !empty($menu_type) && $menu_type === 'custom_menu'){ echo 'checked="checked"'; } ?>
					/>
				<b>Custom Menu Template</b>
			</label>
			<br>
		</div>

		<table>
			<tr>
				<td class="label">
					Menu
				</td>
				<td>
					<select name="<?php echo $this->get_field_name('menu_slug'); ?>"  id="<?php echo $this->get_field_id('menu_slug'); ?>">
						<?php
							foreach( $menus as $menu ){?>
								<option value="<?php echo $menu->slug; ?>" <?php if( !empty($menu_slug) && $menu_slug == $menu->slug){ echo 'selected="selected"'; }?> ><?php echo $menu->name; ?></option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label">
					Template
				</td>
				<td>
					<select name="<?php echo $this->get_field_name('menu_template'); ?>"  id="<?php echo $this->get_field_id('menu_template'); ?>">
						<?php
							foreach( $menu_templates as $menu_template_key => $menu_template_value ){?>
								<option value="<?php echo $menu_template_key; ?>" <?php if( !empty($menu_template) && $menu_template == $menu_template_key){ echo 'selected="selected"'; }?> ><?php echo $menu_template_key; ?></option>
						
							<?php
							}
						?>

					</select>
				</td>
			</tr>
		</table>
		
   </div>



   <!-- CUSTOM MENU FEED -->
	<div class="type-wrapper">
		<div class="type-title">
			<label>
				<input
					type="radio"
					value="menu_feed"
					class="radio-type-select"
					id="<?php echo $this->get_field_id('menu_type'); ?>"
					name="<?php echo $this->get_field_name('menu_type'); ?>"
					<?php if( !empty($menu_type) && $menu_type === 'menu_feed'){ echo 'checked="checked"'; } ?>
					/>
				<b>Menu Feed</b>
			</label>

			<br>
		</div>

		<table>
			<tr>
				<td class="label">
					Menu
				</td>
				<td>
					<select
						name="<?php echo $this->get_field_name('menu_feed_id'); ?>"
						id="<?php echo $this->get_field_id('menu_feed_id'); ?>">
						
						<?php
							foreach( $menus as $menu ){?>
								<option
									value="<?php echo $menu->term_id; ?>"
									<?php if( !empty($menu_feed_id) && $menu_feed_id == $menu->term_id){ echo 'selected="selected"'; }?>
									>
									<?php echo $menu->name; ?>
								</option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label">
					View
				</td>
				<td>
					<select
						name="<?php echo $this->get_field_name('menu_feed_view'); ?>"
						id="<?php echo $this->get_field_id('menu_feed_view'); ?>">
							
							<?php foreach( $menu_views as $menu_view ):?>
								<option
									value="<?php echo $menu_view; ?>"
									<?php if( !empty($menu_feed_view) && $menu_feed_view == $menu_view){ echo 'selected="selected"'; }?>
									>
									<?php echo $menu_view; ?>
								</option>
							<?php endforeach; ?>

					</select>
				</td>
			</tr>
		</table>

   </div>


</div>
 
