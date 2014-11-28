## Posts
__php/postworld_posts.php__

------

### pw_get_posts ( *$post_ids, $fields* )
- Run `pw_get_post()` on each of the __$post_ids__ , and return the given fields in an __Array__

#### Parameters:

__$post_ids__ : *Array*
- An array of post IDs

__$fields__ : *Array*
- Corresponds to `pw_get_post()` fields parameter

#### Process:
- Get the feed outline
- Select which posts to preload
- Preload selected posts

__return__ : *Array*
``` php
array(
	array(
		'ID' => 24,
		'post_title' => 'Post Title',
		'post_content' => 'Content of the post.'
		'post_type' => 'post',
		'link_format' => 'standard',
		...
	),
	array(
	...
	)
)
```

#### Requirements:
- `pw_get_post()` PHP Method
- `pw_query()` PHP Method
- `pw_feed_outline()` PHP Method

------

### pw_get_post ( *$post_id, $fields, [$user_id]* )
- Gets data fields for the specified post, including post world meta data and custom sized thumbnails

#### Parameters:
__$post_id__ : *integer*

__$user_id__ : *integer* (optional)
- ID of the currently logged in user, for user-view specific data (points voted, etc)

__$fields__ : *string / Array*
- Options :
  - __'all'__ (default) 
  - __'preview'__ - Returns a basic set of commonly usable fields  
  - __'micro'__ - Returns a basic set of commonly usable fields  
  - __'edit'__ - Returns a basic set of standard fields for editing a post
  - __Array__ - Use any of the following values in an Array :


__WP GET_POST METHOD__ : http://codex.wordpress.org/Function_Reference/get_post 
- __ID__ (default always)
- __post_author__
- __post_date__
- __post_date_gmt__
- __post_title__
- __post_content__
- __post_permalink__
- __post_excerpt__
- __post_path__
- __post_name__
- __post_type__
- __post_status__
- (all __get_post__ return values )

__WORDPRESS__
- __post_permalink__
  - Uses WP `get_post_permalink()` Method

__POSTWORLD__
- __post_points__
- __rank_score__
- __post_class__
- __link_url__
- __link_format__
- __geo_latitude__
- __geo_longitude__
- __event_start__
- __event_end__
- __related_post__
- __post_timestamp__ - UNIX Timestamp from GMT Date

__TAXONOMIES__
- __taxonomy(tax_slug)[fields]__ - Returns taxonomy terms array for the post
  - Usage :  
    `taxonomy(all)`  
    `taxonomy(category)`  
    `taxonomy(category, post_tag)`  
    `taxonomy(category)[slug]`  
    `taxonomy(category,post_tag)[slug,name]`
  - __return__ : *Array* - returns an Associative Array with each term in the given taxonomy
  - __[fields]__ : (optional)(Default : *all*) - The fields to return. If only one, will return flat array.  
  Options : *term_id, name, slug, description, parent, count, taxonomy, url*


__VIEWER SPECIFIC__
- __has_voted__ - number of points the user has voted for this post

__IMAGE__

- Registered Image Sizes
  - __image( registered_image_size_name[ thumbnail / medium / large / full ]  )__  
  example : __image(medium)__ - returns the registered 'medium' image size
 
- Custom Image Sizes
  - __image( handle, width, height, hard_crop )__  
  example : __image(banner,700,100,1)__

__AVATAR__
- Avatar Images -(Supports both Buddypress and Regular Wordpress Avatars)
  - __avatar( handle, size )__
  example : __avatar(small,48)__

__AUTHOR__
- author(ID,display_name,user_nicename,posts_url,user_profile_url)
- __edit_post_link__ // ??
- __user_profile_url__ - *Requires Buddypress*
- __post_author_social__ <<< PHONG

__DATE & TIME__
- __time_ago__ - "(2 minutes ago)" : http://www.devnetwork.net/viewtopic.php?f=50&t=113253 

#### Process:
- Mixed Methods for retrieving data

__return__ : *Array* (requested fields)

``` php
array(
	'ID' => 24,
	'post_title' => 'Post Title',
	'post_content' => 'The post content.',
	'taxonomy' => array(
		'taxonomy_slug' => array( // taxonomy name
			array(
				'term' => 'blue'
				'url' => 'http://.../tags/blue/'
			),
			array(
				'term' => 'red'
				'url' => 'http://.../tags/red/'
			),
	),
	'image' => array(
		'thumbnail' => array( // registered image 'thumbnail'
			'width' => 150,
			'height' => 150,
			'url' => 'http://../image-150.jpg'
		),
		'custom_handle' => array( // custom image
			'width' => 320,
			'height' => 240,
			'url' => 'http://../image-320.jpg'
		),
		'full' => array( // full original image
			'width' => 1280,
			'height' => 1024,
			'url' => 'http://../image.jpg'
		),
	),
	'avatar' => array(
		'small' => array( // handle
			'width' => 24,
			'height' => 24,
			'url' => 'http://../avatar-24.jpg'
		),
		'medium' => array( // handle
			'width' => 48,
			'height' => 48,
			'url' => 'http://../avatar-48.jpg'
		),
	),
	...
)
```

------

### pw_print_post( $vars )
- Injects a post into an HTML template
- Template takes django style markup, using **h2o-php** *by speedmax*

#### Parameters : __$vars__

__post_id__ : *integer* (required)
- The ID of the post

__template__ : *string* (optional)
- The absolute system directory path of the template to inject the post into

__view__ : *string* (optional)
- The name of the registered view template to display the post in
- This field over-rides `template` variables if provided
- Uses `pw_get_post_template` to get the template path

__fields__ : *string/Array* (optional)
- Passed to `pw_get_post()` `fields` parameter
- Define all the fields here which will be accessable in the template

__vars__ : *A_Array* (optional)
- Can contain a series of `key/value` pairs which will be injected into the data object and accessible in the template.
- Accessible by `{{ key.value }}` markup in the template

__js_vars__ : *Array* (optional)
- List the variables to be injected into the Javascript `$window` object for accessibility
- The required value in most cases is : `array('post')` 

#### Example Usage
- In the context of a single post template, ie. `single.php`

```php
// Globalize Post
global $post;

// Social Media Widgets
global $social_settings;
$social_settings['meta']['url'] = get_permalink();

// Post Settings
$post_settings = array(
  'post_id'   =>  $post->ID,
  'template'  =>  pw_get_post_template ( $post->ID, 'full-h2o', 'dir', true ),
  'fields'    => 'all',
  'vars'      =>  array(
    'language'        =>  $pwSiteLanguage,
    'social_widgets'  =>  pw_social_widgets($social_settings)
    ),
  'js_vars' =>  array('post'),
  );

$post_html = pw_print_post( $post_settings );
echo $post_html;

```



------

### pw_insert_post ( $post )
- Extends `wp_insert_post` : http://codex.wordpress.org/Function_Reference/wp_insert_post 
- Include additional Postworld __Post Meta__ fields as inputs

#### Parameters : $post *Array*
- All fields in `wp_insert_post()` Method
- Postworld __Post Meta__ Fields:
  - __post_class__
  - __link_format__
  - __link_url__
  - __author_id__

__return__ :
- *post_id* - If added to the database, otherwise return *WP_Error Object*

------

### pw_update_post ( *$post* ) 
- Extends `wp_update_post()` : http://codex.wordpress.org/Function_Reference/wp_update_post
- Include additional Postworld fields as inputs (see `pw_insert_post()` )

__return__ : *integer*
- The ID of the post if the post is successfully updated in the database. Otherwise returns 0.

------

### pw_save_post ( *$post* ) 

Status : In development... (phongmedia)

#### Description
- Uses both `pw_update_post()` and `pw_insert_post()` as needed
  - When post ID is *not* supplied, use __Insert Post__ : `pw_insert_post()`
  - When post ID is supplied, use __Update Post__ : `pw_update_post()`
- __Images__ - Takes Media Library ID or raw URL to set a thumbnail image
  - When only a URL is supplied, use the URL
  - When only an ID is supplied, use the ID
  - When both are supplied, use the ID
 
#### Process

__WP & PW Fields__
1. Check if there is a Post ID.
2. Check if that Post ID exists
3. Check if the user owns that Post ID, or if they have permissions to edit other's posts
4. Pass the values to the cooroponding function: `pw_update_post()` and `pw_insert_post()`

__Image Meta__
- Use `$thumbnail_id` in priority over `$thumbnail_url` if both are provided


#### Usage

``` php

$post_data = array(
  ///// STANDARD WORDPRESS INPUTS /////
  'ID'             => [ <post id> ] //Are you updating an existing post?
  'menu_order'     => [ <order> ] //If new post is a page, it sets the order in which it should appear in the tabs.
  'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
  'ping_status'    => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
  'post_author'    => [ <user ID> ] //The user ID number of the author.
  'post_content'   => [ <the text of the post> ] //The full text of the post.
  'post_date'      => [ Y-m-d H:i:s ] //The time post was made.
  'post_date_gmt'  => [ Y-m-d H:i:s ] //The time post was made, in GMT.
  'post_excerpt'   => [ <an excerpt> ] //For all your post excerpt needs.
  'post_name'      => [ <the name> ] // The name (slug) for your post
  'post_parent'    => [ <post ID> ] //Sets the parent of the new post.
  'post_password'  => [ ? ] //password for post?
  'post_status'    => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' | 'custom_registered_status' ]
  'post_title'     => [ <the title> ]
  'post_type'      => [ 'post' ] // The name/slug of the post type
  'tags_input'     => [ '<tag>, <tag>, <...>' ] //For tags.
  'to_ping'        => [ ? ] //?
  'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 

  ///// POSTWORLD INPUTS /////
  'link_url'       => [ <URL> ],
  'post_class'     => [ 'author' | 'contributor' ],
  'link_format'    => [ 'standard' | 'video' | 'audio' ]
  'event_start'    => [ integer | UNIX timestamp ]
  'event_end'      => [ integer | UNIX timestamp ]
  'geo_latitude'   => [ number ]
  'geo_longitude'  => [ number ]
  'related_post'   => [ number ]

  ///// IMAGE INPUTS /////
  'thumbnail_url'  => [ <URL> ], // The URL of an image to be imported into the library
  'thumbnail_id'   => [ <ID> ],  // The ID of the item in the media library

);

pw_save_post($post_data);

```

__return__ : *integer* (the ID of the post which was added / updated)

------

### pw_set_post_thumbnail( *$post_id, $image, [$image_meta]* )

#### Description

- Takes URL or integer / ID and sets as post thumbnail
- Optionally insert image title, excerpt, body, etc.

#### Parameters

__$post_id__ : *integer*
- The ID of the post which is having the thumbnail set

__$image__ : *integer / string*
- Options :
  - *integer* - The ID of the media library item to 
  - *string* - The URL of the image which to import into the Media Library and then set


#### Process
- Check if `$image` is an integer or a string
- If it's an image, use `set_post_thumbnail()`
- If it's a URL, import it into the Media Library and then set it with `set_post_thumbnail()`

__return__ :  *integer* (The ID of the item in the Media Library)

------