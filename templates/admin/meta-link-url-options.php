<div class="link-url-options">
	<div class="btn-group">
		<label
			class="btn"
			ng-repeat="item in <?php echo $vars['options_model']['show']; ?>"
			ng-model="<?php echo $vars['ng_model']; ?>.label.show"
			btn-radio="item.value">
			{{ item.name }}
		</label>
	</div>

	<div ng-show="<?php echo $vars['ng_model']; ?>.label.show == 'custom'">
		<hr class="thin">
		<table>
			<tr>
				<td class="label">Label :</td>
				<td class="control">
					<input
						type="text"
						ng-model="<?php echo $vars['ng_model']; ?>.label.custom"
						placeholder="Custom Link Label">
				</td>
			</tr>
			<tr>
				<td class="label">Tooltip :</td>
				<td class="control">
					<div
						class="btn-group"
						ng-show="<?php echo $vars['options_model']['tooltip_show']; ?>">
						<label
							class="btn"
							ng-repeat="item in <?php echo $vars['options_model']['tooltip_show']; ?>"
							ng-model="<?php echo $vars['ng_model']; ?>.label.tooltip.show"
							btn-radio="item.value">
							{{ item.name }}
						</label>
					</div>
					<input
						ng-hide="<?php echo $vars['ng_model']; ?>.label.tooltip.show == 'default'"
						type="text"
						ng-model="<?php echo $vars['ng_model']; ?>.label.tooltip.custom"
						placeholder="Custom Tooltip">

				</td>
			</tr>

			<tr>
				<td class="label">Highlight :</td>
				<td class="control">
					<div class="btn-group">
						<label
							class="btn"
							ng-repeat="item in <?php echo $vars['options_model']['highlight']; ?>"
							ng-model="<?php echo $vars['ng_model']; ?>.label.highlight"
							btn-radio="item.value">
							{{ item.name }}
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td class="label">Open in new tab : </td>
				<td class="control">
					<div class="btn-group">
						<label
							class="btn"
							ng-repeat="item in <?php echo $vars['options_model']['new_target']; ?>"
							ng-model="<?php echo $vars['ng_model']; ?>.new_target"
							btn-radio="item.value">
							{{ item.name }}
						</label>
					</div>
				</td>
			</tr>
		</table>

	</div>

</div>
