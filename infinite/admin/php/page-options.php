<div id="infinite_admin" ng-app="infinite" class="main">
	<h1>
		<i class="icon-gear"></i>
		<?php echo $theme_admin['main']['page_title']; ?>
	</h1>
	<?php
		// Enable Media Library
		wp_enqueue_media();
		///// GET OPTIONS /////
		$i_options = get_option('i-options', array() );
		$i_header_code = json_encode( get_option( 'i-header-code', '' ) );
	?>
	<script>
		infinite.controller( 'optionsDataCtrl', [ '$scope', function( $scope ){
			$scope.iOptions = <?php echo $i_options; ?>;
			$scope.iHeaderCode = <?php echo $i_header_code; ?>;
			$scope['images'] = {};
		}]);
	</script>
	<div
		i-admin-options
		ng-cloak
		ng-controller="optionsDataCtrl">

		<!--///// THEME OPTIONS /////-->
		<hr class="thick">
		<h3>
			<span class="icon-md"><i class="icon-image"></i></span>
			Logo
		</h3>
		<?php include i_locate_template('admin/modules/select-image-logo.php'); ?>
		
		<hr class="thick">
		<h3>
			<span class="icon-md"><i class="icon-image"></i></span>
			Favicon
		</h3>
		<?php include i_locate_template('admin/modules/select-image-favicon.php'); ?>

		<hr class="thick">
		<h3>Main Menu</h3>
		<span class="icon-md"><i class="icon-nav"></i></span>
		<?php
			echo i_select_menus( array(
				'options_model'	=>	'options.menus',
				'ng_model'	=>	'iOptions.menus.main',
				));
		?>
		<?php i_save_option_button('i-options','iOptions'); ?>
		<hr>

		<hr class="thick">
		<h3>
			<i class="icon-code"></i>
			Header Code
		</h3>
		This code will be inserted into the page header.
		Here is a good place to post tracking codes such as Google Analytics, or third-party additions.
		
		<div>
			<textarea
				msd-elastic
				class="form-control"
				ng-model="iHeaderCode"></textarea>
		</div>
		
		<?php i_save_option_button('i-header-code','iHeaderCode'); ?>

		<hr class="thick">
		<h3>Google Fonts</h3>
		
		<hr class="thick">

		<pre>iOptions: {{ iOptions | json }}</pre>

	</div>
</div>