<?php
	/**
	 * Select a feed from the list of pre-defined feeds
	 */
	$default_vars = array(
		'ng_model' => null,
		);
	$vars = array_replace($default_vars, $vars);

	$feeds = pw_get_option( array(
		'option_name'	=>	PW_OPTIONS_FEEDS,
		));
?>

<span class="icon-md"><i class="pwi-file"></i></span>
<select ng-model="<?php echo $vars['ng_model'] ?>">
	<?php foreach( $feeds as $feed ) : ?>
		<option value="<?php echo $feed['id']?>"><?php echo $feed['name'] . " (" . $feed['id'] . ")" ?></option>
	<?php endforeach ?>
</select>
