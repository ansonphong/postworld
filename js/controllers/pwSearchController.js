/**
 * Created by Michel on 9/22/13.
 */
'use strict';

pwApp.controller('pwSearchController',
    function pwSearchController($scope, $location, $log, pwData) {
    	//$scope.args.year = '2007';
    	//$scope.args.monthnum= '1';
		$scope.args = {};
    	$scope.args.posts_per_page = '50';
    	$scope.items = [];
    	// Create an instance of Post World Data Service
    	//$scope.pw = new pwData();    	
		$scope.getNext = function() {
			// If already getting results, do not run again.
			if ($scope.busy) return;			
			$scope.busy = true;
			// Run Search
			this.searchPW();
		};
		$scope.searchPW = function() {
			$log.info('Controller: pwSearchController Method:SearchPW invoked');
			if (!$scope.args) {	$scope.args = {};};
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// console.log('Params=',$scope.args);
	        	pwData.pw_live_feed($scope.args).then(
					// Success
					function(response) {	
						$log.info('Controller: pwSearchController Method:pw_live_feed ServiceReturned');
						$scope.busy = false;
						if (response.status === undefined) {
							console.log('response format is not recognized');
							return;
						}
						if (response.status==200) {
							$log.info('Controller: pwSearchController Method:pw_live_feed Success with data:',response.data);
							// Add Results to items
							$scope.posts = response.data.posts;
							var items = $scope.posts;
							for (var i = 0; i < items.length; i++) {
								$scope.items.push(items[i]);
							  }
							// this.after = "t3_" + this.items[this.items.length - 1].id;
							$scope.busy = false;			
							
							return response.data.posts;
						} else {
							// handle error
							console.log('error',response.status,response.message);
						}
						// return response.posts;
					},
					// Failure
					function(response) {
						$log.error('Controller: pwSearchController Method:pw_live_feed Failure with response:',response);
						// console.log('failure',response);
						// TODO how to handle error?
					}
				);
				$scope.args = null;
		  };
    }
);

