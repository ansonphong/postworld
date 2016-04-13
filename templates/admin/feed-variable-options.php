<div class="pw-row">
	<div
		class="pw-col-3"
		ng-show="<?php echo $vars['ng_model']; ?>.view.current == 'grid'">
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
			<option value="">Default</option>
		</select>
	</div>
</div>