<select
	ng-options="background.id as background.name for background in pwBackgrounds"
	ng-model="<?php echo $vars['ng_model'] ?>">
	<option value="">Default</option>
</select>