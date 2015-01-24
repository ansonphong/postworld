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
					<td>
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
			///// TEXT INPUT /////
			case 'image-id': ?>
				<tr>
					<td>
						<i class="<?php echo $field['icon']; ?>"></i>
						<b><?php echo $field['label']; ?></b>
						<?php if( !empty( $field['description'] ) ): ?>
							<br><small><?php echo $field['description']; ?></small>
						<?php endif; ?>
					</td>
					<td
						width="75%">
						
						//////////
						Image Input Here
						//////////
						Use WP MediaLibrary Directive, use callback to 

					</td>
				</tr>
			<?php break; ?>



		<?php endswitch; ?>

	<?php endforeach; ?>
</table>

<pre>{{ fields | json }}</pre>

<input type="hidden" name="pw_wp_postmeta_fields" value="{{ fields | json }}">
<!-- <?php echo json_encode( $fields );?> -->
