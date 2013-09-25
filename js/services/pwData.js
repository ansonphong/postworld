/**
 * Created by Michel on 9/22/13.
 * 0127 024 3367
 * 	Development Note:-
 *	To make Ajax request work as a form post, we need to do 3 things:
 *	1- Use AngularJS version 1.2
 * 	2- change content type in the header:-  headers: {'Content-Type': 'application/x-www-form-urlencoded', 'charset':'UTF-8'}
 * 	3- Transform data to url encoded format using the following function http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
 *	http://stackoverflow.com/questions/11442632/how-can-i-make-angular-js-post-data-as-form-data-instead-of-a-request-payload
 *  Otherwise, if you don't want to go through the above hassle, you can just do the following on the server:- 
 * 	$args_text = file_get_contents("php://input");
 *	$args = json_decode($args_text);
 * */

pwApp.factory('pwData', function ($resource, $q, $log) {
	
	// Used for Wordpress Security http://codex.wordpress.org/Glossary#Nonce
	var nonce = 0;
	
	// for Ajax Calls
    var resource = $resource(jsVars.ajaxurl, {action:'wp_action'}, 
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
			$log.info('Service: pwData Method:wp_ajax Arguments: ','fname: ', fname, 'args: ',args);
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
		pw_live_feed: function(args) {
			$log.info('Service: pwData Method:pw_live_feed Arguments: ',args);
			var params = {args:args};
			return this.wp_ajax('pw_live_feed',params);
		},
		o_embed: function(url,args) {
			$log.info('Service: pwData Method:o_embed Arguments: ',args);
			var params = { url:url, args:args};
			return this.wp_ajax('o_embed',params);
		},
		pw_get_posts: function(feed_id, post_ids, fields) {
			$log.info('Service: pwData Method:pw_get_posts Arguments: ',feed_id, post_ids, fields);
			var params = { feed_id:feed_id, post_ids:post_ids, fields:fields};
			return this.wp_ajax('pw_get_posts',params);
		},
		pw_get_templates: function(templates_object) {
			$log.info('Service: pwData Method:pw_get_templates Arguments: ',templates_object);
			var params = { templates_object:templates_object};			
			return this.wp_ajax('pw_get_templates',params);
		},
    };
});
