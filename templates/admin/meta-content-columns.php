<div class="btn-group">
	<label
		ng-repeat="option in <?php echo $vars['options_model']; ?>"
		class="btn" ng-model="<?php echo $vars['ng_model']; ?>" uib-btn-radio="option.value">
		{{ option.name }}
	</label>
</div>