<?php
 /*   _               
 | | | |___  ___ _ __ 
 | | | / __|/ _ \ '__|
 | |_| \__ \  __/ |   
  \___/|___/\___|_|   
					  
///// USER - ADMIN /////*/
?>
<?php
	$instance = 'pwUserWidget_'.pw_random_string(8);
	if ( isset( $options[ 'title' ] ) ) {
			$title = $options[ 'title' ];
		}
		else {
			$title = __( 'Widget', 'text_domain' );
		}
	extract( $options );
?>

<!--///// METABOX SCRIPTS /////-->
<script>

	console.log( 'Init User Widget Script : ' + '<?php echo $instance ?>' );

	///// CONTROLLER /////
	postworldAdmin.controller('<?php echo $instance ?>',
		function( $scope, $pwData, $_, $log ) {

			$log.debug( 'Init User Widget Controller : ' + '<?php echo $instance ?>' );

			$scope.settings = <?php echo json_encode( $options ); ?>;
			$scope.user = <?php echo json_encode( $user ) ?>;
			$scope.viewOptions = <?php echo json_encode( $viewOptions ) ?>;

			$scope.userSelectOptions = [
				{
					name: 'Current Author',
					slug: 'current_author', 
				},
				{
					name: 'Specific User',
					slug: 'user_id', 
				},
			];

			$scope.userIsSelected = function(){
				return !_.isEmpty( $scope.user );
			}

			$scope.widgetUserSelected = function( user ){
				$scope.settings.user_id = user.ID;
				$scope.user = user;
			}

			$scope.widgetClearUser = function(){
				$scope.settings.user_id = 0;
				$scope.user = {};
			}

	});

	pwRegisterController( '<?php echo $instance ?>', 'postworldAdmin' );
	pwCompileElement( 'body', '<?php echo $instance ?>' );

</script>

<div
	id="<?php echo $instance ?>"
	class="postworld">
	<div
		class="postworld-widget postworld-widget-user"
		ng-controller="<?php echo $instance ?>">

		<!-- TITLE -->
		<label class="inner" for="<?php echo $this->get_field_id( 'show_title' ); ?>">
			<input
				type="checkbox"
				value="1"
				title="<?php _e( 'Show Title', 'postworld' ); ?>"
				name="<?php echo $this->get_field_name('show_title'); ?>"
				id="<?php echo $this->get_field_id('show_title'); ?>"
				<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
			<?php _e( 'title', 'postworld' ); ?>
		</label>
		<input
			class="labeled"
			id="<?php echo $this->get_field_id( 'title' ); ?>"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" />

		<!-- SELECT USER -->
		<div class="type-wrapper">

			<div class="btn-group">
				<label
					class="btn btn-primary"
					ng-repeat="option in userSelectOptions"
					ng-model="settings.user_select"
					uib-btn-radio="option.slug">
					{{ option.name }}
				</label>
			</div>
			
			<!-- SELECT : CURRENT USER -->
			<div ng-if="settings.user_select == 'current_author'">
				<hr class="thin">
				<small>
					The author of the currently viewed page will be shown.
				</small>
			</div>

			<!-- SELECT : SPECIFIC USER -->
			<div ng-if="settings.user_select == 'user_id'">
				
				<hr class="thin">

				<!-- SELECT : SELECTED A USER -->
				<div ng-hide="userIsSelected()">

					<div class="labeled">
						<label class="inner">Type Username</label>
						<span class='container-fluid'>
							<?php echo pw_select_user_autocomplete(array(
								'class'		=>	'labeled',
								'on_select' => 	'widgetUserSelected($item)',
								)); ?>
						</span>
					</div>

				</div>

				<!-- SELECT : SELECTED USER -->
				<div ng-show="userIsSelected()">
					<div class="labeled" style="position:relative;">
						<button
							type="button"
							class="button"
							ng-click="widgetClearUser()"
							style="position:absolute; right:0;">
							<i class="pwi-close-thin"></i>
						</button>
						<label class="inner">Selected User</label>
						<input
							class="labeled un-disabled bold"
							type="text"
							disabled
							value="{{ user.display_name }}">
						<small class="micro">
							ID : {{ user.ID }} // {{ user.user_nicename }}
						</small>
					</div>
				</div>

			</div>

		</div>

		<hr class="thin">

		<!-- SELECT -->
		<label
			for="<?php echo $this->get_field_id('taxonomy'); ?>"
			class="inner">
			View
		</label>
		<select
			class="labeled"
			ng-options="key as key for (key , value) in viewOptions"
			ng-model="settings.view">
		</select>

		<!-- HIDDEN INPUTS -->
		<input
			type="hidden"
			name="<?php echo $this->get_field_name( 'user_select' ); ?>"
			value="{{ settings.user_select }}">
		<input
			type="hidden"
			name="<?php echo $this->get_field_name( 'user_id' ); ?>"
			value="{{ settings.user_id }}">
		<input
			type="hidden"
			name="<?php echo $this->get_field_name( 'view' ); ?>"
			value="{{ settings.view }}">


	</div>
</div>


