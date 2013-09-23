POSTWORLD // Angular / JS Functions
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


## Directives


###live-feed
**postworld.directive**

####Description:
• Displays a live unregistered feed based on feed_query pw_query() args

####Process:
• Populate feed_data[feed_id] JS Object with feed_init[feed_id]
• Setup DOM structure with ng-controller and ng-repeat for displaying the feed
• Run JS method : pw_live_feed()


####Parameters:

Parameters are passed via an object with the same name as the 'feed_id'

preload : integer
Number of posts to load at the beginning, before infinite scrolling

load_increment : integer
Number of posts to load at a time when using infinite scroll

order_by : string (optional)

panel : string (optional)

view : object

feed_query : string / object
     • object - an object of query args which is passed to pw_query()


####Usage:
````
<script>
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
</script>
<div live-feed="feed_id"></div> 
```



### load-feed 
**postworld.directive**

#### Requires:
- pw_cache_feed() PHP Method

#### Description:
- Loads a registered feed, which has been registered with the pw_register_feed() PHP method

####Process:

**PHP / AJAX :**

1. Run pw_get_feed( feed_id, preload ) PHP method via AJAX.
     returns : 
          feed_outline
          post_data
          feed_query
          ...


2. Populate `feed_data[feed_id]` JS Object with **feed_outline**, and **feed_query**
3. Populate `feed_data[feed_id][['posts']` Object with post_data posts


**JAVASCRIPT :**

1. Populate `feed_data[feed_id]` Object with settings Object 

**return** : *true*


Usage:

```
<script>
feed_init['feed_id'] = {
     preload: 3,
     load_increment : 10,
     view : {
          current : 'detail',
          options : [ 'list', 'detail', 'grid' ],
     }
}
</script>
<div live-feed="feed_id"></div> 
```


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









RELATED NOTES : 

Angular JS Template Structure
https://www.evernote.com/shard/s275/sh/08be24c4-0630-430b-b118-1e23138664fa/d5a2af40ae684188d12d7e4cc355090f



PHP, MySQL, Wordpress Functions
https://www.evernote.com/shard/s275/sh/7fd5bb62-0902-4050-9889-338f847d044c/5fe6f3da3510557663e8693251ac8557

GitHub repo
https://github.com/phongmedia/postworld/ 