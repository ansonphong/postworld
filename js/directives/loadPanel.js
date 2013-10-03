'use strict';

pwApp.directive('loadPanel', function($log, pwData) {
    return {
        restrict: 'A',
        templateUrl: function(tElement,tAttrs) {
        	pwData.setNonce(78);
        	return '/wordpress/wp-content/plugins/postworld/templates/panels/feed_header.html';
        	/*
        	// Get Template from ID
        	var templates = pwData.pw_get_templates({'name':'name'}).then(
				// Success
				function(response) {
					$log.info('Directive: LoadPanel TemplateUrl ServiceReturned');
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						$log.info('Directive: LoadPanel TemplateUrl Success with data', response.data);
						$log.info('Directive: LoadPanel TemplateUrl Success feed:', response.data.panels.feed_header);
						return response.data.panels.feed_header; // [tAttrs.loadPanel];
					} else {
						// handle error
						$log.error('Directive: LoadPanel TemplateUrl Failure with response', response.status, response.message);
						// TODO should we set busy to false when error is returned?
					}
					// return response.posts;
					return templates; // [tAttrs.loadPanel];
				},
				// Failure
				function(response) {
					$log.error('Directive: LoadPanel TemplateUrl Failure with response', response);
					// TODO how to handle error?
				}
			);			        	
			*/        	
        },
        replace: true,
        controller: 'pwLoadPanelController',
    };
});
