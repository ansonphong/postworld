/*_____                     _____             _     
 |_   _|__ _ __ _ __ ___   |  ___|__  ___  __| |___ 
   | |/ _ \ '__| '_ ` _ \  | |_ / _ \/ _ \/ _` / __|
   | |  __/ |  | | | | | | |  _|  __/  __/ (_| \__ \
   |_|\___|_|  |_| |_| |_| |_|  \___|\___|\__,_|___/
                                                    
///////////////////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminTermFeeds', [ function(){
    return { 
        controller: 'pwAdminTermFeedsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-term-feeds');
        }
    };
}]);

postworldAdmin.controller('pwAdminTermFeedsCtrl',
	[ '$scope', '$log', '$window', '$parse', '$pwData', '$_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $pwData, $_, $pwPostOptions ) {
	
	$scope.view = 'settings';

	// MAKE PRESETS FOR TEMPLATES

	////////// FUNCTIONS //////////
	$scope.newTermFeed = function(){
		var feedId = "term_feed_" + $_.randomString( 8 );
		var newTermFeed = {

			template: 'term-feed-default',

			id: feedId,
			name: "New Feed",
			preload: 10,
			load_increment: 10,
			offset: 0,
			order_by: '-post_date',

			query: {
				post_type: [ 'post' ],
				post_status: 'publish',
				orderby: 'date',
				order: 'DESC',
				event_filter:null,
				offset:0,
				posts_per_page: 200, 
			},

			//blocks:{},
			feed_template: 'feed-list',	// Get HTML feeds from $pwData
			aux_template: 'seo-list',		// Get PHP feeds from $pwData
		};

		$scope.pwTermFeeds.push( newTermFeed );
		$scope.selectItem( newTermFeed );
	}

	$scope.postClassOptions = function(){
		// Use a custom function since the response depends on selected post type
		var post_type = $_.getObj( $scope.selectedItem, 'query.post_type' );
		return $pwPostOptions.postClass( post_type );	
	} 


	////////// POST PARENT //////////
	$scope.selectOptionObj = function( optionValue ){
		var options = $_.getObj( $scope.feedOptions, optionValue );
		var selected = $_.getObj( $scope.selectedItem, optionValue );
		if( options && selected )
			return _.findWhere( options, { value: selected } );
		else
			return false;
	}

	/// WATCH : QUERY › POST PARENT FROM ///
	$scope.$watch('selectedItem.query.post_parent_from', function(value){
		var objExists = $_.objExists( $scope.selectedItem, 'query.post_parent' );
		var isPostId = ( value == 'post_id');
		// If not 'post id' and post_parent object exists
		if( !isPostId && objExists )
			delete $scope.selectedItem.query.post_parent;
		// If 'post id' and post_parent obect doesn't exist
		else if( isPostId && !objExists )
			$scope.selectedItem.query.post_parent = 0;
	});

	////////// REMOVE NULL VALUES //////////
	// Watch the query value for changes
	$scope.$watch( 'selectedItem.query', function( value ){
		if( $_.getObj( $scope, 'selectedItem.query.event_filter' ) == null )
			delete $scope.selectedItem.query.event_filter;
		if( $_.getObj( $scope, 'selectedItem.query.post_class' ) == null )
			delete $scope.selectedItem.query.post_class;
		if( $_.getObj( $scope, 'selectedItem.query.post_parent' ) == null )
			delete $scope.selectedItem.query.post_parent;
		if( $_.getObj( $scope, 'selectedItem.query.exclude_posts_from' ) == null )
			delete $scope.selectedItem.query.exclude_posts_from;
		if( $_.getObj( $scope, 'selectedItem.query.include_posts_from' ) == null )
			delete $scope.selectedItem.query.include_posts_from;
	}, 1);

	
}]);
