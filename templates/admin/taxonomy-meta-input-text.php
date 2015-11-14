<?php
	$meta_value = _get( $vars, 'field.meta_value' );
	if( empty( $meta_value ) )
		$meta_value = '';
?>
<tr class="postworld form-field">
	<th scope="row" valign="top">
		<?php if( _get( $vars, 'field.icon' ) ) : ?>
			<i class="icon <?php echo $vars['field']['icon'] ?>"></i>
		<?php endif ?>
		<label for="<?php echo $vars['input_name'] ?>"><?php echo $vars['field']['label'] ?></label>
	</th>
	<td>
		<input
			type="text"
			class=""
			name="<?php echo $vars['input_name'] ?>"
			id="term_meta"
			value="<?php echo $meta_value ?>">
		<p class="description">
			<?php echo $vars['field']['description'] ?>
		</p>
	</td>
</tr>