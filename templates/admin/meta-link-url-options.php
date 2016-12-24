<?php
	if( empty( $vars['show_options'] ) )
		$show_options = array( 'icon', 'label', 'highlight', 'new_target' );
?>
<div class="link-url-options">
	<div class="btn-group">
		<label
			class="btn"
			ng-repeat="item in <?php echo $vars['options_model']['show']; ?>"
			ng-model="<?php echo $vars['ng_model']; ?>.label.show"
			uib-btn-radio="item.value">
			{{ item.name }}
		</label>
	</div>

	<div ng-show="<?php echo $vars['ng_model']; ?>.label.show == 'custom'">
		<hr class="thin">
		<table>
			<?php if( in_array('icon', $show_options) ): ?>
			<tr>
				<td class="label">Icon : </td>
				<td class="control">
					<?php echo pw_select_icon_options( array( 'ng_model' => $vars['ng_model'].'.icon' ) ); ?>
				</td>
			</tr>
			<?php endif ?>

			<?php if( in_array('label', $show_options) ): ?>
			<tr>
				<td class="label">Label :</td>
				<td class="control">
					<input
						type="text"
						ng-model="<?php echo $vars['ng_model']; ?>.label.custom"
						placeholder="Custom Link Label">
				</td>
			</tr>
			<?php endif ?>

			<?php if( in_array('highlight', $show_options) ): ?>
			<tr>
				<td class="label">Highlight :</td>
				<td class="control">
					<div class="btn-group">
						<label
							class="btn"
							ng-repeat="item in <?php echo $vars['options_model']['highlight']; ?>"
							ng-model="<?php echo $vars['ng_model']; ?>.label.highlight"
							uib-btn-radio="item.value">
							{{ item.name }}
						</label>
					</div>
				</td>
			</tr>
			<?php endif ?>

			<?php if( in_array('new_target', $show_options) ): ?>
			<tr>
				<td class="label">Open in new tab : </td>
				<td class="control">
					<div class="btn-group">
						<label
							class="btn"
							ng-repeat="item in <?php echo $vars['options_model']['new_target']; ?>"
							ng-model="<?php echo $vars['ng_model']; ?>.new_target"
							uib-btn-radio="item.value">
							{{ item.name }}
						</label>
					</div>
				</td>
			</tr>
			<?php endif ?>

		</table>

	</div>

</div>
