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

###__embedly_extract__ ( *url, [object]* )

#### Description
- Uses __embed.ly__ extract service : http://embed.ly/extract
- Input the URL into __embed.ly__ API, return with extracted data object

#### Parameters
__url__ : *string*
- The URL which to submit to Emebed.ly Extract

__object__ : *string* (optional)
- The Javascript object which to inject the data into on success


#### Usage

- __Method__ : 1

``` javascript
var url = 'http://www.youtube.com/watch?v=38peWm76l-U';
var post = {};
embedly_extract( url, post.embedly_extract );

// Produces :

post = {
	'embedly_extract' : {
	    "url": "http://www.youtube.com/watch?v=38peWm76l-U", 
	    "title": "Earth From Space HD 1080p / Nova"
	    "provider_name": "YouTube", 
	    "description": "The groundbreaking two-hour special that reveals a spectacular new space-based vision of our planet. Produced in extensive consultation with NASA scientists, NOVA takes data from earth-observing satellites and transforms it into dazzling visual sequences, each one exposing the intricate and surprising web of forces that sustains life on earth.", 
		  ...
    }
	};

```

- __Method__ : 2

``` javascript

var url = 'http://www.youtube.com/watch?v=38peWm76l-U';
var post = {
	'embedly_extract' : embedly_extract( url );
	};

```
__return__ : *Object* (embed.ly extract data)

------

###__pw_embedly_extract__ ( *url, [object]* )

Status : In concepting... (phongmedia)

#### Description
- A wrapper for `embedly_extract()` JS Method which conditions / remaps the object for Postworld/Wordpress input fields

#### Process

__FILTERS__
- __title__ ›rename› __post_title__
- __description__ ›rename› __post_excerpt__

__return__ : *Object*

------

## Functions

------

### load_post ( post_id, fields, callback )

- Pull in new post data from server 

#### Parameters:
__post_id__
- The post to load via __pw_get_post()__

__fields__
- Default : *edit*
- The fields to pass to __pw_get_post()__

__callback__
- The function which to call with the reponse


------

### save_post ( *post_id, object, callback* )

- Save post data to the server
- Use `pw_save_post()` via AJAX

#### Parameters:

__post_id__
- Default : *null* (creates a new post)

__object__
- Default : *edit_post*
- The object to pass which contains the post data

__callback__
- The function which to call with the reponse

------

###__pw_get_posts__ ( *feed_id, post_ids, fields* )
- Used to access `pw_get_post()` PHP Method via AJAX

####Parameters:
__feed_id__ : *string*  
The ID of the Postworld feed

__post_ids__ : *array*  
An array of post_ids to load from the outline

__fields__ : *object*  
Equivalent to the `pw_get_post()` PHP Method parameters

####Process:
- Run `pw_get_posts()` PHP method via AJAX
- Merge data into JS object : `feeds[feed_id]['posts']`

__return__ : *boolean*

------

###__pw_get_templates__ ( templates_object )

####Parameters:
See `pw_get_templates()` PHP method.

####Description:
- Javascript node for `pw_get_templates()` PHP method
- Used by `pw_feed()` JS method

####Process:
- Run `pw_get_templates()` PHP method via AJAX
- Return the data

__return__ : *JSON* 

------

###__pw_scroll_feed__ ( feed_id )


####Description:

- Pushes the next set of posts for infinite scroll

####Process:
1. Set `feeds[feed_id]['status'] : 'loading'`

2. See which posts have already been loaded `feeds[feed_id]['loaded']`
3. Compare loaded posts to feed_outline.
  * If they're all already loaded, return `feeds[feed_id]['status'] : 'all_loaded'`

4. If there are new posts to load:
  * Make an array of the next set of posts to load by loading the next number of posts defined by `feeds[feed_id]['load_increment']` in sequence from feed_outline
  * Get fields from : `feeds[feed_id]['feed_query']['fields']`
  * Run `pw_get_posts ( feed_id, load_posts, fields )`
  * Set `feeds[feed_id]['status'] : 'loaded'`

__return__ : *true*

------

###__pw_feed__ ( args )

####Process:
1. Access `pw_feed()` PHP Method via AJAX 
2. Use returned data to populate `feeds[feed_id]` JS Object with __feed_outline__, loaded and post data

####Parameters:
  - Same as `pw_feed()` PHP Method

__return__ : *Object*
``` javascript
{
	feed_outline : [1,3,5,8,12,16,24,64],
	post : { Object } 
}
```

------

## Directives

------

### pw-image  *[ directive ]*
- Loads an image object into a predefined scope object / variable

#### Attributes

__image-id__ : *integer / model path*
- The image attachment ID

__image-model__ : *model path*
- The model path which will be populated with the image object

__TODO__ : Add image() fields-like request specific imge sizes, including custom image sizes, like `pw_get_post.`

#### Usage
- The `$scope` object `images` is the reccomended usage to store all the image objects, with a subobject with the ID of the image, *ie.* `link_thumbnail`

```html
  <img
  pw-image
  image-id="{{post.post_meta.link_thumbnail_id}}"
  image-model="images.link_thumbnail"
  ng-src="{{ images.link_thumbnail.url }}">

```

#### Example of `image-model`

```javascript
{
  "width": 480,
  "height": 360,
  "file": "2014/03/188-hqdefault.jpg",
  "url": "http://localhost:8888/wp-content/uploads/2014/03/188-hqdefault.jpg",
  "sizes": {
    "thumbnail": {
      "file": "188-hqdefault-150x150.jpg",
      "width": 150,
      "height": 150,
      "mime-type": "image/jpeg",
      "url": "http://localhost:8888/wp-content/uploads/2014/03/188-hqdefault-150x150.jpg"
    },
    "medium": {
      "file": "188-hqdefault-300x225.jpg",
      "width": 300,
      "height": 225,
      "mime-type": "image/jpeg",
      "url": "http://localhost:8888/wp-content/uploads/2014/03/188-hqdefault-300x225.jpg"
    },
    "banner": {
      "file": "188-hqdefault-480x275.jpg",
      "width": 480,
      "height": 275,
      "mime-type": "image/jpeg",
      "url": "http://localhost:8888/wp-content/uploads/2014/03/188-hqdefault-480x275.jpg"
    },
    "grid": {
      "file": "188-hqdefault-480x275.jpg",
      "width": 480,
      "height": 275,
      "mime-type": "image/jpeg",
      "url": "http://localhost:8888/wp-content/uploads/2014/03/188-hqdefault-480x275.jpg"
    }
  },
  "image_meta": {
    "aperture": 0,
    "credit": "",
    "camera": "",
    "caption": "",
    "created_timestamp": 0,
    "copyright": "",
    "focal_length": 0,
    "iso": 0,
    "shutter_speed": 0,
    "title": ""
  }
}

```


------

### pw-query  *[ directive ]*
- Loads a set of posts into scope based on a query 

#### Attributes

__pw-query__ : *object/model path*
- An object to pass to PHP method `pw_query`, which contains the query variables

__query-results-model__ : *object/model path*
- The array where to deposit the results of the query

__query-status-model__ : *object/model path*
- Where to update the status for the current query instance
- Example: `loading`, `done`, etc...

__query-id__ : *string*
- An ID for the query instance
- Generally used for triggering refreshes with `$on` actions


//// BETTER WAY TO EMIT / UPDATE IT



------

### pw-list-users  *[ directive ]*
- Loads an object into the scope which is an array of user data

#### Attributes

__user-list-id__ : *string*
- The unique identifier for this instance of the user list
- Used by the `pwUserList` Event Listener to verify which list the action is being sent to

__user-fields__ : *Array*
- Fields to pass to the `pw_get_userdata` fields parameter
- Example:
  + {'user_nicename', 'display_name' }

__user-ids__ : *Array*
- Reference to an Array of user IDs

__users-model__ : *object/model path*
- The model in scope where to return the data from `pw_get_userdata`

__users-query__ : *Array* (IN-OP)
- Query for users to auto-populate
- Over-ridden by `user-ids` attribute

#### Scope Methods

### __getUsers( *userIds* )__
- Gets the user data for the array of supplied User IDs
- Gets the attribute `user-fields` fields
- Places the returned array in `user-model` 

### __getUser( *userId* )__


### __pwUserList__ : Event Listener
- Listens for commands broadcast from rootScope or other related emit or broadcast

#### Usage

- Add / Remove User from List with Event Listener:
``` javascript
$rootScope.$broadcast( 'pwUserList', {
    userListId: [string],     // Specify the list ID, equivilant to 
    action: [string],         // Options : addUser / removeUser
    userId: [integer],        // Which User ID to do the action on
    postId: [integer]         // Which Post ID to do the action on (optional)
  } );
```



``` javascript
userListId: $scope.relationsAction,
action: action,
userId: $scope.userId(),
postId: $scope.eventId()
```


------

### wp-media-library *[ directive ]*
- For use on Buttons and clickable items
- Activates the Wordpress Media Library / Uploader, and returns selected images to callback function(s)

#### Attributes
__media-id__ : *string* (optional)
- Sets an ID on the media window for targetted CSS styling

__media-type__ : *string* (optional)
- Select which media type mime type to filter by
- Options:
  + __image__
  + ...

__media-title__ : *string* (optional)
- Sets the title which appears at the top of the media window

__media-button__ : *string* (optional)
- Sets the text which appears on the select button

__media-default-tab__ : *string* (optional) __IN-OP?__
- Sets the default tab which is selected
- Options:
  + __upload__
  + ...

__media-tabs__ : *string* (optional)
- Sets the tabs are available
- Options:
  + __upload__
  + __library__

__media-multiple__ : *boolean* (optional)
- Sets if multiple media files are selected / returned

__media-callback__ : *string* (optional)
- Calls a local callback in the scope of the directive
- Available options:
  + __setPostImage__ - Replaces the `post.image` object with the new image after selected
  + __editPostImage__ - Replaces the `post.thumbnail_id` and `post.image` values in the current `post` object
  + __setOption(option, [field])__ - Sets the selected image object in the `wp_options` table as the specified `option` name. The `field` can optionally be set to `id` and only the image ID will be saved.

__media-parent-callback__ : *string* (optional)
- Calls a callback in the parent scope of the directive
- Value is the name of the function in the parent scope
- If only the function name is provided, it provides one variable of the media array returned from the media window. ie. `functionName(selectedMedia)`

__media-model__ : *string* (optional)
- The name of an object in the parent scope
- The array returned from the media window is populated into this object model

__media-model-array__ : *boolean* (optional)
- Options:
  + `false` *(default)* - the media model with only one image selected will be automatically taken out of the array and converted into an object. ie. `[{ imageObj }]` ›› `{imageObj}`
  + `true` - the media model will be preserved as an array, even if only one image is selected. ie. `[{ imageObj }]`


#### Usage
``` html
<button
      wp-media-library
      media-id="setImage"
      media-type="image"
      media-title="Select a Cover Image"
      media-button="Set Cover Image"
      media-default-tab="upload"
      media-tabs="upload, library"
      media-multiple="false"
      media-callback="setPostImage"
      media-parent-callback="localSetPostImage"
      media-model="thumbnailImage">
      Upload Image
    </button>
```

#### Requirements
- The following line of PHP code must be on the page somewhere for this directive to work

```php
wp_enqueue_media();
```


-----

### o-embed *[ directive ]*
- Populates the element with the embed code for a given media URL

#### Attributes
__o-embed__ : *expression*
- The URL of the URL to embed

__autoplay__ : *boolean*
- Whether or not to auto-play (support limited to Vimeo / YouTube)

__run__ : *boolean*
- If this is *false*, the o-embed will not run
- This is set on a watch, so if this changes to true the *o-embed* will run

#### Usage

```html
<div o-embed="post.link_url" autoplay="true">
  <div class="o-embed" ng-bind-html="oEmbed"></div>
</div>
```

- An additional `oEmbedCode` scope variable is created, which is populated with the raw HTML embed code for dev inspection.

```html
  <div ng-bind="oEmbedCode | json"></div>
```

------

### pw-load-post *[ directive ]*

#### Description : 
- Loads a single post into the DOM
- Used for displaying single posts and features

#### Parameters :

__load_post[ *name* ]__ : *object*
- A JS Object which defines the settings for the post display

- __post_id__ : *integer*
- __fields__ : *string* (optional)
  - Default: __all__
  - Pass to `pw_get_post()` › `$fields` parameter 
- __view__ : *string*
  - The template view to display the post in
  - Path : *templates/posts/{{post_type}}-{{view}}.html*
  - `pw_get_templates` Object : `posts[post_type][post_view]`

#### Process :
- Use `pw_get_post()` via AJAX to get post data
- Populate view template with localized post data object

#### Usage :

Javascript:

``` javascript
load_post['single_post'] = {
	post_id : 24,
  fields : 'all',
	view : 'full',
}
```

HTML :

``` html
<div pw-load-post="single_post"></div>
```

------

###live-feed *[ directive ]*

#### Description:
Displays a live unregistered feed based on `feed_query pw_query()` args

#### Process:

1. Populate `feeds[feed_id]` JS Object with `pw.feeds[feed_id]`
2. Setup DOM structure with ng-controller and ng-repeat for displaying the feed
3. Run JS method : `pw_feed()`


#### Parameters:
Parameters are passed via `pw.feeds[feed_id]`.

__preload__ : *integer*  
Number of posts to load at the beginning, before infinite scrolling

__load_increment__ : *integer*  
Number of posts to load at a time when using infinite scroll

__order_by__ : *string* (optional)

__panel__ : *string* (optional)

__view__ : *object*

__feed_query__ : *string / object*
  - object - an object of query args which is passed to PHP `pw_query()`


####Usage:
``` javascript
pw.feeds['feed_id'] = {
     preload: 3,
     load_increment : 10,
     order_by : 'rank_score',
     panel : 'feed_top',
     view : {
          current : 'detail',
          options : [ 'list', 'detail', 'grid' ],
     }
     query : {}

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
    post
    query
    ...

2. Populate `feeds[feed_id]` JS Object with __feed_outline__, and __feed_query__
3. Populate `feeds[feed_id][['posts']` Object with post posts

__JAVASCRIPT :__  
1. Populate `feeds[feed_id]` Object with `pw.feeds[feed_id]` Object 


#### Parameters

__title__ : *string* (optional)
- Populates the template title

__feed_outline__ : *Array* (optional)
- An array of posts to show in order
- If no `feed_outline` is supplied, will load the registered feed with the same feed ID

__preload__ : *integer*
- How many posts to load before infinite scrolling

__load_incremenet__ : *integer*
- How many posts to load at a time with infinite scrolling

__view__ : *Object*
- __current__ : *string* 
- __options__ : *Array*

__feed_template__ : *string* (panel ID)
- Optional, needed in case of different widgets [having different panels for example]


####Usage

```javascript
pw.feeds['feed_id'] = {
     title: "Load Feed",
     feed_outline : ["166725","166713","166716","166359","165840","166241","165969",...],
     preload: 5,
     load_increment : 10,
     view : {
          current : 'detail',
          options : [ 'list', 'detail', 'grid' ],
     },
     feed_template : 'load_feed_1',
     aux_template: 'seo-list',
}
```
```html
<div live-feed="feed_id" ng-include="templateUrl"></div> 
```


#### Requires:
- `pw_cache_feed()` PHP Method

------

### load-panel *[ directive ]*

#### Description:
- Loads a panel by __panel_id__

#### Attributes

_load-panel_ : *string*
- The panel ID of the panel to load
- Panel ID coorosponds with the file name with `.html` extension, found in the `templates/panels`. ie. for  `templates/panels/myPanel.html` the ID is simply `myPanel`

_panel-post_ : *Binding* (optional)
- Post object to bind to panel
- If provided, will bind to panel scope interior object `$scope.post`

_panel-meta_ : *Angular Expression* (optional)
- Input an expression which is passed into the isolated scope of the panel as `$scope.panelMeta`

#### Usage : With HTML Attributes

```html
<div
  load-panel="myPanel"
  panel-meta="example.myPanelMeta"></div> 

<div
  load-panel="myPanel"
  panel-meta="{ key: 'value' }"></div> 

<div
  load-panel="myPanel"
  panel-post="post"></div> 
```


------


### post-link *[ controller ]*

- Controls the __status__ and __image selection__ in the __post_link__ panel template

#### Process

__Status__
- Conditions a state for the __post_link__ template to change state depending on the value of various fields
- __status__ = 
  - __url_input__ - *(default)*
  - __post_input__ - When current form's __post_title__ has a value (populated by `pw_embedly_extract()` )
  - __success__ - After a successful response from `pw_save_post()` 

__Image Selection__
- Watch object of Embed.ly Extract function `pw_embedly_extract()`, which will populate an object with image properties
- Track of the current cycle through with `pw_next_image()` & `pw_prev_image()`
- Update hidden field `thumbnail_url` in post object to the coorosponding image

Example of images object from Embed.ly:

``` javacript

"images": [
        {
            "width": 480, 
            "url": "http://i1.ytimg.com/vi/38peWm76l-U/hqdefault.jpg", 
            "height": 360, 
            "size": 44269,
            ...
        }, 
        {
            "width": 48, 
            "url": "https://lh5.googleusercontent.com/-LBcQruSPiVE/AAAAAAAAAAI/AAAAAAAAAAA/ZZy901nR234/s48-c-k/photo.jpg", 
            "height": 48, 
            "size": 2316,
            ...
        }
    ], 

```

------

## Object Anatomy

### feeds *Object*
+ A meta object which contains feed status and data of all feeds on the DOM

``` javascript
feeds = {
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

###pw.feeds *Object*
+ Used to initialize a feed directive
+ The contents of this object are then transferred into feeds[feed_id] after initialization

``` javascript
pw.feeds[feed_id] = {
  preload : 10,
  load_increment : 10,
  feed_outline : [24,48,45,...], // (Optional) Loads a pre-defined feed outline
  offset : 0,
  order_by : 'rank_score',
  panels : {
    'panel2'  : 'feed_header',  
  },
  view : {
    current : 'list',
    options : ['list','detail','grid','full']
  },
  blocks : { // (Optional) Loads in blocks inserted into the feed
    offset:3,
    increment: 3,
    max_blocks:50,
    template: 'ad-block'
  },
  query : {
    // pw_query Args in JSON format
    'post_type':['feature','blog'],
    'post_class':'author',
    'link_format':'video',
    'posts_per_page' : 200
    //...
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

#### Description

- Loads in the comments for a given post
- Templates :
  - `templates/comments/comments-header.html`
  - `templates/comments/comment-single.html`

#### Process
- Takes a hierarchical object of comments and displays them in a nested structure
- Use [__pw_get_comments()__](https://github.com/phongmedia/postworld/tree/master/php#pw_get_comments--query-fields-tree-) PHP Method via AJAX to get comments object

#### Parameters

__query__ : *Object*
- Query arguments for which comments to return
- This is passed to __pw_get_comments()__ `$query` parameter
- Same as Wordpress __get_comments($args)__ Parameters : [Function Reference / get_comments](http://codex.wordpress.org/Function_Reference/get_comments#Parameters) 

__fields__ : *Array*
- Default : __all__
- Which comment fields to return for access in the template
- Options defined in __pw_get_comment()__ PHP Method documentation : [Comment Fields](https://github.com/phongmedia/postworld/tree/master/php#pw_get_comment--comment_id-fields-viewer_user_id-)  
- This is passed to the __pw_get_comments()__ `$tree` parameter

__tree__ : *boolean*
- Default : __true__
- Whether or not to display 
- This is passed to the __pw_get_comments()__ `$tree` parameter

__order_by__ : *string*
- Options :
  - comment_points
  - comment_date

__order_options__ : *Object*
- An object which defines the select options for sorting
- Structure for *key:value* is  __sort_field:description__

__min_points__ : *integer*
- Minimum points a comment must have to appear maximized by default
- Comments with less than this will appear minimized by default

__live_poll__ : *integer*
- Default : __0__ (No Polling)
- Updates the comment feed on time set in the directive setting
- Integer defines the number of seconds between polls

__live_poll_while_writing__ : *boolean*
- Default : __false__ (no polling while writing a comment)
- Whether to poll while user is currently editing a comment (which might disturb the commenting process)



#### Usage

Javascript :

``` javascript
load_comments['post_single'] = {
    query : {
        post_id : 133925, // 21853, // 133925, // 166220, // 21853, // 166220,
        status: 'approve',
      orderby : 'comment_date',
        },
    fields : 'all',
    tree : true,
    order_options : {
        'comment_points' : 'Points',
        'comment_date' : 'Date'
        },
    min_points : 0,
    live_poll: 60,
   live_poll_while_writing: false,
};
```

HTML : 

``` HTML
<div load-comments="post_single"></div>
```

------

### edit-comment *[ directive ]*

#### Description

- Produces an edit comment form
- Template :
  - `templates/comments/comment-edit.html`

#### Attributes

__add-comment__ : *string*
- The *type* of comment form
- Options:
  - __text__
  - *rich* - (future implimentation)

__post-id__ : *integer*
- The post ID of the post to add the comment to

__comment-parent__ : *integer* (optional)
- The comment ID of the comment to which it is a response

__comment-id__ : *integer* (optional)
- Provide the comment ID of the comment to which it is a response

#### Scope Variables

__mode__
- Set `mode` in scope
- Options:
  - __add__ - If no `comment-id` is provided
  - __edit__ - If `comment-id` is provided, pre-populate `comment_content` with data from comments object
  - __reply__ - If `data-comment-parent` is provided


#### Usage

For a new comment:

``` HTML
<div add-comment="text" data-post-id="24" data-comment-parent="45324"></div>
```

To edit a comment:

``` HTML
<div add-comment="text" comment-id="435"></div>
```

#### PHP Helper Functions

- `pw_get_comment( $comment_id )` - Get the data array for an existing comment
- `pw_save_comment( $comment_data, [$return] )` - Save the data array for an new / edited comment
  - Returns by default the result of `pw_get_comment( $comment_id )` (comment data *Array*) for the new / edited comment


#### Callback

__update_comments_object__ : *function*
- On successful add / edit comment - append self to comments object

------

## Controllers

------

> Controllers go here...

------

## Edit Post

------

### edit-post *[ route ]*
- Setup an environment for creating or editing posts

#### Routing Parameters

__Routing__ : *URL Parameters*  
Two methods for routing:
- __/#/new/{{post_type}}__  
  - Sets up a new __edit post object__ with the __post_type__ pre-selected  
  - After successful creation of new post, reload to */#/edit/{{post_id}}* 
- __/#/edit/{{post_id}}__  
  - Sets up an __edit post object__, pre-populated with the data from the given __post_id__  
  - Use `pw_get_post($post_id, 'edit')` via AJAX

------

### filter-form *[ controller ]*
- __File__ : *js/postworld.js*

#### Description
- Sits ontop of a form
- Controls and manages input and output for the value of form fields

__Filter__
- Make a syntax or format transformation between model and DOM
- Optionally added as services (?)
- Types: *(optionally applied to the controller)*
  - __tax_input__ - Conditions a *select* form element into the `pw_insert_post()` data model
  - __tags_input__ - Conditions a *text input* form element into the `pw_insert_post()` data model
  - __tax_query__ - Conditions a *select* form element into the `pw_query()` data model
  - __link_format__ - Conditions a field for __link_format__ depending on the contents of __link_url__ field


#### Filter Methods

------

__INPUT FILTERS__ : Query filters condition a model for submission to `wp_insert_post()` : [WP Insert Post Parameters](http://codex.wordpress.org/Function_Reference/wp_insert_post#Parameters)

------

__tax_input__ : *input - select / multiple select*
- Conditions the input select effect on the attributed model for __tax_input__ field on `wp_insert_post()`

HTML View:

``` html
<div edit-field="taxonomy(category)" data-object="post_obj">
	<select multiple name="category" data-object="post_obj.tax_input">
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

------

__tags_input__ : *text input*

HTML View:
  
``` html
<div edit-field="tags_input" data-input="input-text" data-object="post_obj">
	<input name="tags_input" data-object="post_obj.tags_input" value="tag1,tag2,tag3">
</div>
```

Model Output:

``` javascript
post_obj = {
	...,
	'tags_input' : [ 'tag1', 'tag2', 'tag3' ]
}

```

------

__link_format__ : *text input ('link_url' field)*
- Keep a watch on __link_url__ field

Values : 
- Value : __video__
  - If `link_url` contains :  
    youtube/  
    youtu.be/  
    vimeo.com/  
    hulu.com/  
    ustream.com/  
    dailymotion.com/  
    ted.com/  
    dotsub.com/  
    blip.tv/  
- Value : __audio__  
  - If `link_url` contains :    
    mixcloud.com/  
    soundcloud.com/  
    rdio.com/  
- Value : __link__  
  - Other value  
- Value : __standard__  
  - *Empty*

------

__QUERY FILTERS__ : Query filters condition a model for submission to `pw_query()`  

------

__tax_query__ : *input - select / multiple select*
- Conditions the input select effect on the attributed model for __tax_query__ field on `pw_query()`  
- [WP Query Taxonomy Parameters](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters)

HTML View

``` html
<div edit-field="taxonomy(topic)" data-object="query_obj">
	<select multiple name="category" data-object="query_obj.tax_query">
		<option value="term1" selected>Term One</option>
		<option value="term2">Term Two</option>
		<option value="term3" selected>Term Three</option>
		<option value="term4">Term Four</option>
	</select>
</div>

<div edit-field="taxonomy(section)" data-object="query_obj">
	<select multiple name="category" data-object="query_obj.tax_query">
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
- __Status__ : In development (phongmedia) // October 13, 2013

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
- The size of an select field

__data-value__ : *string* (optional)
- The over-ride value of the field

__data-maxlength__ : *integer* (optional)
- The maximum length of the field data

__data-placeholder__ : *string* (optional)
- The __placeholder__ value for an text input box

__data-options__ : *string* (optional)
- Used to define options for select inputs
- The object to use to provide the select options

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
  - When __user_vote__ changes, update the database with `pw_set_post_points()` / `set_comment_points()`
  - Get the updated number of points with `pw_get_post_points()`

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

