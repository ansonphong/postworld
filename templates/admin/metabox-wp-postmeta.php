<table width="100%">
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
					<td width="75%">
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
			///// IMAGE ID /////
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

			<?php
			///// ICON /////
			case 'icon': ?>
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
							echo pw_select_icon_options( array(
								'ng_model' => 'fields.'.$meta_key.'.meta_value'
								));?>

					</td>
				</tr>
			<?php break; ?>

			<?php
			///// RADIO BUTTONS /////
			case 'radio-buttons': ?>
				<tr>
					<td valign="top">
						<i class="<?php echo $field['icon']; ?>"></i>
						<b><?php echo $field['label']; ?></b>
						<?php if( !empty( $field['description'] ) ): ?>
							<br><small><?php echo $field['description']; ?></small>
						<?php endif; ?>
					</td>
					<td width="75%" valign="top">

						<div class="btn-group">
	
							<?php foreach( $field['options'] as $option ): ?>

								<?php if( in_array( 'custom_default', $field['supports'] ) && $option['value'] !== 'default' ): ?>
									<label
										class="btn btn-radio-default"
										ng-model="fields.<?php echo $meta_key ?>.default_value"
										uib-btn-radio="'<?php echo $option['value'] ?>'"
										uib-tooltip="Set as Default" tooltip-popup-delay="333">
										<!--  tooltip-append-to-body="true" -->
										<i class="icon"></i>
									</label>
								<?php endif ?>

								<label
									class="btn"
									ng-model="fields.<?php echo $meta_key ?>.meta_value"
									uib-btn-radio="'<?php echo $option['value'] ?>'">
									<?php if(!empty($option['icon'])): ?>
										<i class="icon <?php echo $option['icon'] ?>"></i>
									<?php endif ?>
									<?php echo $option['label'] ?>
								</label>

							<?php endforeach ?>
						</div>

						<?php
							foreach( $field['options'] as $option ):
								if( _get($option,'description') ): ?>

								<div ng-show="'<?php echo $option['value'] ?>' == fields.<?php echo $meta_key ?>.meta_value">
									<hr class="thin">
									<small><?php echo $option['description'] ?></small>
								</div>

								<?php
								endif;
							endforeach; ?>

					</td>
				</tr>
			<?php break; ?>

		<?php endswitch; ?>

	<?php endforeach; ?>
</table>

<!--<pre>{{ fields | json }}</pre>-->
<input type="hidden" name="pw_wp_postmeta_fields" value="{{ fields | json }}">
<!-- <?php echo json_encode( $fields );?> -->
