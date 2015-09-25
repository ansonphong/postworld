<input
	pw-user-autocomplete
	class="<?php echo $vars['class']?>"
	type="text"
	typeahead-min-length="2"
	typeahead-loading="status"
	typeahead-wait-ms="200"
	typeahead-editable="0"
	typeahead-on-select="<?php echo $vars['on_select']?>"	<?php // Can use these variables : $item, $model, $label; ?>
	ng-model="<?php echo $vars['ng_model']?>"
	typeahead="author.user_nicename as author.display_name for author in queryList(<?php echo $vars['ng_model']?>) | limitTo:<?php echo $vars['limit_to']?>"	<?php // | filter:$viewValue ?>
	autocomplete="off">