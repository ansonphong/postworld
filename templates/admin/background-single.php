<?php extract( $vars ); ?>




<div class="pw-row">
	<div class="pw-col-6">
		<!-- /// PREVIEW /// -->
		<div class="preview-module">
			
		</div>
	</div>
	<div class="pw-col-6">
		<!-- /// SETTINGS /// -->
		<div class="content-wrapper">
	
			<h3>Primary</h3>

			<input type="number" ng-model="<?php  echo $ng_model; ?>.primary.id">
			{{ selectedItem.primary.id }}


			<?php
				echo pw_select_image_id( array(
					'ng_model'		=>	'selectedItem.primary.id',
					'slug'			=>	'primary_background',
					'label'			=>	'Primary Background',
					'display'		=>	false,
				 	));?>

			<hr class="thin">

			<!-- SLIDER -->
			<div
				ui-slider="{orientation: 'horizontal', range: 'min'}" 
				min="0"
				max="100"
				step="1"
				ng-model="selectedItem.tester">
			</div>
			{{selectedItem.tester}}

			<hr>

			<h3>Secondary</h3>

			<input type="number" ng-model="<?php  echo $ng_model; ?>.secondary.id">
			{{ selectedItem.secondary.id }}

			<?php
				echo pw_select_image_id( array(
					'ng_model'		=>	'selectedItem.secondary.id',
					'slug'			=>	'secondary_background',
					'label'			=>	'Secondary Image',
					'display'		=>	false,
				 	));?>

		</div>

	</div>
</div>
