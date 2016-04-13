<?php
	$controller_id = $vars['field']['meta_key'].'_'.pw_random_string(8);
	$term_id = $vars['term']->term_id;
	$meta_key = $vars['field']['meta_key'];
?>
<script type="text/javascript">
	postworldAdmin.controller( '<?php echo $controller_id ?>',
		[ '$scope', '$_',
		function( $scope, $_ ){
		var className = <?php echo json_encode( $vars['field']['meta_value'] ) ?>;
		$scope.iconObj = { className: className };
	}]);
</script>
<tr class="postworld form-field" ng-controller="<?php echo $controller_id ?>" ng-cloak>
	<th scope="row" valign="top">
		<?php if( _get( $vars, 'field.icon' ) ) : ?>
			<i class="icon <?php echo $vars['field']['icon'] ?>"></i>
		<?php endif ?>
		<label for="<?php echo $vars['input_name'] ?>"><?php echo $vars['field']['label'] ?></label>
	</th>
	<td>
		<div class="pw-row">
			<div class="pw-col-6">
				<?php echo pw_admin_select_icon( array( 'ng_model' => 'iconObj.className' ) ); ?>
			</div>
		</div>
		<input
			type="text"
			class="pw-invisible"
			name="<?php echo $vars['input_name'] ?>"
			id="term_meta"
			ng-model="iconObj.className">
		<p class="description">
			<?php echo $vars['field']['description'] ?>
		</p>
	</td>
</tr>