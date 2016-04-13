<?php
wp_enqueue_media();
$controller_id = $vars['field']['meta_key'].'_'.pw_random_string(8);
$term_id = $vars['term']->term_id;
$meta_key = $vars['field']['meta_key'];

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => $controller_id,
	'vars' => array(
		'imageObj' => array(
			'imageId' => (int) $vars['field']['meta_value'],
			),
		),
	));

?>

<tr class="postworld form-field" ng-controller="<?php echo $controller_id ?>">
	<th scope="row" valign="top">
		<?php if( _get( $vars, 'field.icon' ) ) : ?>
			<i class="icon <?php echo $vars['field']['icon'] ?>"></i>
		<?php endif ?>
		<label for="<?php echo $vars['input_name'] ?>"><?php echo $vars['field']['label'] ?></label>
	</th>
	<td>
		<div class="pw-row">
			<div class="pw-col-6">
				
				<?php
					echo pw_select_image_id( array( 
						'ng_model'		=>	'imageObj.imageId',
						'slug'			=>	'term_image',
						'label'			=>	$vars['field']['label'],
						'width'			=>	'400px',
					 	));?>
				
				<input
					type="text"
					class="pw-invisible"
					name="<?php echo $vars['input_name'] ?>"
					id="term_meta"
					ng-model="imageObj.imageId">
				<p class="description">
					<?php echo $vars['field']['description'] ?>
				</p>
			</div>
		</div>
	</td>
</tr>