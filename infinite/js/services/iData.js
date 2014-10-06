/*
 * AJAX DATA SERVICES
 */

infinite.factory('iData', function ($resource, $q, $log, $window) {	  
	// Used for Wordpress Security http://codex.wordpress.org/Glossary#Nonce
	var nonce = 0;
	// Check feed_settigns to confirm we have valid settings
	var validSettings = true;
	
	// for Ajax Calls
	var resource = $resource( $window.iGlobals.paths.ajax_url, {action:'wp_action'}, 
								{	wp_ajax: { method: 'POST', isArray: false, },	}
							);
	return {
		
		// Set Nonce Value for Wordpress Security
		setNonce: function(val) {
			nonce = val;
		},
		// Get Nonce Value
		getNonce: function() {
			return nonce;
		},
		// A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
		wp_ajax: function(fname, args) {
			// $log.debug('pwData.wp_ajax', fname, 'args: ',args);
			var deferred = $q.defer();
			// works only for non array returns
			resource.wp_ajax({action:fname},{args:args,nonce:this.getNonce()},
				function (data) {
					deferred.resolve(data);
				},
				function (response) {
					deferred.reject(response);
				});
			return deferred.promise;		
		},
		
		i_save_option: function( args ) {
			$log.debug('wp_ajax.i_save_option',args);
			var params = { args: args };
			return this.wp_ajax('i_save_option',params);
		},


   }; // END OF pwData return value
});
