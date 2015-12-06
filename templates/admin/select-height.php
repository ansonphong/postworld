<?php
	/**
	 * Select the height values for input / use with
	 * pwHeight (pw-height) Angular Directive.
	 */

	$default_vars = array(
		'ng_model' => null,
		'methods' => array( 'window-base', 'window-percent', 'pixels' ),
		);
	$vars = array_replace($default_vars, $vars);

	$methods = array(
		array(
			'label' => 'Window Base',
			'value' => 'window-base'
			),
		array(
			'label' => 'Window Percent',
			'value' => 'window-percent'
			),
		array(
			'label' => 'Pixels',
			'value' => 'pixels'
			),
		);
?>

<select ng-model="<?php echo $vars['ng_model'] ?>.method">
	<?php foreach( $methods as $method ) : ?>
		<?php if( in_array( $method['value'], $vars['methods'] ) ) : ?>
			<option value="<?php echo $method['value']?>"><?php echo $method['label']?></option>
		<?php endif ?>
	<?php endforeach ?>
</select>

<hr class="thin">

<div ng-switch="<?php echo $vars['ng_model'] ?>.method">
	<div ng-switch-when="window-base">
		<small>
			Sized to touch the base of the window, to maximize screen area.
		</small>
	</div>
	<div ng-switch-when="window-percent">
		<input
			type="number"
			class="short"
			ng-model="<?php echo $vars['ng_model'] ?>.value"> % 
		Height
		<small>
			Percentage of window height.
		</small>
	</div>
	<div ng-switch-when="pixels">
		<input
			type="number"
			class="short"
			ng-model="<?php echo $vars['ng_model'] ?>.value"> px 
		Height
		<small>
			How many pixels tall.
		</small>
	</div>
</div>