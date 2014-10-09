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
	
	$scope.editingFeed = {};
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
		},
	};

	////////// FUNCTIONS //////////
	$scope.newFeed = function(){
		var feedId = "feed_" + $_.makeHash( 16 );
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
				//event_future: false,
				//event_past: false,
				//event_now: false,
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

		$scope.editFeed( newFeed );

	}

	$scope.duplicateFeed = function(){
		// TODO
	}

	$scope.deleteFeed = function( feed ){
		$scope.iFeeds = _.reject( $scope.iFeeds, function( thisFeed ){ return thisFeed.id == feed.id } );
		$scope.editingFeed = {};
		$scope.view = '';
	}

	$scope.editFeed = function( feed ){
		switch( feed ){
			case 'settings':
				$scope.view = 'settings';
				$scope.editingFeed = {};
				break;
			default:
				$scope.view = 'editFeed';
				$scope.editingFeed = feed;
				break;
		}
	}
	
	$scope.showView = function( viewId ){
		if( viewId == $scope.view )
			return true;
		return false;
	}

	$scope.menuClass = function( menuItem ){
		var selected = false;

		switch( menuItem ){
			case 'settings':
				if( $scope.view == menuItem )
					selected = true;
				break;
			default:
				if( $scope.editingFeed.id == menuItem.id )
					selected = true;
				break;
		}

		if( selected )
			return 'selected';

		return;

	}

	$scope.postClassOptions = function(){
		// Use a custom function since the response depends on selected post type
		var post_type = $_.getObj( $scope.editingFeed, 'query.post_type' );
		return $pwPostOptions.postClass( post_type );	
	} 

	$scope.selectItem = function( object, model, value ){
		$scope[object] = $_.setObj( $scope[object], model, value );// 'test';
		//$scope.eval(model) = value;
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


	////////// EVENTS INPUT //////////

	// Watch the time filter value for changes
	// Then set the feed settings accordingly
	$scope.$watch( 'editingFeed.query.event_filter', function( value ){
		if( _.isEmpty( value ) )
			delete $scope.editingFeed.query.event_filter;
	});

	
}]);
