<span pw-menus="<?php echo $vars['options_model'] ?>">
	<select
		ng-options="menu.term_id as menu.name for menu in <?php echo $vars['options_model'] ?>"
		ng-model="<?php echo $vars['ng_model'] ?>">
		<?php if( isset( $vars['null_option'] ) ) : ?>
			<option value=""><?php echo $vars['null_option'] ?></option>
		<?php endif ?>
	</select>
</span>