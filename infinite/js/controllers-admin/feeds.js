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
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_ ) {
	
	$scope.editFeed = {};

	$scope.feedOptions = {
		view: $window.pwSiteGlobals.post_views,
		
		query:{
			post_type: [],	// Get from pwData ( add 'any' option )
			post_status:[],	// Get from pwSiteGlobals
			post_class:[],	// Get from pwSiteGlobals
			orderby:[ 'date', 'rank_score', 'post_points', 'modified', 'rand', 'comment_count' ],
			order: [ 'DESC', 'ASC' ],
		},

	};


	$scope.newFeed = function(){
		var feedId = "myFeed_" + $_.makeHash( 16 );
		var newFeed = {
			id: feedId,
			name: "New Feed",
			preload: 10,
			load_increment: 10,
			offset: 0,
			order_by: '-post_date',
			view:{
				current: $scope.feedOptions.view[0],
				options: $scope.feedOptions.view,
			},
			query: {
				post_type: ['post'],
				post_status: 'publish',
				orderby: 'date',
				order: 'DESC',
				// event_future: 
				// event_past: 
				// event_now: 
				// link_format: 
				// post_class: null,
				// s: 
				// tax_query:
				offset:0,
				posts_per_page: 200, 
			},
			//blocks:{},
			feed_template: null,	// Get HTML feeds from pwData
			aux_feed: null,			// Get PHP feeds from pwData
		};
		$scope.iFeeds.push( newFeed );
	}

	$scope.duplicateFeed = function(){

	}

	$scope.deleteFeed = function( feed ){
		$scope.iFeeds = _.reject( $scope.iFeeds, function( thisFeed ){ return thisFeed.id == feed.id } );
		$scope.editingFeed = {};
	}

	$scope.editFeed = function( feed ){
		$scope.editingFeed = feed;
	}
	
	
}]);
