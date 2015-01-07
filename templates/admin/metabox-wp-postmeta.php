<table
	width="100%">
	<?php foreach( $fields as $field ) : ?>

		<?php
		switch( $field['type'] ):

			///// HEADER /////
			case 'header': ?>
				<tr>
					<td colspan="2">
						<h4
							style="border-bottom:1px solid #ccc; padding-bottom:5px; margin-bottom:5px;">
							<i class="<?php echo $field['icon']; ?>"></i>
							<?php echo $field['label']; ?>
						</h4>
					</td>
				</tr>
			<?php break; ?>


			<?php
			///// TEXT INPUT /////
			case 'text-input': ?>
				<tr>
					<td>
						<i class="<?php echo $field['icon']; ?>"></i>
						<?php echo $field['label']; ?>
					</td>
					<td
						width="75%">
						
						<input
							type="text"
							name="pw_wp_postmeta_<?php echo $field['meta_key'] ?>"
							placeholder="<?php echo $field['placeholder'] ?>"
							value="<?php echo $field['meta_value'] ?>"
							style="width:100%">
							
					</td>
				</tr>
			<?php break; ?>



		<?php endswitch; ?>

	<?php endforeach; ?>
</table>

<input type="hidden" name="pw_wp_postmeta_fields" value='<?php echo json_encode( $fields );?>'>

