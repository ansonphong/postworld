<?php extract( $vars ); ?>

<div class="pw-row">
	<div class="pw-col-8">

		<!-- /// PREVIEW /// -->
		<div class="preview-module" style="position:relative;">
			<div
				pw-background="{{<?php echo $ng_model; ?>.primary}}"
				style="width:100%;height:100%;position:absolute; z-index:1;"
				class="pw-background-primary"></div>
			<div
				pw-background="{{<?php echo $ng_model; ?>.secondary}}"
				style="width:100%;height:100%;position:absolute; z-index:2;"
				class="pw-background-secondary"></div>
		</div>

		<?php if( pw_dev_mode() ) : ?>
			<hr>
			<code>PRIMARY : {{<?php echo $ng_model; ?>.primary | json }}</code>
			<hr class="thin">
			<code>SECONDARY : {{<?php echo $ng_model; ?>.secondary | json }}</code>
		<?php endif; ?>

	</div>
	<div class="pw-col-4">
		<!-- /// SETTINGS /// -->
		<div class="content-wrapper">
	
			<div class="well flush-top">

				<h3>Primary</h3>

				<!-- SELECT IMAGE -->
				<?php
					echo pw_select_image_id( array(
						'ng_model'		=>	 $ng_model.'.primary.image.id',
						'slug'			=>	'primary_image',
						'label'			=>	'Image',
						'display'		=>	false,
					 	));?>

				<hr class="thin">

				<!-- SIZE -->
				<span class="pwi-md"><i class="pwi-arrows-alt"></i></span>
				<select
					id="primary-size"
					ng-options="value for value in optionsMeta.style.backgroundSize"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-size']">
					<option value="">Default</option>
				</select>
				<label for="primary-size">size</label>

				<hr class="thin">

				<!-- REPEAT -->
				<span class="pwi-md"><i class="pwi-target"></i></span>
				<select
					id="primary-repeat"
					ng-options="value for value in optionsMeta.style.backgroundRepeat"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-repeat']">
					<option value="">Default</option>
				</select>
				<label for="primary-repeat">repeat</label>

				<hr class="thin">

				<!-- POSITION -->
				<span class="pwi-md"><i class="pwi-arrows"></i></span>
				<select
					id="primary-position"
					ng-options="value for value in optionsMeta.style.backgroundPosition"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-position']">
					<option value="">Default</option>
				</select>
				<label for="primary-position">position</label>

				<hr class="thin">

				<!-- PARALLAX -->
				<div
					class="indent"
					ng-show="'parallax' == <?php echo $ng_model; ?>.primary.style['background-position']">
					<span class="pwi-md"><i class="pwi-arrows-v"></i></span>
					<input
						type="number"
						id="primary-parallax"
						ng-model="<?php echo $ng_model; ?>.primary.image.parallax">
					<label for="primary-parallax">parallax ratio</label>
					<hr class="thin">
				</div>

				<!-- ATTACHMENT -->
				<span class="pwi-md"><i class="pwi-anchor"></i></span>
				<select
					id="primary-attachment"
					ng-options="value for value in optionsMeta.style.backgroundAttachment"
					ng-model="<?php echo $ng_model; ?>.primary.style['background-attachment']">
					<option value="">Default</option>
				</select>
				<label for="primary-attachment">attachment</label>

				<hr class="thin">

				<!-- COLOR -->
				<span class="pwi-md"><i class="pwi-brush"></i></span>
				<input id="primary-color" type="text" ng-model="<?php echo $ng_model; ?>.primary.style['background-color']">
				<label for="primary-color">color</label>

			</div>

			<div class="well">
				<h3>Secondary</h3>

				<!-- SELECT IMAGE -->
				<?php
					echo pw_select_image_id( array(
						'ng_model'		=>	$ng_model.'.secondary.image.id',
						'slug'			=>	'secondary_background',
						'label'			=>	'Image',
						'display'		=>	false,
					 	));?>

				<hr class="thin">

				<!-- OPACITY -->
				<span class="pwi-md"><i class="pwi-layers"></i></span>
				
				<input id="secondary-opacity" type="number" ng-model="<?php echo $ng_model; ?>.secondary.style.opacity">
				<label for="secondary-opacity">% opacity</label>
				<hr class="thin">
				<div
					ui-slider="{orientation: 'horizontal', range: 'min'}" 
					min="1"
					max="100"
					step="1"
					ng-model="<?php echo $ng_model; ?>.secondary.style.opacity">
				</div>
				
				<hr class="thin">

				<!-- SIZE -->
				<span class="pwi-md"><i class="pwi-arrows-alt"></i></span>
				<input id="secondary-size" type="number" ng-model="<?php echo $ng_model; ?>.secondary.style['background-size']">
				<label for="secondary-size">% size</label>
				<hr class="thin">
				<div
					ui-slider="{orientation: 'horizontal', range: 'min'}" 
					min="1"
					max="100"
					step="1"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-size']">
				</div>

				<hr class="thin">

				<!-- REPEAT -->
				<span class="pwi-md"><i class="pwi-target"></i></span>
				<select
					id="secondary-repeat"
					ng-options="value for value in optionsMeta.style.backgroundRepeat"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-repeat']">
					<option value="">Default</option>
				</select>
				<label for="secondary-repeat">repeat</label>

				<hr class="thin">

				<!-- POSITION -->
				<span class="pwi-md"><i class="pwi-arrows"></i></span>
				<select
					id="secondary-position"
					ng-options="value for value in optionsMeta.style.backgroundPosition"
					ng-model="<?php echo $ng_model; ?>.secondary.style['background-position']">
					<option value="">Default</option>
				</select>
				<label for="secondary-position">position</label>

				<hr class="thin">

				<!-- PARALLAX -->
				<div
					class="indent"
					ng-show="'parallax' == <?php echo $ng_model; ?>.secondary.style['background-position']">
					<span class="pwi-md"><i class="pwi-arrows-v"></i></span>
					<input
						type="number"
						id="secondary-parallax"
						ng-model="<?php echo $ng_model; ?>.secondary.image.parallax">
					<label for="secondary-parallax">parallax ratio</label>
					<hr class="thin">
				</div>

				<!-- ATTACHMENT -->
				<span class="pwi-md"><i class="pwi-anchor"></i></span>
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
