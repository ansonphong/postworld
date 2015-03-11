<?php
	wp_enqueue_media();
	$meta_key = $vars['field']['meta_key'];
?>
<tr class="postworld form-field" ng-cloak>
	<th scope="row" valign="top">
		<label for="<?php echo $vars['input_name'] ?>"><?php echo $vars['field']['label'] ?></label>
	</th>
	<td>
		<?php
			wp_editor(
				$vars['field']['meta_value'],
				$meta_key,
				array(
					'textarea_name'	=>	$vars['input_name'],
					) );
		?>
		<p class="description">
			<?php echo $vars['field']['description'] ?>
		</p>
	</td>
</tr>