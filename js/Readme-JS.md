Postworld // Angular / JS Functions
=========

###Index
###Services
0. [pwData Service](#service-pwData)
###Directives
0. [__Live Feed Directive__](#directive-live-feed)
0. [__Load Panel Directive__](#directive-load-panel)
0. [__Feed Item Directive__](#directive-feed-item)
0. [__Infinite Scroll Directive__](#directive-infinite-scroll)


#service-pwData pwData Service

# Directives
## Live Feed Directive
## Load Panel Directive
## Feed Item Directive
## Infinite Scroll Directive

## General Functions


##### AngularJS
The Javascript methods for Postworld are build using the [AngularJS](http://angularjs.org/) framework.


###__wp_ajax__ ( function, args )
- A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
- Sends 'function' command with args to Wordpress function which has been registered with `wp_ajax_` action hooks
- See : http://codex.wordpress.org/AJAX_in_Plugins

__return__ : *JSON encoded DATA response*

------

###__o_embed__ ( url, args )
- Uses `wp_oembed_get()` WP function via AJAX
- See : http://codex.wordpress.org/wp_oembed_get

__return__ : *Object*

------

## Functions

------

###__pw_get_posts__ ( feed_id, post_ids, fields )
- Used to access pw_get_post() PHP Method via AJAX

####Parameters:
__feed_id__ : *string*  
The ID of the Postworld feed

__post_ids__ : *array*  
An array of post_ids to load from the outline

__fields__ : *object*  
Equivalent to the `pw_get_post()` PHP Method parameters

####Process:
- Run `pw_get_posts()` PHP method via AJAX
- Merge data into JS object : `feed_data[feed_id]['posts']`

__return__ : *boolean*

------

###__pw_get_templates__ ( templates_object )

####Parameters:
See `pw_get_templates()` PHP method.

####Description:
- Javascript node for `pw_get_templates()` PHP method
- Used by `pw_load_feed()` and `pw_live_feed()` JS methods

####Process:
- Run `pw_get_templates()` PHP method via AJAX
- Return the data

__return__ : *JSON* 

------

###__pw_scroll_feed__ ( feed_id )


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

__return__ : *true*

------

###__pw_live_feed__ ( args )

####Process:
1. Access `pw_live_feed()` PHP Method via AJAX 
2. Use returned data to populate `feed_data[feed_id]` JS Object with __feed_outline__, loaded and post data

####Parameters:
  - Same as `pw_live_feed()` PHP Method

__return__ : *Object*
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

__preload__ : *integer*  
Number of posts to load at the beginning, before infinite scrolling

__load_increment__ : *integer*  
Number of posts to load at a time when using infinite scroll

__order_by__ : *string* (optional)

__panel__ : *string* (optional)

__view__ : *object*

__feed_query__ : *string / object*
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

__PHP / AJAX :__  
1. Run `pw_get_feed( feed_id, preload )` PHP method via AJAX.  
  __returns__ : 
    feed_outline
    post_data
    feed_query
    ...

2. Populate `feed_data[feed_id]` JS Object with __feed_outline__, and __feed_query__
3. Populate `feed_data[feed_id][['posts']` Object with post_data posts


__JAVASCRIPT :__  
1. Populate `feed_data[feed_id]` Object with `feed_init[feed_id]` Object 

__return__ : *true*

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
- Loads a panel by __panel_id__

####Process:

__PHP / AJAX :__  
- Run `pw_get_templates( panel_id )` PHP method via AJAX.  
  __returns__ : 
``` javascript
{ panel_id : "template_url.html" }
```

- Populate `templates.panels[feed_id]` JS Object with data object

__JAVASCRIPT :__  
- Append and compile inner __ng-include__ directive like:

``` html
<div ng-include="template_url.html" class="inner"></div>
```

__return__ : *scope*

####Usage:

```html
<div load-panel="panel_id"></div> 
```

- Designer can optionally add a custom __ng-controller__ to the html here.

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

## Edit Post

------

### edit-field ( *postworld.directive* )
- __File__ : *js/postworld.js*
- __Status__ : In development (phongmedia) 

#### Description
- Loads a field on the Edit/Publish Post page
- Renders the input field in the DOM
- Pre-populates it with default/saved data

#### Attributes

__edit-field__ : *string* (required)
- The __name__ and __id__ of the input element
- If a coorosponding value in `window['edit-fields']` exists, this will be used by default

__data-input__ : *string* (required)
- The type of input field
- Options :
  - __input__ (input-text, input-password, input-hidden, input-url)
  - __select__ (select-multiple)

__data-size__ : *integer* (optional)
- The 

__data-value__ : *string* (optional)
- The over-ride value of the field

__data-placeholder__ : *string* (optional)
- The __placeholder__ value for an text input box

__data-object__ : *string* (optional)
- __Default__ : *edit_fields*
- Defines the object from which to pre-populate from
- Uses the key with name of edit-field value
- *Example*

``` javascript 
	var post = { post_title:'This is the Post Title', ...}
```
```html
	<div edit-field="post_title" data-field="input-text" data-object="post" >
```
Will output an text input box with the value : *"This is the Post Title"*


#### Usage

------

__INPUT__ : data-input Attribute

------

__Input : Text__
- Renders a text input box

```html
<div edit-field="post_title" data-input="input-text" data-value="Default Title"></div>
```

__Input : Select__
- Renders a select input dropdown with 'blog' selected

```html
<div edit-field="post_type" data-input="select" data-value="blog"></div>
```

__Input : Multiple Select__
- Renders a multiple select input box, with 'blog' and 'feature' both selected

```html
<div edit-field="post_type" data-input="select-multiple" data-value="blog,feature" data-size="3"></div>
```


------

## Related Notes

__Angular JS Template Structure__  
https://www.evernote.com/shard/s275/sh/08be24c4-0630-430b-b118-1e23138664fa/d5a2af40ae684188d12d7e4cc355090f

__PHP, MySQL, Wordpress Functions__  
https://github.com/phongmedia/postworld/tree/master/php

__GitHub repo__  
https://github.com/phongmedia/postworld/ 


------

## Related Articles

__Retain scroll position on route change in AngularJS?__  
http://stackoverflow.com/questions/14107531/retain-scroll-position-on-route-change-in-angularjs

__WP Ajax Tips__  
http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
