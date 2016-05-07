<?php
/**
 * Adds custom feed options to the admin for grid post view.
 */
add_action( 'pw_feed_options_view_'.'grid', 'pw_feed_options_view_grid_template' );
function pw_feed_options_view_grid_template( $vars ){
	?>
	<div class="pw-row">
		<div class="pw-col-3">
			<label
				for="grid_columns"
				class="inner">
				<?php _e( 'Grid Columns', 'postworld') ?>
			</label>
			<select
				id="grid_columns"
				class="labeled"
				ng-options="value as value for value in feedOptions.views.grid.columns"
				ng-model="<?php echo $vars['ng_model']; ?>.options.views.grid.columns">
				<option value=""><?php _e('Default','postworld') ?></option>
			</select>
		</div>
	</div>
	<?php
}