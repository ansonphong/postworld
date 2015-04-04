<?php
	if( empty( $vars['gallery_options'] ) )
		$vars['gallery_options'] = array('inline','frame','horizontal','vertical'); 
?>

<script>
	postworld.controller('galleryOptionsData',[ '$scope', '_', function($scope,$_){

		var galleryOptionsMeta = <?php echo json_encode( ___('gallery.template', true) ) ?>;
		var galleryOptionsKeys = <?php echo json_encode( $vars['gallery_options'] ) ?>;

		$scope.galleryOptions = [];
		angular.forEach( galleryOptionsMeta, function( value,key ){
			if( $_.inArray( key, galleryOptionsKeys ) ){
				value['key'] = key;
				$scope.galleryOptions.push(value);
			}
		});

		$scope.getSelectedOption = function( objectKey ){
			 // Return the option where the slug equals the selected value
			 return _.findWhere( $scope.galleryOptions, { key: objectKey } );
		};

	}]);
</script>

<div ng-controller="galleryOptionsData">

	<div class="btn-group">
		<label
			ng-repeat="template in galleryOptions"
			class="btn" ng-model="<?php echo $vars['ng_model']; ?>.template" btn-radio="template.key">
			{{ template.name }}
		</label>
	</div>
	<div class="well">
		<table>
			<tr>
				<td valign="top">
					<img
						style="float:left; margin-right:15px;"
						ng-src="<?php echo get_infinite_directory_uri(); ?>/images/layouts/galleries/gallery-{{ <?php echo $vars['ng_model']; ?>.template }}.png">
				</td>
				<td>

					{{ getSelectedOption(<?php echo $vars['ng_model']; ?>.template).description }}
					
					<!-- X SCROLL OPTIONS -->
					<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'horizontal'">
						<hr class="thin">
						<span class="icon-md"><i class="pwi-arrows-h"></i></span>
						<input type="text" size="4" ng-model="<?php echo $vars['ng_model']; ?>.x_scroll_distance" id="horizontal-scroll-distance">
						<label for="horizontal-scroll-distance"><b>horizontal scroll distance</b></label>
						<small> - Number of pixels on the right before load more images <i>(default: 1500)</i></small>
						<hr class="thin">
						<span class="icon-md"><i class="pwi-arrows-v"></i></span>
						<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.height" id="gallery-height">
						<label for="gallery-height"><b>% height</b></label>
						<small> - Percentage height of the window to size the horizontal scroll gallery</small>
						<!--
						- Include the Featured Image as the first image in the gallery (default : false)
						-->
					</div>

					<!-- Y SCROLL OPTIONS -->
					<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'vertical'">
						<hr class="thin">
						<span class="icon-md"><i class="pwi-arrows-v"></i></span>
						<input type="text" size="4" ng-model="<?php echo $vars['ng_model']; ?>.y_scroll_distance" id="vertical-scroll-distance">
						<label for="vertical-scroll-distance"><b>vertical scroll distance</b></label>
						<small> - Number of pixels on the bottom before load more images <i>(default: 1000)</i></small>
						<hr class="thin">
						<span class="icon-md"><i class="pwi-arrows-h"></i></span>
						<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.width" id="gallery-width">
						<label for="gallery-width"><b>% width</b></label>
						<small> - Percentage width of the window to size the vertical scroll gallery</small>
					</div>

				</td>
			</tr>	
		</table>

		<div style="clear:both;"></div>
	</div>
</div>