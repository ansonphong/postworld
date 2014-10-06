<!-- SAVE BUTTON -->
<button
	i-save-option
	ng-click="saveOption('<?php echo $vars['option_name']; ?>','<?php echo $vars['option_model']; ?>')"
	class="button button-primary">
	<span ng-show="status != 'saving'"><i class="icon-disk" style="opacity:.5"></i> &nbsp; Save</span>
	<span ng-show="status == 'saving'"><i class="icon-spinner-2 icon-spin"></i> &nbsp; Save</span>
</button>