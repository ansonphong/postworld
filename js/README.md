Postworld // Angular / JS Functions
=========

## Index
0. [ __General Functions__ ](#general-functions)
0. [ __Functions__ ](#functions)
0. [ __Directives__ ](#directives)
0. [ __Controllers__ ](#controllers)
0. [ __Edit Post__ ](#edit-post)
0. [ __Related Notes__ ](#related-notes)

------

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

------

### load-post *[ directive ]*

#### Description : 
- Loads a single post into the DOM
- Used for displaying single posts and features

#### Parameters :

__load_post[ *name* ]__ : *object*
- A JS Object which defines the settings for the post display

- __post_id__ : *integer*
- __view__ : *string*
  - The template view to display the post in

#### Process :
- Get the template path with `pw_post_template( $post_id, $post_view )` PHP Method via AJAX

#### Usage :

Javascript:

``` javascript
load_post['single_post'] = {
	post_id : 24,
	view : 'full',
}
```

HTML :

``` html
<div load-post="single_post"></div>
```

------

###live-feed *[ directive ]*

#### Description:
Displays a live unregistered feed based on `feed_query pw_query()` args

#### Process:

1. Populate `feed_data[feed_id]` JS Object with `feed_settings[feed_id]`
2. Setup DOM structure with ng-controller and ng-repeat for displaying the feed
3. Run JS method : `pw_live_feed()`


#### Parameters:
Parameters are passed via `feed_settings[feed_id]`.

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
feed_settings['feed_id'] = {
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

### load-feed *[ directive ]*

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
1. Populate `feed_data[feed_id]` Object with `feed_settings[feed_id]` Object 

__return__ : *true*

####Usage:

```javascript
feed_settings['feed_id'] = {
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

### load-panel *[ directive ]*

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

###feed_settings *Object*
+ Used to initialize a feed directive
+ The contents of this object are then transferred into feed_data[feed_id] after initialization

``` javascript
feed_settings[feed_id] = {
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

### load-comments *[ directive ]*
- Loads in the comments for a given post
- Template :
  - `templates/comments/comment-single.html`

Javascript :

``` javascript
load_comments['post_single'] = {
	post_id : 24,
	sort_by : 'rank_score',
	sort_options : {
		'comment_points' : "Points",
		'date' : "Date"
		},
	max_points : 0,
	min_points : -10, 
};

```

HTML : 

``` HTML
<div load-comments="post_single"></div>
```

------

### add-comment *[ directive ]*

__Status__ : In concepting (phongmedia)

#### Description
- Produces an add comment form
- Template :
  - `templates/comments/comment-add-text.html`

#### Attributes
__add-comment__ : *string*
- The type of comment form
- Options:
  - __text__
  - *rich* (for future implimentation)

__post-id__ : *integer*
- The post ID of the post to add the comment to

__comment-parent__ : *integer*
- The comment ID of the comment to which it is a response

``` HTML
<div add-comment="text" data-post-id="24" data-comment-parent="45324"></div>
```

#### Notes
- Add / edit comment / Reply to comment - on success - append self to object - show green check 

------

## Controllers

------

> Controllers go here...

------

## Edit Post

------

### edit-form *[ controller ]*
- __File__ : *js/postworld.js*
- __Status__ : In concepting 

#### Description
- Sits ontop of a form
- Controls and manages input and output for the value of form fields


#### Methods

__Load__ : *args*
- Pull in new form data from server 
- Parameters:
  - 

__Poll__ : *args*
- Polls a value from the DB and updates the model
- Parameters:
  - 


__Submit__
- Submits callback function($args) via AJAX
- Parameters:
  - 

__Filter__
- Make a syntax or format transformation between model and DOM
- Types:
  - __tax-input__ - Conditions a *select* form element into the `pw_insert_post()` data model
  - __tags-input__ - Conditions a *text input* form element into the `pw_insert_post()` data model
  - __tax-query__ - Conditions a *select* form element into the `pw_query()` data model

------

#### Filter Methods

__INPUT FILTERS__ : Query filters condition a model for submission to `wp_insert_post()` : [WP Insert Post Parameters](http://codex.wordpress.org/Function_Reference/wp_insert_post#Parameters)


__tax_input__ : *input - select / multiple select*
- Conditions the input select effect on the attributed model for __tax_input__ field on `wp_insert_post()`

HTML View:

``` html
<div edit-field="taxonomy(category)" data-object="post_obj">
	<select multiple name="category" data-bind="post_obj.tax_input.category">
		<option value="term1" selected>Term One</option>
		<option value="term2">Term Two</option>
		<option value="term3" selected>Term Three</option>
		<option value="term4">Term Four</option>
	</select>
</div>
```

Model Output:

``` javascript

post_obj = {
	...,
	'tax_input' : {
		'category' : [ 'term1', 'term3' ],
		'topic' : [ 'term8', 'term12' ],
	}
}

```

__tags_input__ : *text input*

HTML View:
  
``` html
<div edit-field="tags_input" data-input="input-text" data-object="post_obj">
	<input name="tags_input" data-bind="post_obj.tax_input.tags_input" value="tag1,tag2,tag3">
</div>
```

Model Output:

``` javascript
post_obj = {
	...,
	'tags_input' : [ 'tag1', 'tag2', 'tag3' ]
}

```

__QUERY FILTERS__ : Query filters condition a model for submission to `pw_query()`  

__tax_query__ : *input - select / multiple select*
- Conditions the input select effect on the attributed model for __tax_query__ field on `pw_query()`  
- [WP Query Taxonomy Parameters](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters)

HTML View

``` html
<div edit-field="taxonomy(topic)" data-object="query_obj">
	<select multiple name="category" data-bind="query_obj.tax_query">
		<option value="term1" selected>Term One</option>
		<option value="term2">Term Two</option>
		<option value="term3" selected>Term Three</option>
		<option value="term4">Term Four</option>
	</select>
</div>

<div edit-field="taxonomy(section)" data-object="query_obj">
	<select multiple name="category" data-bind="query_obj.tax_query">
		<option value="term1">Term One</option>
		<option value="term2" selected>Term Two</option>
		<option value="term3">Term Three</option>
	</select>
</div>

```

Model Output:

``` javascript

query_obj = {
	...,
	'tax_query' : {
		'relation' => 'AND',
		{
			'taxonomy' : 'topic',
			'field' : 'slug',
			'terms' : ['term1','term3']
			'operator' => 'AND'
		},
		{
			'taxonomy' : 'section',
			'field' : 'slug',
			'terms' : ['term2']
			'operator' => 'AND'
		},
	}
}

```


------

### edit-field *[ directive ]*
- __File__ : *js/postworld.js*
- __Status__ : In development (phongmedia) // October 8, 2013

#### Description
- Loads a form field on
  - Edit/Publish Post page
  - Search Panel
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
  - __textarea__

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

### edit-field *[ controller ]*

__Status__ : In concepting (phongmedia)

#### Descriptions

- Controls form field's input in relation to a given model

#### Models

__QUERY MODEL__

__taxonomy__ : *input - select / multiple select*
- Conditions the input select effect on the attributed model
- Input:  
  
``` html

<div ng-controller="edit-form">
	
	<div>
		<select >
			<option></option>
			<option></option>
		</select>
	</div>

</div>

```



__EDIT POST MODEL__



------

## Voting

------

### vote-panel *[ directive ]*

#### Description
- Makes it easy to embed a voting panel onto a post template
- Allows user to vote on the panel

#### Attributes

__vote-panel__ : *integer*
- The ID of the post voting on

__data-user__ : *integer* (optional)
- The ID of the current user

__data-type__ : *string* (optional)
- Default : *post*
- Options :
  - post
  - comment


#### Usage
``` html
<div vote-panel="{{post_id}}" data-type="post"></div>
```

#### Return
``` html
<div vote-panel="2" ng-controller="vote-panel">
	<div class="vote_up"></div>
	<div class="post_points">{{ post_points }}</div>
	<input type="hidden" class="user_vote" name="user_vote" value="{{user_vote}}" ng-bind="post.user_vote">
	<div class="vote_down"></div>
</div>
```

------

### vote-panel *[ controller ]*

#### Description
- Sends and receives input for voting on posts and comments

#### Process
- Sit ontop of *vote-panel*
- Watch for change in __user_vote__ model
- AJAX Methods :
  - When __user_vote__ changes, update the database with `set_post_points()` / `set_comment_points()`
  - Get the updated number of points with `get_post_points()`

------

## Users

------

### user-feed *[ directive ]*

__Status__ : In Concepting (phongmedia)

#### Description

- Displays a list of users from a user query
- Similar to **live-feed** directive - instead of showing posts, show users

#### Process

- Access `pw_user_query()` method via AJAX
- Return an outline of USER IDs
- Display users according to template and feed settings


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

