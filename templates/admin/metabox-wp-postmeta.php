<table
	width="100%">
	<?php foreach( $fields as $meta_key => $field ) : ?>

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
					<td valign="top">
						<i class="<?php echo $field['icon']; ?>"></i>
						<b><?php echo $field['label']; ?></b>
						<?php if( !empty( $field['description'] ) ): ?>
							<br><small><?php echo $field['description']; ?></small>
						<?php endif; ?>
					</td>
					<td
						width="75%">
						
						<input
							type="text"
							name="pw_wp_postmeta[<?php echo $field['meta_key'] ?>]"
							placeholder="<?php echo $field['placeholder'] ?>"
							ng-model="fields.<?php echo $meta_key ?>.meta_value"
							style="width:100%">
					</td>
				</tr>
			<?php break; ?>

			<?php
			///// SELECT INPUT /////
			case 'select-input': ?>
				<tr>
					<td valign="top">
						<i class="<?php echo $field['icon']; ?>"></i>
						<b><?php echo $field['label']; ?></b>
						<?php if( !empty( $field['description'] ) ): ?>
							<br><small><?php echo $field['description']; ?></small>
						<?php endif; ?>
					</td>
					<td
						width="75%">
						
						<select
							name="pw_wp_postmeta[<?php echo $field['meta_key'] ?>]"
							ng-model="fields.<?php echo $meta_key ?>.meta_value">
							<?php foreach( $field['options'] as $option ): ?>
								<option value="<?php echo $option['value'] ?>"><?php echo $option['label'] ?></option>
							<?php endforeach ?>
						</select>

					</td>
				</tr>
			<?php break; ?>

			<?php
			///// TEXT INPUT /////
			case 'image-id': ?>
				<tr>
					<td valign="top">
						<i class="<?php echo $field['icon']; ?>"></i>
						<b><?php echo $field['label']; ?></b>
						<?php if( !empty( $field['description'] ) ): ?>
							<br><small><?php echo $field['description']; ?></small>
						<?php endif; ?>
					</td>
					<td
						width="75%">
						<?php
							echo pw_select_image_id( array( 
								'ng_model'		=>	'fields.'.$meta_key.'.meta_value',
								'slug'			=>	'image_'.$meta_key,
								'label'			=>	'Image',
								'width'			=>	'250px',
							 	));?>
					</td>
				</tr>
			<?php break; ?>

		<?php endswitch; ?>

	<?php endforeach; ?>
</table>

<!--<pre>{{ fields | json }}</pre>-->
<input type="hidden" name="pw_wp_postmeta_fields" value="{{ fields | json }}">
<!-- <?php echo json_encode( $fields );?> -->
