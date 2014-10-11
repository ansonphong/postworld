/*_    _       _           _       _____             _     
 (_)  / \   __| |_ __ ___ (_)_ __ |  ___|__  ___  __| |___ 
 | | / _ \ / _` | '_ ` _ \| | '_ \| |_ / _ \/ _ \/ _` / __|
 | |/ ___ \ (_| | | | | | | | | | |  _|  __/  __/ (_| \__ \
 |_/_/   \_\__,_|_| |_| |_|_|_| |_|_|  \___|\___|\__,_|___/
                                                           
////////////////////////////////////////////////////////////*/

infinite.directive( 'iAdminFeeds', [ function(){
    return { 
        controller: 'iAdminFeedsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('i-admin-style');
        }
    };
}]);

infinite.controller('iAdminFeedsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions ) {
	
	$scope.view = 'settings';

	///// FEED OPTIONS /////
	$scope.feedOptions = {
		view: $pwPostOptions.postView(),
		query:{
			post_type: $pwPostOptions.postType(),
			post_status: $pwPostOptions.postStatus(),
			orderby: $pwPostOptions.orderBy(),
			order: $pwPostOptions.order(),
			event_filter: $pwPostOptions.eventFilter(),
			post_parent_from:[
				{
					value: 'top_level',
					name: 'Top Level',
					description: 'Show top level posts, with post_parent : 0.'
				},
				{
					value: 'this_post_id',
					name: 'This Post (Show Children)',
					description: 'Show children of the current post, derived from : $post->ID global.'
				},
				{
					value: 'this_post_parent',
					name: 'This Post Parent (Show Siblings)',
					description: 'Show siblings of the current post, derived from : $post->post_parent global.'
				},
				{
					value: 'post_id',
					name: 'Specific Post',
					description: 'Select a specific post.'
				},
			],
			post__not_in_from:[
				{
					value: 'this_post_id',
					name: 'This Post',
					description: 'Do not include the current post'
				},
			],
		},
	};

	////////// FUNCTIONS //////////
	$scope.newFeed = function(){
		var feedId = "feed_" + $_.makeHash( 8 );
		var newFeed = {
			id: feedId,
			name: "New Feed",
			preload: 10,
			load_increment: 10,
			offset: 0,
			order_by: '-post_date',
			view:{
				current: $scope.feedOptions.view[0],
				options: [],
			},
			query: {
				post_type: [ 'post' ],
				post_status: 'publish',
				orderby: 'date',
				order: 'DESC',
				event_filter:null,
				// link_format:
				// s:
				// tax_query: // TODO : select tax, manually enter slug
				
				// post__not_in : << With options like post_parent to exclude current post

				offset:0,
				posts_per_page: 200, 
			},
			//blocks:{},
			feed_template: 'feed-list',	// Get HTML feeds from pwData
			aux_template: 'seo-list',		// Get PHP feeds from pwData
		};

		$scope.iFeeds.push( newFeed );
		$scope.selectItem( newFeed );
	}

	$scope.postClassOptions = function(){
		// Use a custom function since the response depends on selected post type
		var post_type = $_.getObj( $scope.selectedItem, 'query.post_type' );
		return $pwPostOptions.postClass( post_type );	
	} 

	///// FEED SETTINGS OPTIONS /////
	$scope.feedSettingsOptions = {
		'loadingIcon': [
			'icon-spinner-1',
			'icon-spinner-2',
			'icon-spinner-3',
			'icon-spinner-4',
			'icon-spinner-5',
			'icon-spinner-6',
			'icon-seal-1',
			'icon-triadic-1',
			'icon-triadic-2',
			'icon-triadic-3',
			'icon-triadic-4',
			'icon-triadic-5',
			'icon-seed-of-life',
			'icon-seed-of-life-fill',
			'icon-merkaba',
			'icon-target',
			'icon-sun',
			'icon-contrast',
			'icon-loop',
			'icon-hexagon-thick',
			'icon-hexagon-medium',
			'icon-hexagon-thin',
			'icon-arrow-down-circle',
		],
	};

	////////// POST PARENT //////////
	$scope.postParentSelector = function(){
		var options = $_.getObj( $scope.feedOptions, 'query.post_parent_from' );
		var selected = $_.getObj( $scope.selectedItem, 'query.post_parent_from' );
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
	}, 1);

	
}]);
