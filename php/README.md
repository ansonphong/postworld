Postworld // PHP / MySQL Functions
=========

## Index
0. [ __Post Meta__ : postworld_meta.php ](#post-meta)
0. [ __Points__ : postworld_points.php ](#points)
0. [ __Rank Scores__ : postworld_rank.php ](#rank-scores)
0. [ __Cron Tasks__ : postworld_cron.php ](#cron-tasks)
0. [ __Caching__ : postworld_cache.php ](#caching)
0. [ __Query__ : postworld_query.php ](#query)
0. [ __Users__ : postworld_users.php ](#users)
0. [ __Comments__ : postworld_comments.php ](#comments)
0. [ __Feeds__ : postworld_feeds.php ](#feeds)
0. [ __Sharing__ : postworld_share.php ](#sharing)
0. [ __Images__ : postworld_images.php ](#images)
0. [ __Taxonomies__ : postworld_taxonomies.php ](#taxonomies)
0. [ __Utilities__ : postworld_utilities.php ](#utilities)
0. [ __Wizard__ : postworld_wizard.php ](#wizard)


## Post Meta

__/php/postworld-meta.php__  
Handles getting and setting date in the __post_meta__ table.

------

### pw_get_post_meta ( *$post_id* )

#### Description
Used to get Postworld values in the __wp_postworld_post_meta__ table

#### Process:
1. Get an Associative Array of all columns in the __wp_postworld_post_meta__ table
2. Keys are column names, values are values.

__return__ : *Array*
```php
	array(
		'post_id' => {{integer}}
		'author_id'	=> {{integer}}
		'post_class' => {{string}}
		'link_format' => {{string}}
		'link_url' => {{string}}
		'post_points' => {{integer}}
		'rank_score' => {{integer}}
		//...
	)
```

#### Usage:
```php
$post_meta = pw_get_post_meta($post_id);
```

------

### pw_set_post_meta ( *$post_id*, *$post_meta* )

#### Description
Used to set Postworld values in the __wp_postworld_post_meta__ table

#### Parameters:
All parameters, except post_id, are optional.

__$post_id__ : *integer* (required)

__$post_meta__ : *Array*
- post_class : *string*
- link_format : *string*
- link_url : *string*
- geo_latitude : *number*
- geo_longitude : *number*
- event_start : *integer*
- event_end : *integer*
- related_post : *integer*


#### Usage:
```php
$post_meta = array(
		 'post_class' => string,
		 'link_format' => string,
		 'link_url' => string
);
pw_set_post_meta($post_id, $post_meta);
```

------

## Points
__/php/postworld-points.php__  
Handles getting and setting points data in the __Post Points__ , __Comment Points__ , __User Meta__ and __Post Meta__ tables.

------

__META POINTS FUNCTIONS__

------

### set_points ( $point_type, $id, $set_points )

#### Description

- A meta function for `pw_set_post_points()` and `pw_set_comment_points()`

#### Parameters

__$point_type__ : *string*
- Which type of points to set
- Options :
	- __post__ - Will set points for a *post_id*
	- __comment__ - Will set points for *comment_id*

__$id__ : *integer*
- The __post_id__ or __comment_id__

__$set_points__ : *integer*
- How many points to set for the user

#### Process

- Get the User ID
- Get the user's vote power : `pw_get_user_vote_power()`
	- If __$set_points__ is greater than the user's role __vote_points__ , reduce to __vote_points__

- Define the table and column names to work with :
	- __Points Table__ : *post_points / comment_points*
	- __ID Column__ : *post_id / comment_id*
	- __Points Column__ : *post_points / comment_points*

- Check if row exists in __Points Table__ for the given __ID Column__ and __User ID__
	- If __no row__ , add row to coorosponding __Points Table__
	- If __row exists__ , update the row
	- If __$set_points = 0__ , delete row

- Add Unix timestamp to __time__ column in __Points Table__

- If `$point_type == post` , Update cache in __Post Meta__ table
	1. If row doesn't exist for given __post_id__ in __Post Meta__ table, create new row
	2. Update cached __post_points__ row in __Post Meta__ table directly if there is a change in points

- Update cache in __User Meta__ table, under the post/comment author, under coorosponding __Points Column__
	1. `$point_type == post` update the value of __post_points_meta__ column in __user_meta__ table


Anatomy of __post_points_meta__ column JSON object in __user_meta__ table : see *Database Structure* Document.

__return__ : *Array*

``` php
array(
		 'point_type' => {{$point_type}} // (post/comment) << NEW
		 'user_id' => {{$user_id}} // (user ID) << NEW
		 'id' => {{$id}} // (post/comment ID) << NEW

		 'points_added' => {{integer}} // (points which were successfully added)
		 'points_total' => {{integer}} // (from wp_postworld_meta)
)
```

__TODO:__
- Check that user has not voted too many times recently <<<< Concept method <<< PHONG
	- Use post_points_meta to store points activity << PHONG


------

__POST POINTS__

------

### pw_get_post_points( *$post_id* )
- Get the total number of points of the given post from the __post_points__ column in the __Post Meta__ table

__return__ : *integer* (number of points)

------

### pw_calculate_post_points ( *$post_id* )
- Adds up the points from the specified post, stored in __Post Points__ table

__return__ : *integer* (number of points)

------

### pw_cache_post_points ( *$post_id* ) 
- Calculates given post's current points with `pw_calculate_post_points()`
- Stores the result in the __post_points__ column in __Post Meta__ table

__return__ : *integer* (number of points)

------

### pw_set_post_points( *$post_id, $set_points* )

#### Description
- Wrapper for `pw_set_points()` Method for setting post points
- Run `pw_cache_rank_score()` to update rank score of the post

#### Parameters

__$post_id__ : *integer*

__$set_points__ : *integer*

#### Process

- Run `pw_set_points( 'post', $post_id, $set_points )`

__return__ : *Array* (same as `pw_set_points()` )

------

### pw_has_voted_on_post ( *$post_id, $user_id* ) 
- Check __Post Points__ to see if the user has voted on the post
- Return the number of points voted

__return__ : *integer* (number of points voted)

------

__COMMENT POINTS__

------

### pw_get_comment_points ( $comment_id )
- Get the total number of points of the given comment from the __comment_points__ column in the __Comment Meta__ table

__return__ : *integer* (number of points)

------

### pw_calculate_comment_points ( $comment_id )
- Adds up the points from the specified comment, stored in __Comment Points__ table

__return__ : *integer* (number of points)

------

### pw_cache_comment_points ( $comment_id )
- Calculates given post's current points with `pw_calculate_comment_points()`
- Stores the result in the __comment_points__ column in __Comment Meta__ table

__return__ : *integer* (number of points)

------

### set_comment_points( $comment_id, $set_points )

#### Description
- Wrapper for `pw_set_points()` Method for setting comment points

#### Parameters

__$comment_id__ : *integer*

__$set_points__ : *integer*

#### Process

- Run `pw_set_points( 'comment', $post_id, $set_points )`

__return__ : *Array* (same as `pw_set_points()` )

------

### pw_has_voted_on_comment ( $comment_id, $user_id )
- Check __Comment Points__ table to see if the user has voted on the comment
- Return the number of points voted

__return__ : *integer* (number of points voted)

------

__USER POST POINTS__

------

### pw_get_user_post_points ( *$user_id* )
- Get the number of points voted to posts authored by the given user
- Get cached points of user from __wp_postworld_user_meta__ table __post_points__ column

__return__ : *integer* (number of points)

------

### pw_get_user_post_points_meta
- Get the number of points voted to posts authored by the given user, broken down
- Get cached points of user from __wp_postworld_user_meta__ table __post_points_meta__ column
- Include total points

__return__ : *Array*

```php
array(
	'total' => 640,
	'post_type' => array(
		'post' => 160,
		'link' => 325,
		'blog' => 65,
		'event' => 90
	)
)
```
------


### pw_calculate_user_posts_points ( *$user_id* )
- Add up the points voted to given user's posts, stored in __wp_postworld_post_points__
- __NEW__ : For each post, get the __post_type,__ and also calculate value of points given to posts of each post type
- Cache the total result in the __post_points__ column in the __User Meta__ table
- __NEW__ : Cache the __post_types__ breakdown in __post_points_meta__ column in the __User Meta__ table

__return__ : *Array* (number of points)

```php
array(
	'total' => 640,
	'post_type'	=> array(
		'post' => 160,
		'link' => 325,
		'blog' => 65,
		'event' => 90
	)
)
```

------

### pw_cache_user_posts_points ( *$user_id* )
- Runs `pw_calculate_user_posts_points()` Method
- Caches value in __post_points_meta__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

__USER COMMENT POINTS__

------

### pw_get_user_comments_points ( *$user_id* )

- Get the number of points voted to comments authored by the given user
- Get cached points of user from __wp_postworld_user_meta__ table __comment_points__ column

__return__ : *integer* (number of points)

------

### pw_calculate_user_comments_points ( *$user_id* )
- Adds up the points voted to given user's comments, stored in __wp_postworld_comment_points__ table
- Stores the result in the __post_points__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

### pw_cache_user_comments_points ( *$user_id* )
- Runs `calculate_user_comment_points()` Method
- Caches value in __comment_points__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

__GENERAL POINTS__

------

### pw_has_voted_on_comment ( *$comment_id, $user_id* ) 
- Check __wp_postworld_comment_points__ to see if the user has voted on the comment
- Return the number of points voted

__return__ : *integer*

------

### pw_get_user_points_voted_to_posts ( *$user_id* ,*$break_down=FALSE*)
If **$break_down=False** then : 
- Get total points voted to posts authored by the given user
- Get points of each post from __wp_postworld_post_meta__
- Add all the points up

__return__ : *integer* (number of points)

If **$break_down=True** then :
Get total points voted to posts authored by the given user grouped by post_type
return *array* 	

for_each post type:
post_id,author_id,total_points,post_type

**output format:**

```php 
[
				 {"post_id":"13","author_id":"1","total_points":"10","post_type":"post"},
				 {"post_id":"19","author_id":"1","total_points":"10","post_type":"link"}
]
```
------

### pw_get_user_votes_on_posts ( *$user_id, [$fields], [$direction]* )
- Get all posts which user has voted on from __wp_postworld_post_points__ table 

#### Parameters
__$user_id__ : *integer*

__$fields__ : *string*
- Which fields to return
- Options:
	- __all__ (default)
	- __post_id__ - If this is set as a string, then return flat array with only `post_id`

__$direction__ : *string*
- Filter the direction by which the user has voted posts
- Options:
	- *null* (Default) - Return all voted posts
	- __up__
	- __down__

__return__ : *Object / Array*


#### Usage

Parameters:

``` php
	pw_get_user_votes_on_posts ( 1, 'post_id', 'up' )
```
Returns:

``` javascript
	[234,235,2341,5,135,3151]
```
Parameters:

``` php
	pw_get_user_votes_on_posts ( 1, 'all', 'up' )
```
Returns:

``` php
	#for_each
	'post_id' => {{integer}}
	'votes' => {{integer}}
	'time' => {{timestamp}}
```

------

### pw_get_user_votes_report ( *$user_id* )
#### Description
- Returns the 'recent/active' points activity of the user

#### Process
1. Get all posts which user has recently voted on from `wp_postworld_post_points ( total_posts )`
2. Add up all points cast (total_points)
3. Generate average (total_points/total_posts) 

__return__ : *Object*
``` php
		 'total_posts' => {{integer}} //(number of posts voted on)
		 'total_points' => {{integer}} //(number of points cast by up/down votes)
		 'average_points' => {{decimal}} //(average number of points per post)
```

------

### pw_get_user_vote_power ( *$user_id* )
- Checks to see user's WP roles `with get_user_role()`
- Checks how many points the user's role can cast, from `$pwSiteGlobals['roles'][ $current_user_role ]['vote_points']`

__return__ : *integer* (the number of points the user can cast)

------

## Rank Scores
__php/postworld-rank.php__

Contains functions which are used to read and write Rank Scores for posts.  
Rank Scores, in brief, are calculated by an equation using the number of points and comments divided by the age of the post.  
The Rank Score equation also involves several other curves and 'Currents' which help sort posts based on popularity, similar to Reddit.

------

### pw_get_rank_score ( *$post_id, [$method]* )
- Gets the Rank Score of the given post, using `pw_calculate_rank_score()`
- Retrieves from the __rank_score__ column in __wp_postworld_meta__

__return__ : *integer* (Rank Score)

------

### pw_calculate_rank_score ( *$post_id* )
- Calculates Rank Score based on rank equation
- Returns the Rank Score 

__return__ : *integer* (Rank Score)

------

### pw_cache_rank_score ( *$post_id* )
- Calculate rank_score with `pw_calculate_rank_score()` method
- Cache the result in __wp_postworld_meta__ in the __rank_score__ column

__return__ :  *integer* (Rank Score) 

------

## Cron Tasks
__php/postworld_cron.php__

------

### pw_add_intervals($schedules)
- Add intervals by which to perform cron tasks


------

## Query
__php/postworld_query.php__

Here are custom querying functions. These greatly expand upon the WP_Query functions, and gives us access to all the wp_postworld database tables.   
Each function effectively also populates a Wordpress query session, so can be used like WP_query, when 

------

### pw_query( *$args, [$return_format]* ) 

####Description:

- Extends [WP_Query Class](http://codex.wordpress.org/Class_Reference/WP_Query )
- Adds the ability to queries by Postworld data fields
- Additionally sort by __post_points__ & __rank_score__
- Supports `pw_get_post` Unified Field Model
- Define which fields are returned using `pw_get_posts()` method
- Can determine the __return_format__ as __JSON, PHP Associative Array or WP Post Object__


#### Process:
1. After querying and ordering is finished, if more than IDs are required to return, use `pw_get_posts()` method to return specified fields

__return__ : *ARRAY_A / JSON / WP_Query*

#### Parameters: *$args*

------

__QUERYING :__

------

__event_start__ : *integer* (UNIX Timestamp)
- All events with `event_end` after specified timestamp

__event_end__ : *integer* (UNIX Timestamp)
- All events with `event_start` before specified timestamp

__event_before__ : *integer* (UNIX Timestamp)
- All events with `event_end` before specified timestamp

__event_after__ : *integer* (UNIX Timestamp)
- All events with `event_start` after specified timestamp

__event_filter__ : *string*
- Possible values:
	+ `future` : Get future events, where `event_start` is in the future
	+ `now` : Get events happening now, where current time is in between `event_start` and `event_end`
	+ `past` : Get past events, where `event_end` is past current time


------

__geo_latitude__ : *number*
- A latitute coordinate
- For use with `geo_range`

__geo_longitude__ : *number*
- A longditude coordinate
- For use with `geo_range`

__geo_range__ : *number*
- A number of geographic units to search in prxomity to provided latitute and longitude coordinates
- Both `geo_longitude` and `geo_latitude` fields are required

------

__link_format__ : *string / Array*
- link_format column in wp_postworld_post_meta 
	- __string__ - Return posts with that post_type
	- __Array__ - Return posts in either post_type (IN/OR operator) 

__post_class__ : *string / Array*
- post_class column in wp_postworld_post_meta
	- __string__ - Return posts with that post_type
	- __Array* - Return posts in either post_type (IN/OR operator) 

------

__post_type__ : *string / Array*
- post_type column in wp_posts
	- __string__ - Return posts with that post_type
	- __Array__ - Return posts in either post_type (IN/OR operator)

__author__ : *integer / Array* 
- Use author ID
	- __integer__ - Return posts only written by that author
	- __Array__ - Return posts written by any of the authors (IN/OR operator) 

__author_name__ : *string / Array*
- Use 'user_nicename' in wp_users (NOT 'name')
	- __string__ - Return posts only written by that author
	- __Array__ - Return posts written by any of the authors (IN/OR operator) 

__year__ : *integer*
- 4 digit year (e.g. 2011)
- Return posts within that year

__month__ : *integer*
- Month number (from 1 to 12)
- Return posts within that month

__tax_query__ : *array*
- Just like [tax_query in WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters)

__s__ : *string*
- Search terms
- Query __post_content__ & __post_title__ columns in __wp_posts__ table

------

__ORDERING:__

------

__orderby__ : *string*
- Options
	- date (default)
	- rank_score
	- post_points
	- modified
	- rand
	- comment_count

__order__ : *string*
- Options
	- DESC (default)
	- ASC

------

__RETURNING :__

------

__offset__ : *integer*
- Number of post to displace or pass over. 

__post_count__ : *integer*
- Maximum number of posts to return.
	- 0 (default) - Return all

__fields__ : *string / Array*
- Set return values. Uses `pw_get_posts( $post_ids, $fields )` method
- Pass this directly to `wp_get_posts()` method unless the value is 'ids'
	- __ids__ (default) - Return an Array of post IDs
	- __all__ - Return all fields
	- __preview__ - Return basic fields
	- `array( 'post_title', 'post_content', … )` - Array of fields which to return

__$return_format__ : *string*
- Options
	- __WP_QUERY__ (default) - Return a [WP_Post Object](http://codex.wordpress.org/Class_Reference/WP_Post )
	- __JSON__ - Return a JSON Object
	- __ARRAY_A__ - Return an Associative Array


####Usage:
``` php
$args = array(
	'post_type' => array('post'),
	'year' => '2013',
	'month' => '12',
	'link_format' => 'standard',
	'post_class' => 'editorial',
	'author' => '1',
	'tax_query' => array(
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => 'books'
		)
	),
	's' => 'search string',
	'orderby' => 'rank_score',
	'order' => 'ASC'
	'posts_per_page' => '20',
	'fields' => array('ID','post_title','post_content','post_date'), // See pw_get_post() $fields method
);

$posts = pw_query( $args, 'JSON' );

```

------

### pw_user_query( *$args, [$return_format]* );

#### Description:
- Similar to [WP_User_Query](http://codex.wordpress.org/Class_Reference/WP_User_Query), queries users in wp_users table
- Extends Query fields to Postworld __user_meta__ fields

#### Parameters:

__$args__ : *Array*

__role__ : *string*
- Use 'User Role'

__s__ : *string*
- Query : table : wp_users, columns: user_login, user_nicename, user_email, user_url, display_name

__location_country__ : *string*
- Query __wp_postworld_user_meta__ table column __location_country__

__location_region__ : *string*
- Query __wp_postworld_user_meta__ table column __location_region__ 

__location_city__ : *string*
- Query __wp_postworld_user_meta__ table column __location_city__

__location__ : *string*
- Query __location_country__ , __location_city__ , and __location_region__

__orderby__ : *string*
- Options:
	- __post_points__ - Points to the user's posts
	- __comment_points__ - Points to user's comments
	- __display_name__ - Use Display Name, alphabetical
	- __username__ - Use *Nice Name*, alphabetical
	- __date__ - Date joined

__order__ : *string*
- Options : 
	- __ASC__ (default)
	- __DESC__

__fields__ : *Array*
- Options : 
	- __All__ (default)
	- Any fields from `get_userdata()` Method : http://codex.wordpress.org/Function_Reference/get_userdata
	- Any fields from `pw_get_userdata()` Method

__$return_format__ : *string*
- Options:
	- ARRAY_A (default)
	- JSON

#### Usage:
``` php
$args = array(
		 'location_country' => {{search_terms}}
		 'location_region' => {{search_terms}}
		 'location_city' => {{search_terms}}
		 'location' => {{search_terms}}
		 'role' => {{string}}
		 's' => {{search_terms}}
		 'orderby' => {{string}}
		 'order' => {{string}}
		 'fields' => array(ids) // default ids only // use pw_get_userdata() method
);
$users = pw_user_query( $args, 'JSON' );
```
__return__ : *ARRAY_A / JSON* (Requested Fields)

------

## Users
__php/postworld_users.php__
Here we have a series of functions which are used to read and write custom user meta data.

------

### pw_set_avatar( *$image_object, $user_id* )
- Sets an image/attachment ID has been delegated as the avatar for the given user
- Stored in __Wordpress User Meta__ `wp_usermeta`
	- Key : `pw_avatar`
	- Value : Attachment ID in the Media Library  

#### Parameters

__$image_object__ : *Array* (required)
- __id__ - ID of the attachment in Media Library
- __url__ - URL of image which will be imported into the media library
- __action__ (optional)
	- __delete__ - Deletes the user's avatar

__$user_id__ : *integer* (required)
- ID of the user to set

#### Usage

```php
	pw_set_avatar( array( "id"=>1, [ "action" => "delete" ], [ "url" => "http://...jpg/gif/png" ] ), $user_id );
```

__return__  
- On success
	- __Array__ - Image Meta Data &  URLs
	- `return['id']` is ID 
- On Error returns error object

------

### pw_get_avatar( *$obj* )
- Gets an image URL or object for a user

#### Parameters : $obj

__user_id__ : *integer* (required)

__size__ : *integer* (optional)
- The size (in pixels) to return the avatar (square)


#### Usage

``` php
// Retuns Image URL
$avatar_image = pw_get_avatar( array("user_id"=>"1", "size"=>256) );

// Returns Image Object
$avatar_object = pw_get_avatar( array("user_id"=>"1") );

```

``` javascript
var avatar_object = {
	 "width":640,
	 "height":480,
	 "file":"2013\/11\/IMAGE.jpg",
	 "file_url":"http:\/\/localhost:8888\/wp-content\/uploads\/2013\/11\/IMAGE.jpg",
	 "sizes":{
			"thumbnail":{
				 "file":"IMAGE-150x150.jpg",
				 "width":150,
				 "height":150,
				 "mime-type":"image\/jpeg"
			},
			"medium":{
				 "file":"IMAGE-275x300.jpg",
				 "width":275,
				 "height":300,
				 "mime-type":"image\/jpeg"
			}
	 },
	 "image_meta":{
			"aperture":0,
			"credit":"",
			"camera":"",
			"caption":"",
			"created_timestamp":0,
			"copyright":"",
			"focal_length":0,
			"iso":0,
			"shutter_speed":0,
			"title":""
	 }
}
```

------

### pw_get_avatar_sizes( $user_id, $fields )

- Takes input $fields in the following format
	- `avatar(handle,size)`

__return__ : *Array* (of image objects)

```
$fields = array( 'avatar(small,48)', 'avatar(medium, 150)' );
$avatars_object = pw_get_avatar_sizes( $user_id, $fields );

//RESULT
$avatars_object = array(
	'small'=>array( "width"=>48, "height"=>48, "url"=>"http://...jpg" ),
	'medium'=>array( "width"=>150, "height"=>150, "url"=>"http://...jpg" )
	)
```

------

### pw_get_userdatas( $user_ids, $fields )

- Wrapper for `pw_get_userdata()`
- `$user_ids` is a flat array of user_ids

__return__ : *Array* (of userdata objects)

------


### pw_get_userdata ( *$user_id, [$fields]* )

#### Description :
- Extends `get_userdata()` WP Method to include meta data from the Postworld __user_meta__ table

#### Parameters:
__$user_id__ : *integer*

__$fields__ : (optional) *string / Array*
- __all__ : Default - Return all fields
- Standard __Wordpress__ User Fields:
	- user_login
	- user_nicename
	- user_email
	- user_url
	- user_registered
	- display_name
	- user_firstname
	- user_lastname
	- nickname
	- user_description
	- wp_capabilities
	- admin_color
	- closedpostboxes_page
	- primary_blog
	- rich_editing
	- source_domain
	- roles
	- capabilities

- Custom __Postworld__ User Fields:
	- avatar(small,48) - *( handle, dimensions )*
	- post_points
	- post_points_meta
	- comment_points
	- share_points
	- share_points_meta
	- post_votes
	- comment_votes
	- location_country
	- location_region
	- location_city
	- post_relationships :  
		• viewed  
		• favorites  
		• view_later  

- __Buddypress__ User Fields:
	- user_profile_url

#### Usage
``` php
$user_data = get_user_data('1', array('viewed', 'favorites', 'location_country'));
```

__return__ : *Array* (requested fields)
```php
array(
	'ID'=> '1',
	'viewed' => '23,14,24,51,27,15',
	'favorites' => '23,24,27',
	'location_country' => 'Egypt',
	... 
)
```

#### TODO :
- Include WP method `get_userdata()` fields << TODO : PHONG

------

### pw_update_user ( *$userdata* )
- Extends __wp_update_user()__ to add data to the Postworld __user_meta__ table
- See __wp_update_user()__ : http://codex.wordpress.org/Function_Reference/wp_update_user

#### Usage
``` php
	$userdata = array(
		'ID' => 1,
		'user_url' => 'http://...com',
		'user_description' => 'Description here.',
		'favorites' => '23,24,27',
		'location_country' => 'Egypt',
	);
```

__return__ : *integer*
- __user_id__ - If successful

------

__USER / POST RELATIONSHIPS__

------

### set_post_relationship( *$relationship, $switch, $post_id, $user_id* )
- Used to set a given user's relationship to a given post 

### Parameters
__$relationship__ : *string*
- The type of relationship to set
- __Options__ :
	- viewed
	- favorites
	- view_later

__$post_id__ : *integer*

__$user_id__ : *integer*

__$switch__ : *boolean*
- *true* : Add the post_id to the relationship array
- *false* : Remove the post_id from the relationship array

### Process
- Add/remove the given __post_id__ to the given relationship array in __post_relationships__ column in __User Meta__ table

- __Favorites__
	- If `$relationship == favorite` : Add / remove a row to __Favorites__ table

#### Usage
``` php
	set_post_relationship( 'favorites', true, '24', '101' )
```

#### Anatomy
- __JSON__ in __post_relationships__ column in __User Meta__ table

``` javascript
{
		viewed:[12,25,23,16,47,24,58,112,462,78,234,25,128],
		favorites:[12,16,25],
		read_later:[58,78],
}
```

__return__ : *boolean*
- *true* - If successful set on
- *false* - If successful set off
- *error* - If error

------

### get_post_relationship( *$relationship, $post_id, $user_id* )
- Used to get a given user's relationship to a given post 

### Parameters
__$relationship__ : *string*
- The type of relationship to set
- __Options__ :
	- all
	- viewed
	- favorites
	- view_later

__$post_id__ : *integer*

__$user_id__ : *integer*

### Process
- Check to see if the __post_id__ is in the given relationship array in the __post_relationships__ column in __User Meta__ table

__return__ : *boolean*
- If `$relationship = all` : return an *Array* containing all the relationships it's in

``` php
	array('viewed','favorites')
```
------

### get_post_relationships( *$user_id, [$relationship]* )
- Used to get a list of all post relationships of a specified user

#### Paramaters

__$user_id__ : *integer*

__$relationship__ : *integer* (optional)


#### Process
- Reads the specified relationship *Array* from __post_relationships__ column in __User Meta__ table
- If relationship is undefined, return entire __post_relationships__ object
- Decode from stored JSON, return PHP Array

#### Usage

Specified post relationship :

``` php
	get_post_relationships( '1', 'favorites' )
```
__returns__ : *Array* of post IDs

``` php
	array(24,48,128,256,512)	
```

Un-specified post relationship :

``` php
	get_post_relationships( '1' )
```
__returns__ : Contents of __post_relationships__

``` php
array(
	'viewed' => [12,25,23,16,47,24,58,112,462,78,234,25,128],
	'favorites' => [12,16,25],
	'view_later' => [58,78]
	)
```

------

__POST RELATIONSHIP : "SET" ALIASES__

__$post_id__ : *integer* (optional)
- If undefined, get the current __post_id__ like:
	
	``` php
	global $post;
	$post_id = $post->ID;
	```

__$user_id__ : *integer* (optional)
- If undefined, use: `$user_id = get_current_user_id();`

------

### set_favorite( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __favorites__
- If __$post_id__ is undefined
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'favorites', $switch, $post_id, $user_id )
```

__return__ : *boolean*

### set_viewed( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __viewed__
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'viewed', $switch, $post_id, $user_id )
```

__return__ : *boolean*

### set_view_later( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __view_later__
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'view_later', $switch, $post_id, $user_id )
```

__return__ : *boolean*

------

__POST RELATIONSHIP : "GET" ALIASES__  

__$user_id__ : *integer* (optional)
- If undefined, use: `$user_id = get_current_user_id();`

------

### get_favorites ( *[$user_id]* )
- Use `get_post_relationships()` method to return just the __favorite__ posts

```php
	get_post_relationships($user_id, 'favorites')
```

__return__ : *Array* (of post ids)

------

### get_viewed ( *[$user_id]* )
- Use `get_post_relationships()` method to return just the __viewed__ posts

```php
	get_post_relationships($user_id, 'viewed')
```

__return__ : *Array* (of post ids)

------

### get_view_later ( *[$user_id]* )
- Use `get_post_relationships()` method to return just the __view later__ posts

```php
	get_post_relationships($user_id, 'view_later')
```

__return__ : *Array* (of post ids)

------

__POST RELATIONSHIP : "IS" ALIASES__

__$post_id__ : *integer* (optional)
- If undefined, get the current __post_id__ like:
	
	``` php
	global $post;
	$post_id = $post->ID;
	```

__$user_id__ : *integer* (optional)
- If undefined, use: `$user_id = get_current_user_id();`

------

### is_favorite( *[$post_id], [$user_id]* )
- Use `get_post_relationship()` method to return the post relationship status for __favorites__

``` php
get_post_relationship( 'favorites', $post_id, $user_id )
```

__return__ : *boolean*

------

### is_viewed( *[$post_id], [$user_id]* )
- Use `get_post_relationship()` method to return the post relationship status for __viewed__

``` php
get_post_relationship( 'viewed', $post_id, $user_id )
```

__return__ : *boolean*

------

### is_view_later( *[$post_id], [$user_id]* )
- Use `get_post_relationship()` method to return the post relationship status for __view_later__

``` php
get_post_relationship( 'view_later', $post_id, $user_id )
```

__return__ : *boolean*

------

### is_post_relationship( *$post_relationship, [$post_id], [$user_id]* )
- Use `get_post_relationship()` method to return the post relationship status for the specified post relationship

``` php
	get_post_relationship( $post_relationship, $post_id, $user_id )
```

__return__ : *boolean*

------

__USER LOCATION__

------

### get_user_location ( $user_id )
- From 'location_' columns in wp_postworld_user_meta

__return__ : *Object*
``` php
array(
	'city' => {{city}}
	'region' => {{region}}
	'country' => {{country}}
)
```

------

### get_client_ip ()
__return__ : *string* (IP address of the client)

------

### get_user_role ( *$user_id, [$return_array]* )
- Returns user role(s) for the specified user

####Parameters:
__$user_id__ : *integer*

__$return_array__ : *boolean*
- __false__ (default) - Returns a string, with the first listed role
- __true__ - Returns an Array with all listed roles

__return__ : *string / Array* (set by $return_array)

------

### pw_count_user_posts( *$author_id* )
- Gets the number of posts published by the given user id

__return__ : *integer*

------


## Comments
__php/postworld_comments.php__

------

### pw_get_comment ( *$comment_id, $fields, $viewer_user_id* )
- Gets data for a particular comment

#### Parameters

__$comment_id__
- The ID of the comment

__$fields__ : *Array*
- __Worpress Comment Fields__ : All return fields from [WP get_comment()](http://codex.wordpress.org/Function_Reference/get_comment)
	- comment_ID
	- comment_post_ID
	- comment_author
	- comment_author_email
	- comment_author_url
	- comment_author_IP
	- comment_date
	- comment_date_gmt
	- comment_content
	- comment_karma
	- comment_approved
	- comment_agent
	- comment_type
	- comment_parent
	- user_id
- __Postworld Comment Fields__
	- comment_points
	- viewer_voted
	- time_ago

__$viewer_user_id__
- The user ID of a user to return vote data by

__return__ : *Array*

``` php
Array (
	// WORDPRESS FIELDS
	[comment_ID] => 1
	[comment_post_ID] => 1
	[comment_author] =>
	[comment_author_email] =>
	[comment_author_url] =>
	[comment_author_IP] =>
	[comment_date] => 2013-10-19 19:41:02
	[comment_date_gmt] => 2013-10-19 19:41:02
	[comment_content] => Hello universe.
	[comment_approved] => 1
	[comment_agent] =>
	[comment_type] =>
	[comment_parent] => 0
	[user_id] => 1
	
	// POSTWORLD FIELDS
	[comment_points] => 0
	[user_voted] => 0
	[time_ago] => 1 second ago
	)
```

------

### pw_get_comments ( *$query, [$fields, $tree]* )
- Gets data for queries comments

#### Parameters

__$query__ : *Associative Array*
- Same as Wordpress __get_comments__ Parameters : [Function Reference / get_comments](http://codex.wordpress.org/Function_Reference/get_comments#Parameters)

__$fields__ : *Array*
- Comment data fields to return
- Same as __pw_get_comment__ fields

__$tree__ : *boolean*
- Default : __true__
- Whether or not to return the comments in a hierarchical structure

#### Process
- Submit `$query` to Wordpress `get_comments()` function
	- Preserve the selected fields
- If there are custom Postworld Comment fields defined, get their values
- If `$tree == true` organize them into a hierarchical structure with `tree_obj()` function

__return__ : *Array* (of comments)

------

### pw_save_comment( *$comment_data, [$return]* )

#### Description
- If `comment_ID` parameter is supplied (and comment exists), use `wp_update_comment`
	- Otherwise use `wp_insert_comment()`
- Includes various security measures

#### Parameters

__$comment_data__ : *Array A*
- Fields:
	- comment_post_ID
	- comment_content
	- comment_type
	- comment_parent
	- user_id
	- comment_author_IP
	- comment_agent
	- comment_date_gmt
	- comment_date
	- comment_approved

__$return__ : *string*
- How to return on a successful add
- Options:
	- __data__ (default) - Return with the post object from `pw_get_comment()`
	- __id__ - Return just the ID of the new / updated comment


#### Usage

__New Comment:__

``` php
$comment_data = array(
	'comment_content' => "Hello world.",
	'comment_post_ID' => 27,
	'comment_parent' => 0,
	);

pw_save_comment($comment_data,'id');
```
__return__ : *integer* (New / Updated Comment ID)
- When `$return = 'id'`


__Edit Comment:__

``` php
$comment_data = array(
	'comment_ID' => 1,
	'comment_content' => "Hello universe.",
	);

pw_save_comment($comment_data,'data');
```

__return__ : *Array A* (New / Updated Comment Data)
- When `$return = 'data'`
- See `pw_get_comment()` Return

------

## Feeds
__php/postworld_feeds.php__

------


### pw_live_feed ( *$vars* )
- Used to insert a `live-feed` or `load-feed` feed
- Prints the `<script>` and `html` tags for a feed

#### Parameters : *$vars*

__feed_id__ : *string* (optional)
- Unique identifier for the feed (must be unique)
- By default, a unique ID hash is generated for each feed

__element__ : *string* (optional)
- Which element the feed is wrapped in
- *Default* : `div`

__directive__ : *string* (optional)
- Which directive to use to insert the feed
- Options:
	+ `live-feed` (default)
	+ `load-feed`

__class__ : *string* (optional)
- Any classes to be added to the feed element
- *Default* : `feed`

__attributes__ : *string* (optional)
- Any additional HTML attributes to be added to the feed element
- *Example* : `id="myFeed" title="My Feed"`

__echo__ : *boolean* (optional)
- If `true`, this will echo the feed
- If `false`, the function will return a string containing the feed JS + HTML
- *Default*: `true`

__aux_template__ : *string* (optional)
- String value is the php feed ID
- Will print the feed with the attributed feed template from the `feeds` template subdir
- Useful for printing hidden hard HTML feeds to suppliment for SEO

__feed__ : *array* (optional)
- Feed values for the Angular directive



#### Simple Example
- Here is an example of a typical usage
```php
$feed_vars = array(
	'feed'	=>	array(
		'feed_template'	=>	'feed-grid',
		'aux_template'	=>	'seo-list',
		'view'	=>	array(
			'current' 	=> 'grid',
			),
		'query' 		=> array(
			'post_status'		=>	'publish',
			'post_type'			=>	'post',
			'fields'			=>	'preview',
			'posts_per_page'	=>	200,
			),
		),
	);
pw_live_feed( $feed_vars );
```

#### Full Example
- Here is an example shown with all the default settings in place
```php
$feed_vars = array(
	'feed_id'		=>	'myFeed',
	'element'		=>	'div',
	'directive'		=>	'live-feed',
	'class'			=>	'feed',
	'attributes'	=>	'',
	'echo'			=>	true,
	'feed'	=>	array(
		'preload'			=>	10,
		'load_increment' 	=> 	10,
		'offset'			=>	0,
		'order_by'			=>	'-post_date',
		'view'	=>	array(
			'current' 	=> 'list',
			'options'	=>	array( 'list', 'grid' ),
			),
		'query' 		=> array(
			'post_status'		=>	'publish',
			'post_type'			=>	'post',
			'fields'			=>	'preview',
			'posts_per_page'	=>	200,
			),
		'feed_template'	=>	null,	// The ID of a feed in /views/feeds/
		),
	);
pw_live_feed( $feed_vars );
```



------


### pw_get_live_feed ( *$args* )

#### Description:
- Used for custom search querying, etc.
- Does not access *wp_postworld_feeds* caches
- Helper function for the `pw_get_live_feed()` JS method


#### Parameters: $args

__feed_id__ : *string*


__preload__ : *integer*
- Number of posts to fetch data and return as post_data

__feed_query__ : *Array*
- `pw_query()` Query Variables


#### Process:
- Generate return __feed_outline__ , with `pw_feed_outline( $args[feed_query] )` method
- Generate return post data by running the defined preload number of the first posts through
`pw_get_posts( feed_outline, $args['feed_query']['fields'] )`


#### Usage:
``` php
$args = array (
		 'feed_id'        => 'myFeed',
		 'preload'        => 10,
		 'query' => array(
					// pw_query args    
		 )
)
$live_feed = pw_get_live_feed ( *$args* );
```

__return__ : *Object*

``` php
array(
	'feed_id' => {{string}},
	'feed_outline' => '12,356,3564,2362,236',
	'preload' => {{integer}},
	'post_data' => array(), // Output from pw_get_posts() based on feed_query
)
```

------

### pw_register_feed ( *$args* )

#### Description:
- Registers the feed in __feeds__ table

#### Process:
1. If the __feed_id__ doesn't appear in the __wp_postworld_feeds__ table :
	1. Create a new row
	2. Enable `write_cache = true`

2. Store `$args['feed_query']` in the __feed_query__ column in Postworld __Feeds__ table as a JSON Object

3. If write_cache is true, run `pw_cache_feed(feed_id)`

__return__ : __$args__ *Array*

#### Parameters : $args

__feed_id__ : *string* (required)
- The id of the feed by which is it loaded

__feed_query__ : *array* (required)
- The query object which is stored in __feed_query__ in __feeds__ table, which is input directly into __pw_query__

__write_cache__ : *boolean* (optional)
- If the __feed_id__ is new to the __feeds__ table, set `write_cache = true`
	- **false** (default) - Wait for cron job to update feed outline later, just update feed_query
	- **true** - Cache the feed outline with method : run `pw_cache_feed( $feed_id )`

#### Usage :
``` php
$args = array (
	'feed_id' => 'front_page_feed',
	'write_cache'  => false,
	'feed_query' => array(
		// pw_query() $args    
	)
);
pw_register_feed ($args);
```

------

### pw_feed_outline ( *$pw_query_args* )
- Uses `pw_query()` method to generate an array of __post_ids__ based on the supplied `$pw_query_args`

#### Parameters:

__$pw_query_args__ : *Array*
- `pw_query()` Arguments

#### Process:
- Over-ride `feed_query['fields']` variable to __'id'__
- Flatten `pw_query()` return to Array of __post_ids__

__return__ : *Array* (of post IDs)

------

### pw_cache_feed ( *$feed_id* )
- Generates a new feed outline for a registered __feed_id__ and caches it
 __return__ : *Array* (number of posts in this feed , feed_query) - to be added to cron_logs


#### Process:
- Run `pw_feed_outline( $args )` on the __args__ in __feed_query__ column in the row of the given __$feed_id__
- Store as *comma delineated list of IDs* in the __feed_outline__ column of *feeds* table

__return__ : *Array* (of post IDs) 

------

### __pw_load_feed__ ( *$feed_id, [$preload]* )

#### Parameters:
__$feed_id__ : *string*

__$preload__ : *integer* (optional)('0' default)
- The number of posts to pre-load with post_data

__$fields__ : *Array* (optional)
- Array of fields to relay to `pw_get_posts()`
- Only used if a number of posts to preload is specified
- Defaults to to 'fields' parameter defined in the feed query

#### Process:
- Return an object containing all the columns from the __Feeds__ table
- If $preload (integer) is provided, then use `pw_get_posts()` on that number of the first posts in the __feed_outline__ , return in __post_data__ Object
- Use fields value from __feed_query__ column under key fields 

__return__ : *Array*

```php
array(
	'feed_id' => {{string}},
	'feed_query' => {{array}},
	'time_start' => {{integer/timestamp}},
	'time_end' => {{integer/timestamp}},
	'timer' => {{milliseconds}},
	'feed_outline' => {{array (of post IDs)}},
	'post_data' => {{array (of post data)}}
)
```

------

### pw_print_feed( *$args* )
- Returns a string with the rendered templates of a given feed

#### Parameters : $args
__feed_id__ : *string*
- Feed ID of the registered Feed to print
- If supplied, `feed_query` is ignored

__feed_query__ : *Array*
- Postworld Query args input directly into `pw_query()`

__posts__ : *array*
- Pass in pre-queried post data

__fields__ : *string/Array* (optional) (default:null)
- Fields to pass to `pw_get_post()`
- Defaults to null, which falls back to fields defined in the registered `feed_query['fields']`
- Overrides `feed_query['fields]`

__view__ : *string* (optional)
- Which Postworld `view` template to use
- Will auto-detect the post type and use the proper template
- If this is not defined, `template` must be defined

__template__ : *string* (optional)
- Absolute path of the template to use (including `.html` extension), relative to posts template path

#### Usage

##### Example with Load Feed from cached `feed_id`
```php
///// PRINT LOAD FEED /////
$print_feed_args = array(
	'feed_id' =>  'features-front_page',
	'posts'   =>  3,
	'fields'  =>  array('ID','post_title', 'post_excerpt','post_permalink'),
	'view'    =>  'list-h2o',
	);
echo pw_print_feed( $print_feed_args );
```

##### Example with Feed Query
```php
///// PRINT LOAD FEED /////
$print_feed_args = array(
	'feed_query'  =>  array(
		'post_type' =>  array('blog'),
		'posts_per_page'  =>  '3',
		'fields'  =>  array('ID','post_title', 'post_excerpt','post_permalink'),
		),
	'view'    =>  'list-h2o',
	);
echo pw_print_feed( $print_feed_args );
```

------

### pw_print_menu_feed( *$vars* )
- Runs `pw_print_feed()` on the posts in a given menu

```php
/*
		$vars = array(
			"menu"    => ""     // Name or ID or slug of menu
			"fields"  => array()  // Fields to pass to pw_get_post
			"view"    => ""   // Which view to render
		)
*/
```

------

### pw_get_post_template ( *$post_id, $post_view, $path_type* )
- Returns an template path based on the provided post ID and view

#### Process
- Check the __post type__ of the post as `$post_type` with `get_post_type( $post_id )`
- Using `pw_get_templates()`, get the template object

#### Parameters
__$post_id__ : *integer*

__$post_view__ : *string*

__$path_type__ : *string* (optional)
- Options:
	- __url__ (default): Returns absolute URL string of template file
	- __dir__ : Returns absolute directory path of template file


#### Usage

``` php
$post_template_url = pw_get_post_template ( $post->ID, 'full', 'url' );
```

__return__ : *string*
- The URL or absolute path of the template
`http://www.com/wp-content/plugins/postworld/templates/posts/post-full.html`

------

### pw_get_panel_template ( *$panel_id, $path_type* )
- Returns a string of the template path based on the provided post ID and view

#### Paramters
__$panel_id__ : *string* (required)
- The ID of the panel, which is the name of the file in the /panels directory, minus the extension

__$path_type__ : *string* (optional)
- The type of path to return
- Options:
	+ __url__ : (default) Absolute URL to the path
	+ __dir__ : Absolute system path, ie. /var/vhosts/www...

__return__ : *string / false*
- The URL or absolute path of the template

------

### pw_get_template ( *$subdir, $panel_id, $ext, $path_type* )
- Gets an Object of template paths based on the provided object
- Searches both the default and override template paths

#### Paramters
__$subdir__ : *string* (required)
- Which subdirectory to search for the template, relative to the default and over-ride template paths

__$panel_id__ : *string* (required)
- The ID of the panel, which is the name of the file in the /panels directory, minus the extension

__$ext__ : *string* (optional)
- The file extension of the template
- Default : __html__

__$path_type__ : *string* (optional)
- The type of path to return
- Options:
	+ __url__ : (default) Absolute URL to the path
	+ __dir__ : Absolute system path, ie. /var/vhosts/www...

__return__ : *string / false*
- The URL or absolute path of the template

------

### pw_get_templates ( *$vars* )
- Gets an Object of template paths based on the provided object
- Searches both the default and override template paths

#### Parameters: __*$vars*__

__subdirs__ : *Array* (optional)
- Which sub-directory(s) to search through
- By default, will search through all sub-directories within the template paths
- ie. `array('posts','panels','comments')`

__posts__ : *Array* (optional)
- Custom filtering for returning post templates
- Options:
	+ __post_types__ : *Array* (optional) - Which post types to return templates for. *Default*: All post types
	+ __post_views__ : *Array* (optional) - Which post views to return templates for. *Default*: All post views registered in `pw-config`

__path_type__ : *string* (optional)
- Options:
	- __url__ (default): Returns absolute URL string of template file
	- __dir__ (default): Returns absolute directory path of template file

__ext__ : *string* (optional)
- The suffix / extension of file type which to search for
- Must not include period / dot before extension ie. `html`, `php`
- Default: `html`

__source__ : *string* (optional)
- The method by which to merge the over-ride templates
- Options:
	+ __merge__ (default) - A custom merge method
	+ __default__ - Default PHP merge method

#### Process:

__POST TEMPLATES OBJECT__

- __Default__ post templates path :  
	__/plugins__/postworld/templates/posts

- __Over-ride__ post templates path :  
	__/theme_name__/postworld/templates/posts


1. Generate list of template names :
	- {{post_type}}-{{post_view}}.html  
	post-list.html  
	post-detail.html  
	etc…

2. For each template name, check over-rides path for templates with `file_exists()` PHP Method

3. If template __post_type__ doesn't exist, fallback:
	- For __post_type__ default to __post__  
	If ( __link-list.html__ ) doesn't exist use ( __post-list.html__ )

4. If template __post_view__ over-ride doesn't exist, fallback to default templates path
	-  For __post_view__ default to plugin path  
	If ( __/theme_name__/.../post-list.html ) doesn't exist, use ( __/plugins__/.../post-list.html )

5. Gather all the template files into an object


__TEMPLATES OBJECTS BY DIRECTORY__

- Default panels template path :  
	__/plugins__/postworld/templates/[panels/comments/modals]

- Over-ride panels template path:  
	__/theme_name__/postworld/templates/[panels/comments/modals]


1. Generate a url of the requester panel_id by checking both the Default and Over-ride template folders
	- {{panel_id}}.html  
	Key is __file_name__ without the HTML extension, value is the path relative to base domain
	 
2. If file exists in __over-ride__ paths, overwrite the __default__ paths

__return__ : *Array* (with requested template paths)


#### Usage:

``` php

// To get Selective Post Templates Object
$args =  array(
	'subdirs' => array( 'posts', 'comments' ),
	'posts'=> array( 
		'post_types' => array('posts', 'pages'),
		'post_views' => array('list','full'),
		)
	);
$post_templates = pw_get_templates ($args);

// To get Panel Templates Object
$panel_template = pw_get_templates ( array( 'subdirs' => array( 'panels' ));

// To get Comments Template Object
$panel_template = pw_get_templates ( array( 'comments' ));

```

#### Return:

- __Post Templates Object__ : *Array* - With post_views nested within post_types

After JSON Encoded :

``` javascript
{
	posts : {
	 'post' : {
			'list' : '/wp-content/plugins/postworld/templates/posts/post-list.html',
			'detail' : '/wp-content/plugins/postworld/templates/posts/post-detail.html',
			'full' : '/wp-content/theme_name/postworld/templates/posts/post-full.html',
			},
	},
};

```

- __Directory Template Object__ : *Array* - With key as __panel_id__ value as __panel_url__

After JSON Encoded :

``` javascript
{
panels : {
	'feed_top': '/wp-content/plugins/postworld/templates/panels/feed_top.html',
	//...
	}
};
```

------

### pw_parse_template( *$template_path, $vars* )
- Parses a PHP template
- Injects the template with the provided `$vars` localized via extract
	+ `extract($vars)`
- Useful for turning parsed template HTML into a variable

#### Parameters
__$template_path__ : *string* (required)
- The absolute directory path the PHP template to parse

__$vars__ : *array* (optional)
- Variables to be injected locally into the template
- `$vars` is unpacked into the template context with PHP's `extract()`
- A value of : `array( 'query'=>array('post_type'=>'post') )` is accessed in the template like `$query['post_type']`

#### Usage 
```php
$vars = array(
	'query' => array(
		'post_type' => $post->post_type // Localized in template as $query['post_type']
		)
	);
$template_html = pw_parse_template( $template_path, $vars );
```

__return__ : *string*
- The parsed template contents

------

## Sharing
__php/postworld_share.php__

------

### pw_set_share ( *$user_id, $post_id* )

#### Description
- Sets a record of a share in __Shares__ table
- __Context__ : The URL leading to the share looks like : 
	- `http://realitysandwich.com/?p=24&u=48`
	- __p__ : The post ID
	- __u__ : The user ID


#### Process
1. Setup
	- Check if user ID exists
	- Check if post ID exists
	- Get the ID of the post author from __Posts__ table 
	- Get the user's IP address with `get_client_ip()`
2. Process IP
	- Check IP address against list of IPs stored in `recent_ips` column in __Shares__ table
	- If the IP is not in the list, add to the list and add 1+ to total_views in wp_postworld_user_shares
	- If the IP is in the list, do nothing
	- If the array length of IPs is over {{100}}, remove old IPs
3. Add Share 
	- If the IP is unique, add one point to the share
	- Update __last_time__ with current GMT UNIX Timestamp

__return__ : *boolean*
- __true__ - if added share
- __false__ - if no share added

------

__SHARE REPORTS__

------

### pw_user_share_report_outgoing ( *$user_id* )

#### Description
- Generate a report of all the shares relating to the current user __by posts that the given user has shared__

#### Process
- Lookup all posts shared by user ID in __User Shares__ table, column __user_id__

__return__ : *Array*

``` php
array(
		array(
				'post_id' => 8723,
				'shares' => 385,
				'last_time' => {{integer}}
			),
		array(
				'post_id' => 3463,
				'shares' => 234,
				'last_time' => {{integer}}
			),
		...

	)
```
------

### pw_user_share_report_meta ( *$user_share_report* )
- Inserts the post object data for each user share


#### Usage : Outgoing
``` php
pw_user_share_report_meta( pw_user_share_report_outgoing( $displayed_user_id ) );
```

#### Output : Outgoing
``` javascript
[
	{
		"post_id":"181217",
		"shares":"1",
		"last_time":"2013-11-11 07:11:21",
		"post":{ "post_title":"title",... } // << Adds this object : output from pw_get_post
	},
	...
]
```

#### Usage : Incoming
``` php
pw_user_share_report_meta( pw_user_share_report_incoming( $displayed_user_id ) );
```

#### Output : Incoming
``` javascript
[
	{
		"post_id":"200047",
		"total_shares":"1",
		"post":{ "post_title":"title",... } // << Adds this object : output from pw_get_post
		"user_shares":[
			{
				"user_id":"1",
				"shares":"1",
				"last_time":"2013-11-11 07:13:54"
				"author":{ "display_name":"Name", ... } // << Adds this object : output from pw_get_userdata
			}
		]
	},
	...
]

```


------

### pw_user_share_report_outgoing ( *$user_id* )

#### Description
- Generate a report of all the shares relating to the current user __by shares to the given user's posts__

#### Process
- Lookup all shared posts owned by the user ID from __User Shares__ table, column __author_id__

__return__ : *Array* 

``` php
array(
		array(
				'post_id' => 9348,
				'total_shares' => 1385,
				'users_shares' => array( 
						array(
								'user_id' => 843,
								'shares' => 235,
								'last_time' => {{integer}}
							),
						array(
								'user_id' => 733,
								'shares' => 345,
								'last_time' => {{integer}}
							),
						...
					)
			),
		array(
				'post_id' => 623,
				'total_shares' => 4523,
				'users_shares' => array( 
						array(
								'user_id' => 633,
								'shares' => 785,
								'last_time' => {{integer}}
							),
						array(
								'user_id' => 124,
								'shares' => 573,
								'last_time' => {{integer}}
							),
						...
					)
			),
	)
```

------

### pw_post_share_report ( *$post_id* )

- Generate a report of all the shares relating to the current post

#### Process
- Collect data from __Shares__ table on the given post 

__return__ : *Array*

``` php
array(
	array(
		'user_id' => '12',
		'shares' => '434',
		'last_time' => {{integer}}
		),
	array(
		'user_id' => '53',
		'shares' => '34',
		'last_time' => {{integer}}
		),
	...
	)

```

------

## Images
__php/postworld_images.php__

Contains functions for getting registered images, resizing images and post attachment images.

------

### pw_url_to_media_library( *$image_url* )
- Uploads a remote image URL to the local Media Library

__return__ : *integer* (Attachment ID)

------

## Taxonomies
__php/postworld_taxonomies.php__

Contains functions for working with Taxonomies.

------

### pw_query_terms( *$args* )
- Queries for and returns selected post terms

##### Parameters : $args

__search__ : *string* (required)
- The search term to match

__taxonomy__ : *string* (required)
- The slug of the taxonomy to search for terms in


------

### taxonomies_outline ( *[$taxonomies], [$max_depth], [$fields]* )

#### Description
- Generate a heirarchical object outlining the requested Taxonomies
- Wrapper for `wp_tree_obj()` Method customized for taxonomies

#### Parameters
__$taxonomies__ : *string/Array* (optional) 
- Options
	- __all__ (default) - Returns all hierarchical public taxonomies
	- *Array* - Array of taxonomy names which to receive a term outline for

__$max_depth__ : *integer* (optional)
- Default : __2__
- The maximum depth of children to parse

__$fields__ : *Array* (optional)
- Default : *all*
- Options :
	- term_id
	- name
	- slug
	- description
	- parent
	- count
	- taxonomy
	- url

#### Usage
``` php
	$toplevel_category_outline = taxonomies_outline( array('category'), 1 );
```

#### Return
```php
array(
	'topics' => array( // Taxonomy
		'label' => 'Topics',
		'terms' => array(
			array(
				'term' => 'Eco', // Term
				'slug' => 'eco',
				'term_id' => '1',
				'url' => {{string}},
				'description' => {{string}},
				'terms' => array(
					array(
						'term' => 'Environmental', // Sub-term
						'slug' => 'environment',
						'term_id' => '3',
						'url' => {{string}},
						'description' => {{string}},
						),
					array(
						'term' => 'Global',
						'slug' => 'global',
						'term_id' => '4',
						'url' => {{string}},
						'description' => {{string}},
						),
					...
					)
				),
			array(
				'term' => 'Tech',
				'slug' => 'tech',
				'term_id' => '2',
				'url' => {{string}},
				'description' => {{string}},
				),
		),
	'sections' => array(
		...
		)
)
```
#### Todo

- Add option to limit/extend fields ($fields parameter)
	- Include toggle for 'capabilities, url, description...'

------

### pw_insert_terms ( *$terms_array, [$input_format],[$force_slugs]* )
- Inserts an array of terms into the DB from an Array or JSON Object

#### Parameters
__$terms_array__ : *(JSON)string/Array*
- Structure is up to two levels deep
- __First level__ key is the name of the taxonomy.
- __Second Level__ Object is key:value array with slug and name of term, optionally contains an object __children__ with terms which will have their parent set as the second level slug
- __Third Level__ Array (optional) is key:value pairs of children as `"slug" => "name"`

``` php
$terms = array(
	"taxonomy_slug" => array(
		array(
			"slug" => "the_slug",
			"name" => "The Name",
			"children" => array(
				"the_slug"=>"The Name",
				"the_slug"=>"The Name",
				"the_slug"=>"The Name",
				...
			)
		)
		array(
			"slug" => "the_slug",
			"name" => "The Name",
			"children" => array(
				"the_slug"=>"The Name",
				"the_slug"=>"The Name",
				"the_slug"=>"The Name",
				...
			)
		)
		...
	)
	...
);
```

__$input_format__ : *string*
- Options:
	- __ARRAY_A__ (default)
	- __JSON__

__$force_slugs__ : *boolean*
	- Default : __false__

#### Process
- Uses `wp_insert_term()` - [Wordpress Codex](http://codex.wordpress.org/Function_Reference/wp_insert_term) - to insert a hierarchical array of terms
- Cycle through each level, adding terms
- If a **term** of the same `slug` already exists within __*the same taxonomy*__
	- Do not add the term
	- Update the name of the existing term
	- Continue to add children if any, with the parent of the already existing term
- If a **term*** with the same `slug` already exists within __*a different taxonomy*__
	- If `$force_slugs == true`, change the other slug - appending an incremental number 

__return__ : *true*

#### Usage

```php
$json_terms = "{
		"topic" : [
				{
						slug:"psyche",
						name:"/psyche",
						children:{
								ancient:"Ancient Mysteries",
								astrology:"Astrology",
								consciousness:"Consciousness",
								dreams:"Dreams",
								},
				},
				{
						slug:"arts",
						name:"/arts",
						children:{
								conferences:"Conferences",
								digital_art:"Digital Art",
								world_art:"World Art",
								},
				},
				{
						slug:"body",
						name:"/body",
						children:{
								energy_medicine:"Energy Medicine",
								food_nutrition:"Food & Nutrition",
								healing:"Healing",
								herbalism:"Herbalism",
								},
				},
		],
		'section' : [
				{
						slug:"psychedelic",
						name:"Psychedelic Culture",
				},
				{
						slug:"conscious_convergences",
						name:"Conscious Convergences",
				},
				{
						slug:"psi",
						name:"Psi Frontiers",
				},
		";

pw_insert_terms($json_terms,"JSON", true);

```

------

## Utilities
__php/postworld_utilities.php__

Contains utility helper functions.

------

### postworld_includes()
- Will includes and que all the files neccessary to run Postworld
- Run this in every PHP template where Postworld is used

------

### tree_obj ( *$object, $parent, $depth, $settings* )

#### Parameters :

__$object__ : *Array*
- The flat Array which to process into a hierarchical tree structure

__$parent__ : *integer*
- Default : __0__
- Current operating parent
- Used by recursions

__$depth__ : *integer*
- Default : __0__
- Current operating depth
- Used by recursions

__$settings__ : __Array__

- __fields__ : *Array*
	- Default : `array('name')`
	- The fields which to preserve into the new structure

- __id_key__ : *string*
	- Default : *id*
	- The key which to use to deliniate the ID of an object

- __parent_key__ : *string*
	- Default : *parent*
	- The key which to use to define the parent ID of an object

- __child_key__ : *string*
	- Default : *children*
	- The key under which to nest the children

- __max_depth__ : *integer*
	- Default : *10*
	- The maximum depth of branches to parse

- __callback__ : *string* (optional)
	- The callback helper function which to call while populating the fields

- __callback_array__ : *array* (optional)
	- The localized field values to pass to the callback function
	- Derived directly from __object__ key of the same name
	- Passes live values of the named keys in given order to __callback__ function

#### Usage

``` php
$settings = array(
	'fields' => array('name','id'),
	'id_key' => $id_key,
	'parent_key' => $parent_key,
	'child_key' => $child_key,
	'max_depth' => $max_depth,
	'callback' => $callback,
	'callback_fields' => $callback_fields,
	);

$tree_obj = tree_obj( $object, 0, 0, $settings );
```

#### Input : $object
``` php
$object = array(
	array(
		'name' => 'Blue',
		'id' => 1,
		'parent' => 0
		),
	array(
		'name' => 'Navy Blue',
		'id' => 2,
		'parent' => 1
		),
	array(
		'name' => 'Aqua Marine',
		'id' => 3,
		'parent' => 1
		),
	array(
		'name' => 'Red',
		'id' => 4,
		'parent' => 0
		),
	array(
		'name' => 'Maroon',
		'id' => 5,
		'parent' => 4
		),
	)

```

#### Return

``` php
array(
	array(
		'name' => 'Blue',
		'id' => 1,
		'children' => array(
			array(
				'name' => 'Navy Blue',
				'id' => 2,
				),
			array(
				'name' => 'Aqua Marine',
				'id' => 3,
				),
			)
		),
	
	array(
		'name' => 'Red',
		'id' => 4,
		'children' => array(
			array(
				'name' => 'Maroon',
				'id' => 5,
				),
			)
		),
	)

```

------

### wp_tree_obj ( *$args* )

#### Description
- A wrapper for `tree_obj()` Method for taking WP Objects
- Used to organize __comments__, __posts__ or __terms__ heirarchically

#### Usage

``` php
$args = array(
	'object' => $object // Array or Array of WP Objects,
	'fields' => $fields,
	'id_key' => $id_key,
	'parent_key' => $parent_key,
	'child_key' => $child_key,
	'max_depth' => $max_depth,
	'callback' => $callback,
	'callback_fields' => $callback_fields,
)
$heirarchy = wp_tree_obj( $args );

```
------


### extract_linear_fields ( *$fields_array, $query_string* )

#### Description :
- Extracts nested comma deliniated values starting with `$query_string` from `$fields_array`
- Returns an *Array*


#### Parameters :

__$fields_array__ : *Array*
- The array which you want to search through

__$query_string__ : *string*
- The term which you want the value to match the beginning of

__return__ : *Array*

#### Example / Usage :

``` php
$fields = array(
	"taxonomy(category)",
	"taxonomy(topic,section)",
	"taxonomy(post_tag)"
	);

$linear_fields = json_encode( extract_linear_fields( $fields,'taxonomy' ) );

```

Result :

``` javascript

[ "category", "topic", "section", "post_tag" ]

```

------

### extract_hierarchical_fields ( *$fields_array, $query_string* )

#### Description :
- Extracts nested comma deliniated values starting with `$query_string` from `$fields_array`
- Nests inside each value the fields which are with it in square brackets
- Returns an *Associative Array*


#### Parameters :

__$fields_array__ : *Array*
- The array which you want to search through

__$query_string__ : *string*
- The term which you want the value to match the beginning of

__return__ : *Array*

#### Example / Usage :

``` php
$fields = array(
	"taxonomy(category)[id,name]",
	"taxonomy(topic,section)[id,slug]",
	"taxonomy(post_tag)"
	);

$hierarchical_fields = json_encode( extract_hierarchical_fields( $fields, 'taxonomy' ) );

```

Result :

``` javascript
{
	"category":["id","name"],
	"topic":["id","slug"],
	"section":["id","slug"],
	"post_tag":[""]
}

```


------

## Wizard
__php/postworld_wizard.php__

Contains helper functions for storing and retreiving data for user-specific wizards.

------

### pw_set_wizard_status( *$vars* )
- Sets the status of a wizard in relation to the user
- Inserts the status object 

#### Process
1. If the user doesn't have a `wizard_status` postmeta entry, create one.
2. Insert / overwrite the current wizard sub-object by `wizard_name`

#### Parameters : $vars

__user_id__ : *integer* (optional)
- If no user ID is provided, the current user ID will be used

__wizard_name__ : *string* (required)
- The name of the object to add the value to

__value__ : *JSON string / A_ARRAY* (required)
- The value to be inserted into the specified object

__input_format__ : *string* (optional)
- The format of the value being passed in and set
- Options:
		+ __A_ARRAY__ (default)
		+ __JSON__

__output_format__ : *string* (optional)
- The format of the value being returned
- Options:
		+ __A_ARRAY__ (default)
		+ __JSON__

##### Return : *Array*
- The current full value of the `wizard_status` user meta value

__wizard_Status__ Model:
```javascript

	wizard_status = {
		organizeEvent : {
			active: true,   // If it in in progress
			visible: false, // If it is visible on the sidebar
		},
	};


```


------

### pw_get_wizard_status( *$vars* )
- Gets the current status of a user's progress through a wizard
- Stored in JSON as `wizard_status` in `wp_usermeta`

#### Parameters : $vars

__user_id__ : *string* (optional)
- If no user ID is provided, the current user ID will be used

__wizard_name__ : *string* (optional)
- The name of the wizard to return the status of
- If no `wizard_name` is specified, the entire `wizard_status` object will be returned

__format__ : *string* (optional)
- The format of the value to return
- Options:
		+ __A_ARRAY__ (default)
		+ __JSON__

#### Usage
```php
		$wizard_status = unify_get_wizard_status( array(
				'user_id' => 1,
				'wizard_name' => 'organizerInit',
				) );
```

#### Return
- Return the requested object
- If wizard doesn't exist, or user doesn't have wizard status, or any other error, return `false`


------

### pw_is_wizard_active( *$wizard_name* )
- Reads the value of a Wizard Status object for the logged in user and returns whether the requested `$wizard_name` is currently active

__return__ : *boolean*
- If the requested wizard object has `active:'true'` return __true__. In all other cases return __false__.

------

### pw_active_wizard( [`$user_id`] )
- Return an string of the active wizard for the logged in user

__return__ : *string*
- Return the `wizard_name`
- Return false if no active wizards





