<?php
	$meta_value = _get( $vars, 'field.meta_value' );
	$values = _get( $vars, 'field.values' );
?>
<tr class="postworld form-field">
	<th scope="row" valign="top">
		<?php if( _get( $vars, 'field.icon' ) ) : ?>
			<i class="icon <?php echo $vars['field']['icon'] ?>"></i>
		<?php endif ?>
		<label for="<?php echo $vars['input_name'] ?>"><?php echo $vars['field']['label'] ?></label>
	</th>
	<td>
		<p class="description">
			<label>
				<select
					name="<?php echo $vars['input_name'] ?>">
					<?php foreach( $values as $v ) : ?>
						<option
							<?php if( $v['value'] == $meta_value ) echo 'selected' ?>
							value="<?php echo $v['value'] ?>">
							<?php echo $v['label'] ?>
						</option>
					<?php endforeach ?>
				</select>
				<?php echo $vars['field']['description'] ?>
			</label>
		</p>
	</td>
</tr>