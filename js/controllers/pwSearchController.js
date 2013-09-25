/**
 * Created by Michel on 9/22/13.
 */
'use strict';

pwApp.controller('pwSearchController',
    function pwSearchController($scope, $location, $log, pwData) {
    	//$scope.args.year = '2007';
    	//$scope.args.monthnum= '1';
    	//$scope.args.posts_per_page = '10';
		$scope.searchPW = function() {
			$log.info('Controller: pwSearchController Method:SearchPW invoked');
			if (!$scope.args) {	$scope.args = {};};
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// console.log('Params=',$scope.args);
	        $scope.posts = 
	        	pwData.pw_live_feed($scope.args).then(
					// Success
					function(response) {	
						$log.info('Controller: pwSearchController Method:pw_live_feed ServiceReturned');
						if (response.status === undefined) {
							console.log('response format is not recognized');
							return;
						}
						if (response.status==200) {
							$log.info('Controller: pwSearchController Method:pw_live_feed Success with data:',response.data);
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

