POSTWORLD // Angular / JS Functions
=========


## General Functions


##### AngularJS
The Javascript methods for Postworld are build using the [AngularJS](http://angularjs.org/) framework.


###**wp_ajax** ( function, args )
- A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
- Sends 'function' command with args to Wordpress function which has been registered with `wp_ajax_` action hooks
- See : http://codex.wordpress.org/AJAX_in_Plugins

**return** : JSON encoded DATA response
------

###**o_embed** ( url, args )
- Uses `wp_oembed_get()` WP function via AJAX
- See : http://codex.wordpress.org/wp_oembed_get

**return** : Object
------

## Functions


###**pw_get_posts** ( feed_id, post_ids, fields )
- Used to access pw_get_post() PHP Method via AJAX

####Parameters:
**feed_id** : *string*
The ID of the Postworld feed

**post_ids** : *array*
An array of post_ids to load from the outline

**fields** : *object*
Equivalent to the `pw_get_post()` PHP Method parameters

####Process:
- Run `pw_get_posts()` PHP method via AJAX
- Merge data into JS object : `feed_data[feed_id]['posts']`

**return** : *boolean*

------

###**pw_get_templates** ( templates_object )

####Parameters:
See pw_get_templates() PHP method.

####Description:
- Javascript node for pw_get_templates() PHP method
- Used by pw_load_feed() and pw_live_feed() JS methods

####Process:
- Run pw_get_templates() PHP method via AJAX
- Return the data

**return** : *JSON* 

------

###**pw_scroll_feed** ( feed_id )


####Description:

- Pushes the next set of posts for infinite scroll

####Process:
- Set feed_data[feed_id]['status'] : 'loading'

- See which posts have already been loaded feed_data[feed_id]['loaded']
- Compare loaded posts to feed_outline. If they're all already loaded, return 
     feed_data[feed_id]['status'] : 'all_loaded'

- If there are new posts to load,
	...* Make an array of the next set of posts to load by loading the next number of posts defined by feed_data[feed_id]['load_increment'] in sequence from feed_outline
    ..* Get fields from : feed_data[feed_id]['feed_query']['fields']
    ..* Run pw_get_posts ( feed_id, load_posts, fields )
    ..* Set feed_data[feed_id]['status'] : 'loaded'


return : true


------
###**pw_live_feed** ( args )
• Access pw_live_feed() PHP Method via AJAX 
• Use returned data to populate feed_data[feed_id] JS Object with feed_outline, loaded and post data

Parameters:
     • Same as pw_live_feed() PHP Method

return : Object
     feed_outline : [ … ]
     post_data : { … } 
