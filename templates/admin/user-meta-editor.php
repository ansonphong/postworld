<?php
	wp_enqueue_media();
	$instance = pw_random_string(8);

	if( !isset( $vars['settings'] ) )
		$vars['settings'] = array();

	$defaultSettings = array(
		'textarea_name'	=>	'pwusermeta['.$vars['meta_key'].']',
		);

	$settings = array_replace_recursive( $defaultSettings, $vars['settings'] );

?>

<h3><i class="<?php echo $vars['icon'] ?>"></i> <?php echo $vars['label'] ?></h3>

<div>
	<table
		class="form-table">
		<tr>
			<th><label><?php echo $vars['description'] ?></label></th>
			<td>
				<?php wp_editor( $vars['value'], $vars['meta_key'], $settings ); ?>
			</td>
		</tr>
	</table>
</div>
