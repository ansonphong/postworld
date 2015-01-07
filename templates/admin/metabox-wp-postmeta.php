<table>
	<?php foreach( $fields as $field ) : ?>
		<tr>
			<td>
				<i class="<?php echo $field['icon']; ?>"></i>
				<?php echo $field['label']; ?>
			</td>
			<td>
				<?php
					switch( $field['input_type'] ):
						case 'text':
						?>
							<input
								type="text"
								name="pw_wp_postmeta_<?php echo $field['meta_key'] ?>"
								placeholder="<?php echo $field['placeholder'] ?>"
								value="<?php echo $field['meta_value'] ?>">
						<?php
						break;
					endswitch;
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<input type="hidden" name="pw_wp_postmeta_fields" value='<?php echo json_encode( $fields );?>'>

