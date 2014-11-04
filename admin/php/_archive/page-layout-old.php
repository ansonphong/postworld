<?php
	// Load Globals
	global $i_options;

	// Define Variables
	$post_types = get_post_types( array( "public" => true ), 'names' );
	//echo json_encode($post_types);

	// Get Sidebars
	$I_Sidebars = new I_Sidebars();
	$sidebars = (array) $I_Sidebars->get_sidebars();

	$sidebar_options = array(
		array(
			'name'	=>	'Left',
			'slug'	=>	'left'
			),
		array(
			'name'	=>	'Right',
			'slug'	=>	'right'
			),
		);

?>
<div ng-controller="layoutMenu">
	<form method="post" action="options.php">
		<?php
			wp_nonce_field('update-options');
			settings_fields( 'infinite-theme-settings' );
			?>

		<table class="form-table" ng-cloak>

			<!--////////// LAYOUTS //////////-->
			<?php
				foreach($i_settings['layout'] as $setting){
				?>
					<tr valign="top" class="module" ng-controller="layoutModule" ng-init="layoutFormat='<?php echo get_option($setting['name']); ?>'">
						<th scope="row"><i class="<?php echo $setting['icon']; ?>"></i> <?php echo $setting['label']; ?></th>
						<td>
							<?php echo radio_image_select( $setting['name'], $setting['options'], 'ng-model="layoutFormat"' ); ?>
							
							<!-- SIDEBARS -->
							<div class="module sidebars" ng-show="show.sidebars">
								
								<b>Sidebars</b>
								<?php foreach( $sidebar_options as $sidebar_option ){ ?>
										<span class="select-module" ng-show="show.<?php echo $sidebar_option['slug'] ?>">
											<label><?php echo $sidebar_option['name'];  ?></label>
											<select>
												<?php foreach( $sidebars as $sidebar ){ ?>
													<option value=""></option>
													<option><?php echo $sidebar['name'] ?></option>
												<?php } ?>
											</select>
										</span>
								<?php } ?>
							</div>

							<!-- FOOTER -->
							<div class="module footer" ng-show="show.footer">
								<b>Footer</b>
								
							</div>

						</td>
					</tr>
				<?php
				}
			?>
			<tr valign="top">
				<th scope="row">Some Other Option</th>
				<td><input type="text" name="some_other_option" value="<?php echo get_option('some_other_option'); ?>" /></td>
			</tr>
		</table>

		LAYOUT OPTION PER POST TYPE : INCLUDING ( ARCHIVE, SINGLE ) <br>
		MENU POSITION<br>
		USER LOGIN POSITION<br>
		
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>
