<small>
	Provide download button for full size image:
</small>
<hr class="thin">
<div class="btn-group">
	<label
		ng-repeat="obj in <?php echo $vars['options_model']; ?>"
		class="btn" ng-model="<?php echo $vars['ng_model']; ?>" btn-radio="obj.value">
		{{ obj.name }}
	</label>
</div>
