<?
	// Load Globals
	global $i_admin_urls;

	// Load Up Infinite Sidebars Class
	$I_Sidebars = new I_Sidebars();
	$sidebars = $I_Sidebars->get_sidebars();

	// Check if ID is set in URL
	if(isset($_GET['id']))
		$sidebar_id = $_GET['id'];

	// Cycle through loaded sidebars
	foreach( $sidebars as $sidebar ){
		if( $sidebar['id'] == $sidebar_id ){
			// Get the object of the current selected sidebar by ID
			$edit_sidebar = $sidebar;
			break;
		}
	}

	// Define Message
	if( !is_array($edit_sidebar) )
		$message = "error-exist";
	else
		$message = null;

?>

<div id="col-container">

	<?php i_display_messages($message); ?>

	<div class="col-wrap">
		<div class="form-wrap">
			<form action="<?php echo admin_url( 'admin-post.php' ); ?>">
				<input type="hidden" name="action" value="i_update_sidebar">
				<input type="hidden" name="id" value="<?php echo $edit_sidebar['id']; ?>">

				<p class="submit">
					<a href="<?php echo $i_admin_urls['sidebars']; ?>"><button name="cancel" id="cancel" class="button" type="button"><?php echo $i_language['general']['back']; ?></button></a>
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $i_language['sidebars']['update']; ?>">
				</p>
				<div class="form-field form-required">
					<label for="tag-name"><?php echo $i_language['sidebars']['name']; ?></label>
					<input name="name" id="sidebar-name" type="text" value="<?php echo $edit_sidebar['name']; ?>" size="40" aria-required="true">
					<p><?php echo $i_language['sidebars']['name_info']; ?></p>
				</div>
				<div class="form-field">
					<label for="tag-id"><?php echo $i_language['sidebars']['id']; ?></label>
					<input name="id" id="sidebar-id" type="text" value="<?php echo $edit_sidebar['id']; ?>" size="40" disabled>
					<p><?php echo $i_language['sidebars']['id_info']; ?></p>
				</div>
				<div class="form-field">
					<label for="tag-class"><?php echo $i_language['sidebars']['class']; ?></label>
					<input name="class" id="sidebar-class" type="text" value="<?php echo $edit_sidebar['class']; ?>" size="40">
					<p><?php echo $i_language['sidebars']['class_info']; ?></p>
				</div>
				<div class="form-field">
					<label for="tag-description"><?php echo $i_language['sidebars']['description']; ?></label>
					<textarea name="description" id="sidebar-description" rows="5" cols="40"><?php echo $edit_sidebar['description']; ?></textarea>
					<p><?php echo $i_language['sidebars']['description_info']; ?></p>
				</div>

				<div class="form-field">
					<label for="tag-before_widget"><?php echo $i_language['sidebars']['before_widget']; ?></label>
					<textarea name="before_widget" id="sidebar-before_widget" rows="5" cols="40"><?php echo $edit_sidebar['before_widget']; ?></textarea>
					<p><?php echo $i_language['sidebars']['before_widget_info']; ?></p>
				</div>

				<div class="form-field">
					<label for="tag-after_widget"><?php echo $i_language['sidebars']['after_widget']; ?></label>
					<textarea name="after_widget" id="sidebar-after_widget" rows="5" cols="40"><?php echo $edit_sidebar['after_widget']; ?></textarea>
					<p><?php echo $i_language['sidebars']['after_widget_info']; ?></p>
				</div>

				<div class="form-field">
					<label for="tag-before_title"><?php echo $i_language['sidebars']['before_title']; ?></label>
					<textarea name="before_title" id="sidebar-before_title" rows="5" cols="40"><?php echo $edit_sidebar['before_title']; ?></textarea>
					<p><?php echo $i_language['sidebars']['before_title_info']; ?></p>
				</div>

				<div class="form-field">
					<label for="tag-after_title"><?php echo $i_language['sidebars']['after_title']; ?></label>
					<textarea name="after_title" id="sidebar-after_title" rows="5" cols="40"><?php echo $edit_sidebar['after_title']; ?></textarea>
					<p><?php echo $i_language['sidebars']['after_title_info']; ?></p>
				</div>

			</form>
		</div>
	</div>
</div>