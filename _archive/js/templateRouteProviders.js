
	////////// ROUTE PROVIDERS //////////
	$routeProvider.when('/live-feed-1/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed1Widget.html',                           
		});
	$routeProvider.when('/live-feed-2/',    
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed2Widget.html',
			// reloadOnSearch: false,                
		});
	$routeProvider.when('/live-feed-2-feeds/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed3Widget.html',                
		});
	$routeProvider.when('/live-feed-with-ads/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed6Widget.html',                
		});
	$routeProvider.when('/live-feed-2-feeds-auto/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed4Widget.html',                
		});
	$routeProvider.when('/live-feed-params/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed5Widget.html',                
		});
	$routeProvider.when('/load-feed-1/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed1Widget.html',                
		});
	$routeProvider.when('/load-feed-2/',
		{
			template: '<h2>Coming Soon</h2>',               
		});
	$routeProvider.when('/load-feed-2-feeds/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed3Widget.html',                
		});
	$routeProvider.when('/load-feed-cached-outline/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed4Widget.html',                
		});
	$routeProvider.when('/load-feed-ads/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed5Widget.html',                
		});
	$routeProvider.when('/load-panel/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPanelWidget.html',                
		});
	$routeProvider.when('/home/',
		{
				template: '<h2>Coming Soon</h2>',               
		});
	$routeProvider.when('/edit-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/editPost.html',                
		});

	$routeProvider.when('/load-comments/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadCommentsWidget.html',             
		});            
	$routeProvider.when('/embedly/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedlyWidget.html',             
		});            
	$routeProvider.when('/load-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPostWidget.html',             
		});            
	$routeProvider.when('/o-embed/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedWidget.html',             
		});       
	$routeProvider.when('/media-modal/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/mediaModal.html',             
		});  
	$routeProvider.when('/post-link/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/postLink.html',             
		});  
	$routeProvider.when('/test-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwTestWidget.html',             
		});  
