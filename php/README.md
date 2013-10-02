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
0. [ __Posts__ : postworld_posts.php ](#posts)
0. [ __Feeds__ : postworld_feeds.php ](#feeds)
0. [ __Sharing__ : postworld_share.php ](#sharing)
0. [ __Images__ : postworld_images.php ](#images)

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
	'post_id' => {{integer}}
	'author_id'	=> {{integer}}
	'post_class' => {{string}}
	'post_format' => {{string}}
	'link_url' => {{string}}
	'post_points' => {{integer}}
	'rank_score' => {{integer}}
```

#### Usage:
```php
$post_meta = pw_get_post_meta($post_id);
```

------

### pw_set_post_meta ( *post_id*, *$post_meta* )

#### Description
Used to set Postworld values in the __wp_postworld_post_meta__ table

#### Parameters:
All parameters, except post_id, are optional.

__$post_id__ : *integer* (required)

__$post_meta__ : *Array*
- post_class
- post_format
- link_url

#### Usage:
```php
$post_meta = array(
     'post_class' => string,
     'post_format' => string,
     'link_url' => string
);
pw_set_post_meta($post_id, $post_meta);
```

------

## Points
__/php/postworld-points.php__  
Handles getting and setting points data in the __points__ and __post_meta__ tables.

------

__POST POINTS__

------

### get_post_points( *$post_id* )
Get the total number of points of the given post from the points column in __wp_postworld_post_meta__

__return__ : *integer* (number of points)

------

### calculate_post_points ( *$post_id* )
- Adds up the points from the specified post, stored in __wp_postworld_post_points__
- Stores the result in the points column in __wp_postworld_post_meta__
__return__ : *integer* (number of points)

------

### cache_post_points ( *$post_id* ) 
- Calculates given post's current points with `calculate_post_points()`
- Stores points it in __wp_postworld_post_meta__ table_ in the __post_points__ column

__return__ : *integer* (number of points)

------

### set_post_points( *$post_id, $set_points* )

#### Parameters

__$post_id__ : *integer*

__$set_points__ : *integer*

#### Process

- Check role of current user, and check how many points they can cast from __user_roles__ table as __$vote_points__
  - If __$set_points__ is greater than the user's role __vote_points__ , reduce to __vote_points__

__HINT:__
```php
	if ( abs($vote_points) > abs($set_points) )
		$set_points = $vote_points * (abs($set_points)/$set_points);
```

- Check if row exists in __points__ table for the given __$post_id__ and __$user_id__
  - If __no row__ , add row to __points__ table
  - If __row exists__ , update the row
  - If __$set_points = 0__ , delete row

- Add Unix timestamp to __time__ column in __post_points__ table

- Update cache in __post_meta__ table
  1. If row doesn't exist for given __post_id__ in __post_meta__ table create new row
  2. Update cached __post_points__ row in __post_meta__ table directly if there is a change in points

__HINT:__

```php
$old_user_points = has_voted_on_post($post_id,$user_id); // get previous points user has voted on points
$update_points = $set_points - $old_user_points; // calculate the difference in points

$old_post_points = get_post_points($post_id); // get previous points of the post
$new_post_points = $old_post_points + $update_points; // add the updated points

// Set post_points column in post_meta table to $new_post_points

```

- Update cache in __user_meta__ table
  1. Get value of __post_points_meta__ column in __user_meta__ table
  2. Update the number of points in the __post_points_meta__ object

__HINT:__

``` php
$post_type = get_post_type( $post_id ); // check post_type of given post
$post_points_meta = // Get value of post_points_meta column in user_meta table
$post_points_meta = json_decode( $post_points_meta ); // decode from JSON
$post_type_points = $post_points_meta['post_type'][$post_type]; // Get the number of points in given post_type
$post_points_meta['post_type'][$post_type] = $post_type_points + $update_points; // Add new points
$post_points_meta = json_encode($post_points_meta); // encode back into JSON
// Write new post_points_meta object to user_meta table
```

Anatomy of __post_points_meta__ column JSON object in __user_meta__ table : see *Database Structure* Document.

__return__ : *Array*
``` php
array(
     'points_added' => {{integer}} // (points which were successfully added)
     'points_total' => {{integer}} // (from wp_postworld_meta)
)
```

__TODO:__
- Check that user has not voted too many times recently <<<< Concept method <<< PHONG
  - Use post_points_meta to store points activity << PHONG

------

__USER POST POINTS__

------

### get_user_posts_points ( *$user_id* )
- Get the number of points voted to posts authored by the given user
- Get cached points of user from __wp_postworld_user_meta__ table __post_points__ column

__return__ : *integer* (number of points)

------

### calculate_user_posts_points ( *$user_id* )
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

### cache_user_posts_points ( *$user_id* )
- Runs `calculate_user_posts_points()` Method
- Caches value in __post_points_meta__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

__COMMENT POINTS__

------

### get_user_comments_points ( *$user_id* )

- Get the number of points voted to comments authored by the given user
- Get cached points of user from __wp_postworld_user_meta__ table __comment_points__ column

__return__ : *integer* (number of points)

------

### calculate_user_comments_points ( *$user_id* )
- Adds up the points voted to given user's comments, stored in __wp_postworld_comment_points__ table
- Stores the result in the __post_points__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

### cache_user_comments_points ( *$user_id* )
- Runs `calculate_user_comment_points()` Method
- Caches value in __comment_points__ column in __wp_postworld_user_meta__ table

__return__ : *integer* (number of points)

------

__GENERAL POINTS__

------

### has_voted_on_post ( *$post_id, $user_id* ) 
- Check __wp_postworld_points__ to see if the user has voted on the post
- Return the number of points voted

__return__ : *integer* (number of points voted)

------

### has_voted_on_comment ( *$comment_id, $user_id* ) 
- Check __wp_postworld_comment_points__ to see if the user has voted on the comment
- Return the number of points voted

__return__ : *integer*

------

### get_user_points_voted_to_posts ( *$user_id* ,*$break_down=FALSE*)
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

### get_user_votes_on_posts ( *$user_id* )
- Get all posts which user has voted on from __wp_postworld_post_points__ table 

__return__ : *Object*
``` php
	#for_each
	'post_id' => {{integer}}
	'votes' => {{integer}}
	'time' => {{timestamp}}
```

------

### get_user_votes_report ( *$user_id* )
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

### get_user_vote_power ( *$user_id* )
- Checks to see user's WP roles `with get_user_role()`
- Checks how many points the user's role can cast, from __wp_postworld_user_roles__ table, under __vote_points__ column

__return__ : *integer* (the number of points the user can cast)

------

## Rank Scores
__php/postworld-rank.php__

Contains functions which are used to read and write Rank Scores for posts.  
Rank Scores, in brief, are calculated by an equation using the number of points and comments divided by the age of the post.  
The Rank Score equation also involves several other curves and 'Currents' which help sort posts based on popularity, similar to Reddit.

------

### get_rank_score ( *$post_id, [$method]* )
- Gets the Rank Score of the given post, using `calculate_rank_score()`
- Retrieves from the __rank_score__ column in __wp_postworld_meta__

__return__ : *integer* (Rank Score)

------

### calculate_rank_score ( *$post_id* )
- Calculates Rank Score based on rank equation
- Returns the Rank Score 

__return__ : *integer* (Rank Score)

------

### cache_rank_score ( *$post_id* )
- Calculate rank_score with `calculate_rank_score()` method
- Cache the result in __wp_postworld_meta__ in the __rank_score__ column

__return__ :  *integer* (Rank Score) 

------

## Cron Tasks
__php/postworld_cron.php__

------

### pw_add_intervals($schedules)
- Add intervals by which to perform cron tasks

------

## Caching
__php/postworld_cache.php__

------

### cache_all_points ()
- Runs cache_user_points() and cache_post_points()

__return__ : *cron_logs Object* (add to table wp_postworld_cron_logs)

------

### cache_all_user_points()
- Cycles through all users with cache_user_points() method

__return__ : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_post_points()
- Cycles through each post in each post_type with points enabled
- Calculates and caches each post's current points with cache_post_points() method

__return__ : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_comment_points()
- Cycles through all columns
- Calculates and caches each comment's current points with cache_comment_points() method

__return__ : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_rank_scores ()
- Cycles through each post in each post_type scheduled for Rank Score caching
- Calculates and caches each post's current rank with cache_rank_score() method
__return__ : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_feeds ()
- Run pw_cache_feed() method for each feed registered for feed caching in WP Options
__return__ : *cron_logs* Object (store in table wp_postworld_cron_logs)

------

SHARES

------

### cache_shares ( *[$cache_all]* ) << UNDER CONSTRUCTION << 

#### Description
- Caches user and post share reports

#### Paramaters
__$cache_all__ : *boolean*  
Default : *false*

#### Process
- If `$cache_all = false`, just update the recently changed share reports
  - Check __Cron Logs__ table for the most recent start time of the last `cache_shares()` operation
  - __POSTS :__  
  Get an array of all __post_IDs__ from __Shares__ table which have been updated since the most recent run of `cache_shares()` by checking the __last time__ column  
  Run `cache_post_shares($post_id)` for all recently updated shares
  - __AUTHORS :__  
  Get an array of all __post_author_IDs__ from __Shares__ table  which have been updated since the most recent run of `cache_shares()` by checking the __last time__ column, 
  Run `cache_user_post_shares($user_id)` for all recently updated user's shares
   - __USERS :__  
  Get an array of all __user_IDs__ from __Shares__ table  which have been updated since the most recent run of `cache_shares()` by checking the __last time__ column 
  Run `cache_user_shares($user_id)` for all recently updated user's shares

- If `$cache_all = true`
  - Cycle through every single post and run `cache_post_share_report($post_id)`
  - Cycle through every single user and run `cache_user_share_report($user_id)`

__return__ : *cron_logs* Object (store in table wp_postworld_cron_logs)

------

POST SHARES

------

### calculate_post_shares( *$post_id* )
- Calculates the total number of shares to the given post

#### Process
- Lookup the given __post_id__ in the __Shares__ table
- Add up ( *SUM* ) the total number in __shares__ column attributed to the post

__return__ : *integer* (number of shares)

------

### cache_post_shares( *$post_id* )
- Caches the total number of shares to the given post

#### Process
- Run `calculate_post_shares($post_id)`
- Write the result to the __post_shares__ column in the __Post Meta__ table

__return__ : *integer* (number of shares)

------

USER SHARES

------

### calculate_user_shares( *$post_id, [$mode]* )
- Calculates the total number of shares relating to a given user

#### Parameters
__$post_id__ : *integer*

__$mode__ : *string* (optional)
- Options :
  - __both__ (default) : Return both __incoming__ and __outgoing__ 
  - __incoming__ : Return shares attributed to the user's posts  
  - __outgoing__ : Return shares that the user has initiated

#### Process
- Lookup the given __user_id__ in the __Shares__ table
- Modes :
  - For __incoming__ : Match to __author_id__ column in __Shares__ table 
  - For __outgoing__ : Match to __user_id__ column in __Shares__ table
- Add up *(SUM)* the total number of the __shares__ column attributed to the user, according to `$mode`

__return__ : *Array* (number of shares)

```php
array(
	'incoming' => {{integer}},
	'outgoing' => {{integer}}
	)
```

------

### cache_user_shares( *$user_id, [$mode]* )
- Caches the total number of shares relating to a given user

#### Process
- Run `calculate_post_shares()`
- Update the __post_shares__ column in the __Post Meta__ table

__return__ : *integer* (number of shares)

------

CRON LOGS

------

### clear_cron_logs ( *$timestamp* )
- Count number of rows in wp_postworld_cron_logs (rows_before)
- Deletes all rows which are before the specified timestamp (rows_removed)
- Count number of rows after clearing (rows_after)

__return__ : *Object*
``` php
	'rows_before' => {{integer}}
	'rows_removed' => {{integer}}
	'rows_after' => {{integer}}
```

------

### Objects Anatomy : 

#### cron_logs *Object*
``` php
	'type' => {{feed/post_type}}
	'query_id' => {{feed id / post_type slug}}
	'time_start' => {{timestamp}}
	'time_end' => {{timestamp}}
	'timer' => {{milliseconds}}
	'posts' => {{number of posts}}
	'timer_average' => {{milliseconds}}
	'query_vars' => {{ query_vars Object }}
```

#### query_vars *Object*
``` php
	'post_type' => {{string}}
	'class' => {{string}}
	'format' => {{string}}
	etc...
```

------

## Query
__php/postworld_query.php__

Here are custom querying functions. These greatly expand upon the WP_Query functions, and gives us access to all the wp_postworld database tables.   
Each function effectively also populates a Wordpress query session, so can be used like WP_query, when 

------

### pw_query( *$args, [$return_format]* ) 

####Description:

- Similar to the functionality of [WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query )
- Queries by Postworld data fields
- Additionally sort by __post_points__ & __rank_score__
- Define which fields are returned using `pw_get_posts()` method
- Can determine the __return_format__ as __JSON, PHP Associative Array or WP Post Object__


#### Process:
1. After querying and ordering is finished, if more than IDs are required to return, use `pw_get_posts()` method to return specified fields

__return__ : *ARRAY_A / JSON / WP_Query*

#### Parameters: *$args*

------

__QUERYING :__

------

__post_type__ : *string / Array*
- post_type column in wp_posts
  - __string__ - Return posts with that post_type
  - __Array__ - Return posts in either post_type (IN/OR operator)

__post_format__ : *string / Array*
- post_format column in wp_postworld_post_meta 
  - __string__ - Return posts with that post_type
  - __Array__ - Return posts in either post_type (IN/OR operator) 

__post_class__ : *string / Array*
- post_class column in wp_postworld_post_meta
  - __string__ - Return posts with that post_type
  - __Array* - Return posts in either post_type (IN/OR operator) 

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
- Set return values. Uses pw_get_posts( $post_ids, $fields ) method
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
	'post_format' => 'standard',
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
	'posts_per_page' : '20',
	fields : array('ID','post_title','post_content','post_date'), // See pw_get_post() $fields method
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
    viewed
    favorites
    view_later

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

### set_post_relationship( *$relationship, $post_id, $user_id, $switch* )
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

#### Usage
``` php
	set_post_relationship( 'favorites', '24', '101', true )
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
- If no __$user_id__ is defined, use __wp_get_current_user()__ method to get user ID
- If no __$post_id__ is defined, use `$post->ID` method to get the post ID

------

### set_favorite( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __favorites__
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'favorites', $post_id, $user_id, $switch )
```

__return__ : *boolean*

### set_viewed( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __viewed__
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'viewed', $post_id, $user_id, $switch )
```

__return__ : *boolean*

### set_view_later( *$switch, [$post_id], [$user_id]* )
- Use `set_post_relationship()` to set the post relationship for __view_later__
- __$switch__ is a *boolean*

``` php
	set_post_relationship( 'view_later', $post_id, $user_id, $switch )
```

__return__ : *boolean*

------

__POST RELATIONSHIP : "GET" ALIASES__  
- If no __$user_id__ is defined, use __wp_get_current_user()__ method to get user ID

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
- If no __$user_id__ is defined, use __wp_get_current_user()__ method to get user ID
- If no __$post_id__ is defined, use `$post->ID` method to get the post ID

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
		'post_format' => 'standard',
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
    - ID, post_title, post_excerpt, post_permalink, post_path, post_type, post_date, post_time_ago, comment_count, link_url, 
  - __Array__ - Use any of the following values in an Array :


__WP GET_POST METHOD__ : http://codex.wordpress.org/Function_Reference/get_post 
- __ID__ (default always)
- __post_author__
- __post_date__
- __post_date_gmt__
- __post_title __
- __post_content__
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
- __post_format__
- __post_class__
- __link_url__

__TAXONOMIES__
- __taxonomy(tax_slug)__ - Returns taxonomy terms array for the post ie. *taxonomy(category)*
  - __return__ : *array* - returns an Associative Array with each :


__VIEWER SPECIFIC__
- __user_vote__ - number of points the user has voted for this post

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
- __post_author_name__
- __post_author_link__
- __post_author_website__
- __post_author_description__
- __post_author_nicename__
- __edit_post_link__
- __post_author_social__ <<< PHONG

__DATE & TIME__
- __post_time_ago__ - "(2 minutes ago)" : http://www.devnetwork.net/viewtopic.php?f=50&t=113253 

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

### pw_insert_post ( $post )
- Extends wp_insert_post : http://codex.wordpress.org/Function_Reference/wp_insert_post 
- Include additional Postworld fields as inputs

#### Parameters : $post *Array*
- All fields in `wp_insert_post()` Method
- __post_class__
- __post_format__
- __link_url__
- __external_image__

__return__ :
- *post_id* - If added to the database, otherwise return *WP_Error Object*

------

### pw_update_post ( *$post* ) 
- Extends `wp_update_post()` : http://codex.wordpress.org/Function_Reference/wp_update_post
- Include additional Postworld fields as inputs (see `pw_insert_post()` )


------

## Feeds
__php/postworld_feeds.php__

------

### pw_live_feed ( *$args* )

#### Description:
- Used for custom search querying, etc.
- Does not access *wp_postworld_feeds* caches at all
- Helper function for the `pw_live_feed()` JS method


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
     'feed_id' => {{string}},
     'preload'  => {{integer}}
     'feed_query' => array(
          // pw_query args    
     )
)
$live_feed = pw_live_feed ( *$args* );
```

__return__ : *Object*

``` php
array(
	'feed_id' => {{string}},
	'feed_outline' => '12,356,3564,2362,236',
	'loaded' => '12,356,3564',
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
  2. Enable write_cache

2. Store `$args['feed_query']` in the __feed_query__ column in Postworld __feeds__ table as a JSON Object

3. If write_cache is true, run `pw_cache_feed(feed_id)`

__return__ : __$args__ *Array*

#### Parameters : $args

__feed_id__ : *string*

__feed_query__ : *array*
- default : none
- The query object which is stored in __feed_query__ in __feeds__ table, which is input directly into __pw_query__

__write_cache__ : *boolean*
- If the __feed_id__ is new to the __feeds__ table, set `write_cache = true`
  - false (default) - Wait for cron job to update feed outline later, just update feed_query
  - true - Cache the feed with method : run pw_cache_feed( $feed_id )

#### Usage :
``` php
$args = array (
	'feed_id' => 'front_page_feed',
	'write_cache'  => true,
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

### pw_get_templates ( *$templates_object* )
- Gets an Object of template paths based on the provided object

#### Parameters:

__$templates_object__ : *Array* (optional)

Options:
- *Array* containing __['posts']__ : indicates to return a __Post Templates Object__
  - __post_types__ : *Array* (optional) - Array of post_types which to return template paths for  
    __default__ : Get all registered post types with `get_post_types()` WP Method :  
	`get_post_types( array( array( 'public' => true, '_builtin' => false ) ), 'names' )`

  - __post_views__ : *Array* (optional) - Array of 'feed views' which to retrieve templates for  
    __default__ : `array( 'list', 'detail', 'grid', 'full' )`

- *Array* containing __['panels']__ : indicates to return a __Panel Templates Object__
  - __panel_id__ : Return the url for the given panel_id

- __null__ : *default*  
  Returns object with all panels and templates in the default and over-ride folders.


#### Process:

__POST TEMPLATES OBJECT__

``` php
	if($templates_object['posts']) // If it has a posts object
```

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


__PANEL TEMPLATES OBJECT__

``` php
	if($templates_object['panels']) // If it has a templates object
```

- Default panels template path :  
  __/plugins__/postworld/templates/panels

- Over-ride panels template path:  
  __/theme_name__/postworld/templates/panels


1. Generate a url of the requester panel_id by checking both the Default and Over-ride template folders
  - {{panel_id}}.html  
  Key is __file_name__ without the HTML extension, value is the path relative to base domain
   
2. If file exists in __over-ride__ paths, overwrite the __default__ paths

__return__ : *Array* (with requested template paths)

#### Usage:
``` php
// To get Post Templates Object
$args = array(
	'posts' => array(
		'post_types' => array( 'post', 'link' ),
		'post_views' => array( 'grid', 'list', 'detail', 'full' )
	),
);
$post_templates = pw_get_templates ($args);

// To get Panel Template Object
$panel_template = pw_get_templates ( array( 'panels'=>'panel_id' ));

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

- __Panel Template Object__ : *Array* - With key as panel_id value as panel_url

After JSON Encoded :
``` javascript
{
panels : {
	'feed_top': '/wp-content/plugins/postworld/templates/panels/feed_top.html',
	}
};
```

------

## Sharing
__php/postworld_share.php__

------

### set_share ( *$user_id, $post_id* )

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

### user_share_report ( *$user_id* )

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

### user_posts_share_report ( *$user_id* )

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

### post_share_report ( *$post_id* )

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

