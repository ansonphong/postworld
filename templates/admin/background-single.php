<?php extract( $vars ); ?>

<div class="pw-row">
	<div class="pw-col-6">

		<!-- /// PREVIEW /// -->
		<div class="preview-module" style="position:relative;">
			<div
				pw-background="<?php echo $ng_model; ?>.primary"
				style="width:100%;height:100%;position:absolute; z-index:1;"
				class="pw-background-primary"></div>
			<div
				pw-background="<?php echo $ng_model; ?>.secondary"
				style="width:100%;height:100%;position:absolute; z-index:2;"
				class="pw-background-secondary"></div>
		</div>

	</div>
	<div class="pw-col-6">
		<!-- /// SETTINGS /// -->
		<div class="content-wrapper">
	
			<div class="well">

				<h3>Primary</h3>

				<!-- SELECT IMAGE -->
				<?php
					echo pw_select_image_id( array(
						'ng_model'		=>	 $ng_model.'.primary.image.id',
						'slug'			=>	'primary_image',
						'label'			=>	'Primary Image',
						'display'		=>	false,
					 	));?>

				<hr class="thin">

				<!-- SIZE -->
				<span class="icon-md"><i class="icon-target"></i></span>
				<select
					id="primary-size"
					ng-options="value for value in optionsMeta.style.backgroundSize"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-size']">
					<option value="">Default</option>
				</select>
				<label for="primary-size">size</label>

				<hr class="thin">

				<!-- POSITION -->
				<span class="icon-md"><i class="icon-target"></i></span>
				<select
					id="primary-position"
					ng-options="value for value in optionsMeta.style.position"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-position']">
					<option value="">Default</option>
				</select>
				<label for="primary-position">position</label>

				<hr class="thin">

				<!-- ATTACHMENT -->
				<span class="icon-md"><i class="icon-target"></i></span>
				<select
					id="primary-attachment"
					ng-options="value for value in optionsMeta.style.backgroundAttachment"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-attachment']">
					<option value="">Default</option>
				</select>
				<label for="primary-attachment">attachment</label>

				<hr class="thin">

				<!-- COLOR -->
				<span class="icon-md"><i class="icon-brush"></i></span>
				color : <input type="text" ng-model="<?php echo $ng_model; ?>.primary.style['background-color']">
				

			</div>

			<div class="well">
				<h3>Secondary</h3>

				<!-- SELECT IMAGE -->
				<?php
					echo pw_select_image_id( array(
						'ng_model'		=>	$ng_model.'.secondary.image.id',
						'slug'			=>	'secondary_background',
						'label'			=>	'Secondary Image',
						'display'		=>	false,
					 	));?>

				<hr class="thin">

				<!-- OPACITY -->
				<span class="icon-md"><i class="icon-target"></i></span>
				opacity : <input type="number" ng-model="<?php echo $ng_model; ?>.secondary.style.opacity">%
				<hr class="thin">
				<div
					ui-slider="{orientation: 'horizontal', range: 'min'}" 
					min="0"
					max="100"
					step="1"
					ng-model="<?php echo $ng_model; ?>.secondary.style.opacity">
				</div>
				
				<hr class="thin">

				<!-- SIZE -->
				<span class="icon-md"><i class="icon-target"></i></span>
				size : <input type="number" ng-model="<?php echo $ng_model; ?>.secondary.style['background-size']">%
				<hr class="thin">
				<div
					ui-slider="{orientation: 'horizontal', range: 'min'}" 
					min="0"
					max="100"
					step="1"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-size']">
				</div>

				<hr class="thin">

				<!-- POSITION -->
				<span class="icon-md"><i class="icon-target"></i></span>
				<select
					id="secondary-position"
					ng-options="value for value in optionsMeta.style.position"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-position']">
					<option value="">Default</option>
				</select>
				<label for="secondary-position">position</label>

				<hr class="thin">

				<!-- ATTACHMENT -->
				<span class="icon-md"><i class="icon-target"></i></span>
				<select
					id="secondary-attachment"
					ng-options="value for value in optionsMeta.style.backgroundAttachment"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-attachment']">
					<option value="">Default</option>
				</select>
				<label for="secondary-attachment">attachment</label>

			</div>
			
		</div>

	</div>
</div>
