<?
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
	extract($options);
?>
<div
	id="<?php echo $instance ?>"
	class="postworld">
	<div
		class="postworld-widget postworld-widget-user"
		ng-controller="<?php echo $instance ?>Ctrl">

		<!-- TITLE -->
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<input
				type="checkbox"
				value="1"
				title="Show Title"
				name="<?php echo $this->get_field_name('show_title'); ?>"
				id="<?php echo $this->get_field_id('show_title'); ?>"
				<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
			<?php _e( 'Title:' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo $this->get_field_id( 'title' ); ?>"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" />


		<div class="type-wrapper">
		
			<div class="btn-group">
				<label
					class="btn btn-primary"
					ng-repeat="option in userSelectOptions"
					ng-model="settings.user_select"
					btn-radio="option.slug">
					{{ option.name }}
				</label>
		    </div>
		    
			<hr class="thin">

			<div ng-show="settings.user_select == 'user_id'">

				<div ng-hide="userIsSelected()">
		         	<div class="labeled">
						<label class="inner">Select User</label>
						<span class='container-fluid' ng-controller="userAutocomplete">
							<input
								class="labeled gray"
								type="text"
								typeahead-min-length="2"
								typeahead-loading="status"
								typeahead-wait-ms="100"
								ng-change="queryList()"
								typeahead-editable="0"
								typeahead-on-select="widgetUserSelected($item)"
								ng-model="username"
								typeahead="author.user_nicename as author.display_name for author in authors | filter:$viewValue | limitTo:20"
								autocomplete="off">

							<!--
							username : {{ username | json }}
							<hr class="thin">
							authors : {{ authors | json }}
							<hr>
							-->
						</span>
					</div>
				</div>

				<div ng-show="userIsSelected()">
					SELECTED USER :
					<h3>
						{{ user.display_name }}
						<button type="button" ng-click="widgetClearUser()">
							<i class="icon-close"></i>
						</button>
					</h3>
					
				</div>


			</div>


			<input
				type="hidden"
				name="<?php echo $this->get_field_name( 'user_select' ); ?>"
				value="{{ settings.user_select }}">
			<input
				type="hidden"
				name="<?php echo $this->get_field_name( 'user_id' ); ?>"
				value="{{ settings.user_id }}">

			<!--
			<hr class="thin">
			{{ settings }}
			-->

	   </div>

	</div>

</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var <?php echo $instance ?> = angular.module( '<?php echo $instance ?>', ['postworldAdmin'] );
	///// CONTROLLER /////
	<?php echo $instance ?>.controller('<?php echo $instance ?>Ctrl',
		['$scope', 'pwData', '_', '$log',
		function( $scope, $pwData, $_, $log ) {

			$scope.settings = <?php echo json_encode( $options ); ?>;

			$scope.user = <?php echo json_encode( $user ) ?>;

			///// USER SELECT OPTIONS /////
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
				//return Boolean( $_.get( $scope.settings, 'user_id' ) );
				return !_.isEmpty( $scope.user );
			}

			$scope.widgetUserSelected = function( user ){
				//; setUserValue( $item, 'user' )
				$scope.settings.user_id = user.ID;
				$scope.user = user;
			}

			$scope.widgetClearUser = function(){
				$scope.settings.user_id = 0;
				$scope.user = {};
			}

	}]);
</script>
<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("<?php echo $instance ?>"),['<?php echo $instance ?>']);
</script>
