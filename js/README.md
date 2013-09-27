Postworld // Angular / JS Functions
=========


## General Functions


##### AngularJS
The Javascript methods for Postworld are build using the [AngularJS](http://angularjs.org/) framework.


###**wp_ajax** ( function, args )
- A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
- Sends 'function' command with args to Wordpress function which has been registered with `wp_ajax_` action hooks
- See : http://codex.wordpress.org/AJAX_in_Plugins

**return** : *JSON encoded DATA response*

------

###**o_embed** ( url, args )
- Uses `wp_oembed_get()` WP function via AJAX
- See : http://codex.wordpress.org/wp_oembed_get

**return** : *Object*

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
See `pw_get_templates()` PHP method.

####Description:
- Javascript node for `pw_get_templates()` PHP method
- Used by `pw_load_feed()` and `pw_live_feed()` JS methods

####Process:
- Run `pw_get_templates()` PHP method via AJAX
- Return the data

**return** : *JSON* 

------

###**pw_scroll_feed** ( feed_id )


####Description:

- Pushes the next set of posts for infinite scroll

####Process:
1. Set `feed_data[feed_id]['status'] : 'loading'`

2. See which posts have already been loaded `feed_data[feed_id]['loaded']`
3. Compare loaded posts to feed_outline.
  * If they're all already loaded, return `feed_data[feed_id]['status'] : 'all_loaded'`

4. If there are new posts to load:
  * Make an array of the next set of posts to load by loading the next number of posts defined by `feed_data[feed_id]['load_increment']` in sequence from feed_outline
  * Get fields from : `feed_data[feed_id]['feed_query']['fields']`
  * Run `pw_get_posts ( feed_id, load_posts, fields )`
  * Set `feed_data[feed_id]['status'] : 'loaded'`

**return** : *true*

------

###**pw_live_feed** ( args )

####Process:
1. Access `pw_live_feed()` PHP Method via AJAX 
2. Use returned data to populate `feed_data[feed_id]` JS Object with **feed_outline**, loaded and post data

####Parameters:
  - Same as `pw_live_feed()` PHP Method

**return** : *Object*
``` javascript
{
feed_outline : [1,3,5,8,12,16,24,64],
post_data : { Object } 
}
```

------

## Directives


###live-feed ( *postworld.directive* )

####Description:
Displays a live unregistered feed based on `feed_query pw_query()` args

####Process:

1. Populate `feed_data[feed_id]` JS Object with `feed_init[feed_id]`
2. Setup DOM structure with ng-controller and ng-repeat for displaying the feed
3. Run JS method : `pw_live_feed()`


####Parameters:
Parameters are passed via `feed_init[feed_id]`.

**preload** : *integer*  
Number of posts to load at the beginning, before infinite scrolling

**load_increment** : *integer*  
Number of posts to load at a time when using infinite scroll

**order_by** : *string* (optional)

**panel** : *string* (optional)

**view** : *object*

**feed_query** : *string / object*
  - object - an object of query args which is passed to `pw_query()`


####Usage:
````javascript
feed_init['feed_id'] = {
     preload: 3,
     load_increment : 10,
     order_by : 'rank_score',
     panel : 'feed_top',
     view : {
          current : 'detail',
          options : [ 'list', 'detail', 'grid' ],
     }
     feed_query : {…}

}
```
```html
<div live-feed="feed_id"></div> 
```

------

### load-feed ( *postworld.directive* )

#### Description:
- Loads a registered feed, which has been registered with the `pw_register_feed()` PHP method

####Process:

**PHP / AJAX :**  
1. Run `pw_get_feed( feed_id, preload )` PHP method via AJAX.  
  **returns** : 
    feed_outline
    post_data
    feed_query
    ...

2. Populate `feed_data[feed_id]` JS Object with **feed_outline**, and **feed_query**
3. Populate `feed_data[feed_id][['posts']` Object with post_data posts


**JAVASCRIPT :**  
1. Populate `feed_data[feed_id]` Object with `feed_init[feed_id]` Object 

**return** : *true*

####Usage:

```javascript
feed_init['feed_id'] = {
     preload: 3,
     load_increment : 10,
     view : {
          current : 'detail',
          options : [ 'list', 'detail', 'grid' ],
     }
}
```
```html
<div live-feed="feed_id"></div> 
```

#### Requires:
- `pw_cache_feed()` PHP Method

------

### load-panel ( *postworld.directive* )

#### Description:
- Loads a panel by **panel_id***

####Process:

**PHP / AJAX :**  
1. Run `get_panel( panel_id )` PHP method via AJAX.  
  **returns** : 
``` javascript
{
    panel_id : "template_url.html"
}
```

2. Populate `templates.panels[feed_id]` JS Object with data object

**JAVASCRIPT :**  
1. Append and compile inner **ng-include** directive like:
``` html 
<div ng-include="template_url.html" class="inner"></div>
```

**return** : *scope*

####Usage:

```html
<div load-panel="ad_panel" width="300" height="100" class="panel_class" id="ad_panel_id"></div> 
```

- Designer can optionally add a custom **ng-controller*** to the html here.

#### Requires:
- `pw_cache_feed()` PHP Method

------

## Object Anatomy

### feed_data *Object*
+ A meta object which contains feed status and data of all feeds on the DOM

``` javascript
feed_data = {
     feed_id : {
          feed_outline : [1,3,5,15,52,64],
          loaded : [1,3,5],
          status : 'loaded',
          posts : [Object],
     },
     feed_id : {
          …
     }
}
```

------

###feed_init *Object*
+ Used to initialize a feed directive
+ The contents of this object are then transferred into feed_data[feed_id] after initialization

``` javascript
feed_init[feed_id] = {
     preload : 10,
     load_increment : 10,
     offset : 0,
     max_posts : 0,
     order_by : 'rank_score',
     panel : 'feed_top',
     view : {
          current : 'list',
          options : ['list','detail','grid','full']
     },
     query_args : {
          (pw_query Args in JSON format)
     }
}
```

------

### templates *Object*
+ A meta object which contains urls to templates

``` javascript
templates = {
	posts : {
	     'post' : {
	          'list' : '/wp-content/plugins/postworld/templates/posts/post-list.html',
	          'detail' : '/wp-content/plugins/postworld/templates/posts/post-detail.html',
	          'full' : '/wp-content/theme_name/postworld/templates/posts/post-full.html',
	          },
	     },
	panels : {
	     'feed_top': '/wp-content/plugins/postworld/templates/panels/feed_top.html',
	     'front_page': '/wp-content/theme_name /postworld/templates/panels/front_page.html',
	     },
};
```

------

## Related Notes

**Angular JS Template Structure**  
https://www.evernote.com/shard/s275/sh/08be24c4-0630-430b-b118-1e23138664fa/d5a2af40ae684188d12d7e4cc355090f

**PHP, MySQL, Wordpress Functions**  
https://github.com/phongmedia/postworld/tree/master/php

**GitHub repo**  
https://github.com/phongmedia/postworld/ 