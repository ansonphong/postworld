/*_____             _     
 |  ___|__  ___  __| |___ 
 | |_ / _ \/ _ \/ _` / __|
 |  _|  __/  __/ (_| \__ \
 |_|  \___|\___|\__,_|___/
                          
/////////////////////////*/

postworldAdmin.directive( 'pwFeedOptions',
	[ 'pwData', '$_', 'pwPostOptions',
	function( $pwData, $_, $pwPostOptions ){
    return { 
        link:function( $scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-feed-options');

        	///// FEED OPTIONS /////
			$scope.feedOptions = {
				view: $pwPostOptions.postView()['options'].feeds,
				views:{
					grid:{
						columns:[1,2,3,4,5,6],
					}
				},
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
					exclude_posts_from:[
						{
							value: 'this_post_id',
							name: 'This Post',
							description: 'Exclude the current post',
						},
					],
					include_posts_from:[
						{
							value: 'this_post_id',
							name: 'This Post',
							description: 'Include the current post',
						},
						{
							value: 'this_post_parent',
							name: 'This Post Parent',
							description: 'Include the current posts parent',
						},
					],
					author_from:[
						{
							value:'this_author',
							name: 'This Author',
							description: 'Include posts by the current post\'s author.',
						},
						{
							value:'author_id',
							name: 'Specific Author',
							description: 'Select a specific author.',
						},
					],
				},
			};


        }
    };
}]);

postworldAdmin.directive( 'pwAdminFeeds', [ function(){
    return { 
        controller: 'pwAdminFeedsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-feeds');
        }
    };
}]);

postworldAdmin.controller('pwAdminFeedsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'pwData', '$_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $pwData, $_, $pwPostOptions ) {
	
	$scope.view = 'settings';

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

		$scope.pwFeeds.push( newFeed );
		$scope.selectItem( newFeed );
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

	/// WATCH : QUERY › AUTHOR FROM ///
	$scope.$watch('selectedItem.query.author_from', function(value){
		var objExists = $_.objExists( $scope.selectedItem, 'query.author' );
		var isAuthorId = ( value == 'author_id');
		// If not 'post id' and post_parent object exists
		if( !isAuthorId && objExists )
			delete $scope.selectedItem.query.author;
		// If 'author id' and post_parent obect doesn't exist
		else if( isAuthorId && !objExists )
			$scope.selectedItem.query.author = 0;
	
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
