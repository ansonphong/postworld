<div class="btn-group">
	<label
		ng-repeat="placement in <?php echo $vars['options_model'] ?>"
		class="btn" ng-model="<?php echo $vars['ng_model'] ?>" btn-radio="placement.slug">
		{{ placement.name }}
	</label>
</div>