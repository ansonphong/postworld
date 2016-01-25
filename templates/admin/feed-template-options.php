<div class="pw-row">
	<div class="pw-col-3">
		<label
			for="feed_view"
			class="inner">
			<?php _e( 'View', 'postworld') ?>
		</label>
		<select
			id="feed_view"
			class="labeled"
			ng-model="<?php echo $vars['ng_model']; ?>.view.current"
			ng-options="value for value in feedOptions.view">
			<option value=""><?php _ex( 'Default', 'option', 'postworld') ?></option>
		</select>
	</div>
	<?php /*
	<!--
	<div class="pw-col-3">
		<label
			for="feed_view_options"
			class="inner">
			<?php _ex( 'View Options', 'template views', 'postworld') ?>
		</label>
		<select
			id="feed_view_options"
			class="labeled"
			ng-model="<?php echo $vars['ng_model']; ?>.view.options"
			ng-options="value for value in feedOptions.view"
			multiple>
		</select>
	</div>
	-->
	<!--
	<div class="pw-col-3">
		<label
			for="item-aux_template"
			class="inner">
			<?php _e( 'Auxilary Template', 'postworld') ?>
		</label>
		<select
			id="item-aux_template"
			class="labeled"
			ng-model="<?php echo $vars['ng_model']; ?>.aux_template"
			ng-options="key as key for (key, value) in phpFeedTemplates">
			<option value=""><?php _ex( 'None', 'option', 'postworld') ?></option>
		</select>
	</div>
	-->
	*/ ?>
</div>