<span class="icon-md"><i class="icon-image"></i></span>
Provide download button for full size image
<div class="btn-group">
	<label
		ng-repeat="obj in <?php echo $vars['options_model']; ?>"
		class="btn" ng-model="<?php echo $vars['ng_model']; ?>" btn-radio="obj.value">
		{{ obj.name }}
	</label>
</div>