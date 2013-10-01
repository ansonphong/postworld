Postworld // PHP / MySQL Functions
=========

## Index
0. [ **Post Meta** : postworld_meta.php ](#post-meta)
0. [ **Points** : postworld_points.php ](#points)
0. [ **Rank Scores** : postworld_rank.php ](#rank-scores)
0. [ **Cron Tasks** : postworld_cron.php ](#cron-tasks)
0. [ **Caching** : postworld_cache.php ](#caching)
0. [ **Query** : postworld_query.php ](#query)
0. [ **Users** : postworld_users.php ](#users)
0. [ **Posts** : postworld_posts.php ](#posts)
0. [ **Feeds** : postworld_feeds.php ](#feeds)
0. [ **Sharing** : postworld_share.php ](#sharing)
0. [ **Images** : postworld_images.php ](#images)

## Post Meta

**/php/postworld-meta.php**  
Handles getting and setting date in the **post_meta** table.

------

### pw_get_post_meta ( *$post_id* )

#### Description
Used to get Postworld values in the **wp_postworld_post_meta** table

#### Process:
1. Get an Associative Array of all columns in the **wp_postworld_post_meta** table
2. Keys are column names, values are values.

**return** : *Array*
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
Used to set Postworld values in the **wp_postworld_post_meta** table

#### Parameters:
All parameters, except post_id, are optional.

**$post_id** : *integer* (required)

**$post_meta** : *Array*
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



## Points
**/php/postworld-points.php**  
Handles getting and setting points data in the **points** and **post_meta** tables.

------

**POST POINTS**

------

### get_post_points( *$post_id* )
Get the total number of points of the given post from the points column in **wp_postworld_post_meta**

**return** : *integer* (number of points)

------

### calculate_post_points ( *$post_id* )
- Adds up the points from the specified post, stored in **wp_postworld_post_points**
- Stores the result in the points column in **wp_postworld_post_meta**
**return** : *integer* (number of points)

------

### cache_post_points ( *$post_id* ) 
- Calculates given post's current points with `calculate_post_points()`
- Stores points it in **wp_postworld_post_meta** table in the **post_points** column

**return** : *integer* (number of points)

------

### set_post_points( *$post_id, $set_points* )

#### Parameters

**$post_id** : *integer*

**$set_points** : *integer*

#### Process

- Check role of current user, and check how many points they can cast from **user_roles** table as **$vote_points**
  - If **$set_points** is greater than the user's role **vote_points**, reduce to **vote_points**

**HINT:**
```php
	if ( abs($vote_points) > abs($set_points) )
		$set_points = $vote_points * (abs($set_points)/$set_points);
```

- Check if row exists in **points** table for the given **$post_id** and **$user_id**
  - If **no row**, add row to **points** table
  - If **row exists**, update the row
  - If **$set_points = 0**, delete row

- Add Unix timestamp to **time** column in **post_points** table

- Update cache in **post_meta** table
  1. If row doesn't exist for given **post_id** in **post_meta** table create new row
  2. Update cached **post_points** row in **post_meta** table directly if there is a change in points

**HINT:**
```php
$old_user_points = has_voted_on_post($post_id,$user_id); // get previous points user has voted on points
$update_points = $set_points - $old_user_points; // calculate the difference in points

$old_post_points = get_post_points($post_id); // get previous points of the post
$new_post_points = $old_post_points + $update_points; // add the updated points

// Set post_points column in post_meta table to $new_post_points

```

- Update cache in **user_meta** table
  1. Get value of **post_points_meta** column in **user_meta** table
  2. Update the number of points in the **post_points_meta** object

**HINT:**
``` php
$post_type = get_post_type( $post_id ); // check post_type of given post
$post_points_meta = // Get value of post_points_meta column in user_meta table
$post_points_meta = json_decode( $post_points_meta ); // decode from JSON
$post_type_points = $post_points_meta['post_type'][$post_type]; // Get the number of points in given post_type
$post_points_meta['post_type'][$post_type] = $post_type_points + $update_points; // Add new points
$post_points_meta = json_encode($post_points_meta); // encode back into JSON
// Write new post_points_meta object to user_meta table
```

Anatomy of **post_points_meta** column JSON object in **user_meta** table :
``` javascript
{
	post_type : {
		post : 242,
		link : 523,
		blog : 123,
		event : 12
	}
}
```

**return** : *Array*
``` php
array(
     'points_added' => {{integer}} // (points which were successfully added)
     'points_total' => {{integer}} // (from wp_postworld_meta)
)
```

**TODO:**
- Check that user has not voted too many times recently <<<< Concept method <<< PHONG
  - Use post_points_meta to store points activity << PHONG


------

**USER POST POINTS**

------

### get_user_posts_points ( *$user_id* )
- Get the number of points voted to posts authored by the given user
- Get cached points of user from **wp_postworld_user_meta** table **post_points** column

**return** : *integer* (number of points)

------

### calculate_user_posts_points ( *$user_id* )
- Add up the points voted to given user's posts, stored in **wp_postworld_post_points**
- **NEW** : For each post, get the **post_type**, and also calculate value of points given to posts of each **post_type**
- Cache the total result in the **post_points** column in **wp_postworld_user_meta**
- **NEW** : Cache the **post_types** breakdown in the **post_points_meta** column in **wp_postworld_user_meta**

**return** : *Array* (number of points)
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
- Caches value in **post_points** column in **wp_postworld_user_meta** table

**return** : *integer* (number of points)

------

**COMMENT POINTS**

------

### get_user_comments_points ( *$user_id* )

- Get the number of points voted to comments authored by the given user
- Get cached points of user from **wp_postworld_user_meta** table **comment_points** column

**return** : *integer* (number of points)

------

### calculate_user_comments_points ( *$user_id* )
- Adds up the points voted to given user's comments, stored in **wp_postworld_comment_points** table
- Stores the result in the **post_points** column in **wp_postworld_user_meta** table

**return** : *integer* (number of points)

------

### cache_user_comments_points ( *$user_id* )
- Runs `calculate_user_comment_points()` Method
- Caches value in **comment_points** column in **wp_postworld_user_meta** table

**return** : *integer* (number of points)

------

**GENERAL POINTS**

------

### has_voted_on_post ( *$post_id, $user_id* ) 
- Check **wp_postworld_points** to see if the user has voted on the post
- Return the number of points voted

**return** : *integer* (number of points voted)

------

### has_voted_on_comment ( *$comment_id, $user_id* ) 
- Check **wp_postworld_comment_points** to see if the user has voted on the comment
- Return the number of points voted

**return** : *integer*

------

### get_user_points_voted_to_posts ( *$user_id* ,*$break_down=FALSE*)
If **$break_down=False** then : 
- Get total points voted to posts authored by the given user
- Get points of each post from **wp_postworld_post_meta**
- Add all the points up

**return** : *integer* (number of points)

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
- Get all posts which user has voted on from **wp_postworld_post_points** table 

**return** : *Object*
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

**return** : *Object*
``` php
     'total_posts' => {{integer}} //(number of posts voted on)
     'total_points' => {{integer}} //(number of points cast by up/down votes)
     'average_points' => {{decimal}} //(average number of points per post)
```

------

### get_user_vote_power ( *$user_id* )
- Checks to see user's WP roles `with get_user_role()`
- Checks how many points the user's role can cast, from **wp_postworld_user_roles** table, under **vote_points** column

**return** : *integer* (the number of points the user can cast)

------

## Rank Scores
**php/postworld-rank.php**

Contains functions which are used to read and write Rank Scores for posts.  
Rank Scores, in brief, are calculated by an equation using the number of points and comments divided by the age of the post.  
The Rank Score equation also involves several other curves and 'Currents' which help sort posts based on popularity, similar to Reddit.

------

### get_rank_score ( *$post_id, [$method]* )
- Gets the Rank Score of the given post, using `calculate_rank_score()`
- Retrieves from the **rank_score** column in **wp_postworld_meta**

**return** : *integer* (Rank Score)

------

### calculate_rank_score ( *$post_id* )
- Calculates Rank Score based on rank equation
- Returns the Rank Score 

**return** : *integer* (Rank Score)

------

### cache_rank_score ( *$post_id* )
- Calculate rank_score with `calculate_rank_score()` method
- Cache the result in **wp_postworld_meta** in the **rank_score** column

**return** :  *integer* (Rank Score) 

------

## Cron Tasks
**php/postworld_cron.php**

------

### pw_add_intervals($schedules)
- Add intervals by which to perform cron tasks

------

## Caching
**php/postworld_cache.php**

------

### cache_all_points ()
- Runs cache_user_points() and cache_post_points()

**return** : *cron_logs Object* (add to table wp_postworld_cron_logs)

------

### cache_all_user_points()
- Cycles through all users with cache_user_points() method

**return** : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_post_points()
- Cycles through each post in each post_type with points enabled
- Calculates and caches each post's current points with cache_post_points() method

**return** : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_comment_points()
- Cycles through all columns
- Calculates and caches each comment's current points with cache_comment_points() method

**return** : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_rank_scores ()
- Cycles through each post in each post_type scheduled for Rank Score caching
- Calculates and caches each post's current rank with cache_rank_score() method
**return** : *cron_logs* Object (add to table wp_postworld_cron_logs)

------

### cache_all_feeds ()
- Run pw_cache_feed() method for each feed registered for feed caching in WP Options
**return** : *cron_logs* Object (store in table wp_postworld_cron_logs)

------

### clear_cron_logs ( *$timestamp* )
- Count number of rows in wp_postworld_cron_logs (rows_before)
- Deletes all rows which are before the specified timestamp (rows_removed)
- Count number of rows after clearing (rows_after)

**return** : *Object*
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
**php/postworld_query.php**

Here are custom querying functions. These greatly expand upon the WP_Query functions, and gives us access to all the wp_postworld database tables.   
Each function effectively also populates a Wordpress query session, so can be used like WP_query, when 

------

### pw_query( *$args, [$return_format]* ) 

####Description:

- Similar to the functionality of [WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query )
- Queries by Postworld data fields
- Additionally sort by **post_points** & **rank_score**
- Define which fields are returned using `pw_get_posts()` method
- Can determine the **return_format** as **JSON, PHP Associative Array or WP Post Object**


#### Process:
1. After querying and ordering is finished, if more than IDs are required to return, use `pw_get_posts()` method to return specified fields

**return** : *ARRAY_A / JSON / WP_Query*

#### Parameters: *$args*

------

**QUERYING :**

------

**post_type** : *string / Array*
- post_type column in wp_posts
  - **string** - Return posts with that post_type
  - **Array** - Return posts in either post_type (IN/OR operator)

**post_format** : *string / Array*
- post_format column in wp_postworld_post_meta 
  - **string** - Return posts with that post_type
  - **Array** - Return posts in either post_type (IN/OR operator) 

**post_class** : *string / Array*
- post_class column in wp_postworld_post_meta
  - **string** - Return posts with that post_type
  - **Array* - Return posts in either post_type (IN/OR operator) 

**author** : *integer / Array* 
- Use author ID
  - **integer** - Return posts only written by that author
  - **Array** - Return posts written by any of the authors (IN/OR operator) 

**author_name** : *string / Array*
- Use 'user_nicename' in wp_users (NOT 'name')
  - **string** - Return posts only written by that author
  - **Array** - Return posts written by any of the authors (IN/OR operator) 

**year** : *integer*
- 4 digit year (e.g. 2011)
- Return posts within that year

**month** : *integer*
- Month number (from 1 to 12)
- Return posts within that month

**tax_query** : *array*
- Just like [tax_query in WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters)

**s** : *string*
- Search terms
- Query **post_content** & **post_title** columns in **wp_posts** table

------

**ORDERING :**

------

**orderby** : *string*
- Options
  - date (default)
  - rank_score
  - post_points
  - modified
  - rand
  - comment_count

**order** : *string*
- Options
  - DESC (default)
  - ASC

------

**RETURNING :**

------

**offset** : *integer*
- Number of post to displace or pass over. 

**post_count** : *integer*
- Maximum number of posts to return.
  - 0 (default) - Return all

**fields** : *string / Array*
- Set return values. Uses pw_get_posts( $post_ids, $fields ) method
- Pass this directly to `wp_get_posts()` method unless the value is 'ids'
  - **ids** (default) - Return an Array of post IDs
  - **all** - Return all fields
  - **preview** - Return basic fields
  - `array( 'post_title', 'post_content', … )` - Array of fields which to return

**$return_format** : *string*
- Options
  - **WP_QUERY** (default) - Return a [WP_Post Object](http://codex.wordpress.org/Class_Reference/WP_Post )
  - **JSON** - Return a JSON Object
  - **ARRAY_A** - Return an Associative Array


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
- Extends Query fields to Postworld **user_meta** fields

#### Parameters:

**$args** : *Array*

**role** : *string*
- Use 'User Role'

**s** : *string*
- Query : table : wp_users, columns: user_login, user_nicename, user_email, user_url, display_name

**location_country** : *string*
- Query **wp_postworld_user_meta** table column **location_country**

**location_region** : *string*
- Query **wp_postworld_user_meta** table column **location_region** 

**location_city** : *string*
- Query **wp_postworld_user_meta** table column **location_city**

**location** : *string*
- Query **location_country**, **location_city**, and **location_region**

**orderby** : *string*
- Options:
  - **post_points** - Points to the user's posts
  - **comment_points** - Points to user's comments
  - **display_name** - Use Display Name, alphabetical
  - **username** - Use *Nice Name*, alphabetical
  - **date** - Date joined

**order** : *string*
- Options : 
  - **ASC** (default)
  - **DESC**

**fields** : *Array*
- Options : 
  - **All** (default)
  - Any fields from `get_userdata()` Method : http://codex.wordpress.org/Function_Reference/get_userdata
  - Any fields from `pw_get_userdata()` Method

**$return_format** : *string*
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
**return** : *ARRAY_A / JSON* (Requested Fields)

------

## Users
**php/postworld_users.php**
Here we have a series of functions which are used to read and write custom user meta data.

------

### pw_get_userdata ( *$user_id, [$fields]* )

#### Description :
- Extends `get_userdata()` WP Method to include meta data from the Postworld **user_meta** table

#### Parameters:
**$user_id** : *integer*

**$fields** : (optional) *string / Array*
- **all** : Default - Return all fields
- Standard **Wordpress** User Fields:
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

- Custom **Postworld** User Fields:
  - viewed
  - favorites
  - location_country
  - location_region
  - location_city
  - post_points
  - post_points_meta
  - comment_points
  - user_role

#### Usage
``` php
$user_data = get_user_data('1', array('viewed', 'favorites', 'location_country'));
```

**return** : *Array* (requested fields)
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
- Include WP method `get_userdata()` fields << PHONG

------

### pw_update_user ( *$userdata* )
- Extends **wp_update_user()** to add data to the Postworld **user_meta** table
- See **wp_update_user()** : http://codex.wordpress.org/Function_Reference/wp_update_user

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

**return** : *integer*
- **user_id** - If successful

------

### set_favorite ( *$post_id, $user_id, $add_remove* )
- Add or remove the given post id, from the array in favourites column in wp_postworld_user_meta of the given user
- Add or remove row in pw_postworld_favorites, with user_id and post_id
- If it was added or removed, add 1 or subtract 1 from table wp_postworld_post_meta  in column favorites

#### Parameters:

**$add_remove**
- Options
  - **1** - add it to favourites
  - **-1** - remove it from favorites

**return** :
- **1**  - added successfully
- **-1** - removed successfully
- **0**  - nothing happened

------

### get_favorites ( *$user_id* )
- Return array from the favourites column in wp_postworld_user_meta of the given user

**return** : *array* (of post ids)

------

### is_favorite ( *$post_id, $user_id* )
- Checks the favorites column in **user_meta** table of the given user to see if the user has set the post as a favorite

**return** : *boolean*

------

### set_viewed ( *$user_id, $post_id, $viewed* )
- Adds to removes to the array in has_viewed in wp_postworld_user_meta 

#### Parameters:

**$viewed** : *boolean*
- **true** - check if the post_id is already in the array. If not, add it.
- **false** - check if the post_id is already in the array. If so, remove it.

**return** : *boolean* (true)

------

### get_viewed ( *$user_id* )
- Gets list of posts by id which the user has viewed
**return** : *Array*

------

### has_viewed ( *$user_id, $post_id* )
- Checks to see if user has viewed a given post
- Values stored in array in has_viewed in wp_postworld_user_meta

**return** : *boolean* 

------

### get_user_location ( $user_id )
- From 'location_' columns in wp_postworld_user_meta

**return** : *Object*
``` php
array(
	'city' => {{city}}
	'region' => {{region}}
	'country' => {{country}}
)
```

------

### get_client_ip ()
**return** : *string* (IP address of the client)

------

### get_user_role ( *$user_id, [$return_array]* )
- Returns user role(s) for the specified user

####Parameters:
**$user_id** : *integer*

**$return_array** : *boolean*
- **false** (default) - Returns a string, with the first listed role
- **true** - Returns an Array with all listed roles

**return** : *string / Array* (set by $return_array)

------

## Posts
**php/postworld_posts.php**

------

### pw_get_posts ( *$post_ids, $fields* )
- Run `pw_get_post()` on each of the **$post_ids**, and return the given fields in an **Array**

#### Parameters:

**$post_ids** : *Array*
- An array of post IDs

**$fields** : *Array*
- Corresponds to `pw_get_post()` fields parameter

#### Process:
- Get the feed outline
- Select which posts to preload
- Preload selected posts

**return** : *Array*
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
**$post_id** : *integer*

**$user_id** : *integer* (optional)
- ID of the currently logged in user, for user-view specific data (points voted, etc)

**$fields** : *string / Array*
- Options :
  - **'all'** (default) 
  - **'preview'** - Returns a basic set of commonly usable fields
    - ID, post_title, post_excerpt, post_permalink, post_path, post_type, post_date, post_time_ago, comment_count, link_url, 
  - **Array** - Use any of the following values in an Array :


**WP GET_POST METHOD** : http://codex.wordpress.org/Function_Reference/get_post 
- **ID** (default always)
- **post_author**
- **post_date**
- **post_date_gmt**
- **post_title **
- **post_content**
- **post_excerpt**
- **post_path**
- **post_name**
- **post_type**
- **post_status**
- (all **get_post** return values )

**WORDPRESS**
- **post_permalink**
  - Uses WP `get_post_permalink()` Method

**POSTWORLD**
- **post_points**
- **rank_score**
- **post_format**
- **post_class**
- **link_url**

**TAXONOMIES**
- **taxonomy(tax_slug)** - Returns taxonomy terms array for the post ie. *taxonomy(category)*
  - **return** : *array* - returns an Associative Array with each :


**VIEWER SPECIFIC**
- **user_vote** - number of points the user has voted for this post

**IMAGE**

- Registered Image Sizes
  - **image( registered_image_size_name[ thumbnail / medium / large / full ]  )**  
  example : **image(medium)** - returns the registered 'medium' image size
 
- Custom Image Sizes
  - **image( handle, width, height, hard_crop )**  
  example : **image(banner,700,100,1)**

**AVATAR**
- Avatar Images -(Supports both Buddypress and Regular Wordpress Avatars)
  - **avatar( handle, size )**
  example : **avatar(small,48)**

**AUTHOR**
- **post_author_name**
- **post_author_link**
- **post_author_website**
- **post_author_description**
- **post_author_nicename**
- **edit_post_link**
- **post_author_social** <<< PHONG

**DATE & TIME**
- **post_time_ago** - "(2 minutes ago)" : http://www.devnetwork.net/viewtopic.php?f=50&t=113253 

#### Process:
- Mixed Methods for retrieving data

**return** : *Array* (requested fields)
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
- **post_class**
- **post_format**
- **link_url**
- **external_image**

**return** :
- *post_id* - If added to the database, otherwise return *WP_Error Object*

------

### pw_update_post ( *$post* ) 
- Extends `wp_update_post()` : http://codex.wordpress.org/Function_Reference/wp_update_post
- Include additional Postworld fields as inputs (see `pw_insert_post()` )


------

## Feeds
**php/postworld_feeds.php**

------

### pw_live_feed ( *$args* )

#### Description:
- Used for custom search querying, etc.
- Does not access *wp_postworld_feeds* caches at all
- Helper function for the `pw_live_feed()` JS method


#### Parameters: $args

**feed_id** : *string*


**preload** : *integer*
- Number of posts to fetch data and return as post_data

**feed_query** : *Array*
- `pw_query()` Query Variables


#### Process:
- Generate return **feed_outline**, with `pw_feed_outline( $args[feed_query] )` method
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

**return** : *Object*
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
- Registers the feed in **feeds** table

#### Process:
1. If the **feed_id** doesn't appear in the **wp_postworld_feeds** table :
  1. Create a new row
  2. Enable write_cache

2. Store `$args['feed_query']` in the **feed_query** column in Postworld **feeds** table as a JSON Object

3. If write_cache is true, run `pw_cache_feed(feed_id)`

**return** : **$args** *Array*

#### Parameters : $args

**feed_id** : *string*

**feed_query** : *array*
- default : none
- The query object which is stored in **feed_query** in **feeds** table, which is input directly into **pw_query**

**write_cache** : *boolean*
- If the **feed_id** is new to the **feeds** table, set `write_cache = true`
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
- Uses `pw_query()` method to generate an array of **post_ids** based on the supplied `$pw_query_args`

#### Parameters:

**$pw_query_args** : *Array*
- `pw_query()` Arguments

#### Process:
- Over-ride `feed_query['fields']` variable to **'id'**
- Flatten `pw_query()` return to Array of **post_ids**

**return** : *Array* (of post IDs)

------

### pw_cache_feed ( *$feed_id* )
- Generates a new feed outline for a registered **feed_id** and caches it

#### Process:
- Run `pw_feed_outline( $args )` on the **args** in **feed_query** column in the row of the given **$feed_id**
- Store as *comma delineated list of IDs* in the **feed_outline** column of *feeds* table

**return** : *Array* (of post IDs) 

------

### pw_get_feed ( *$feed_id, [$preload]* )

#### Parameters:

**$feed_id** : *string*

**$preload** : *integer* (optional)('0' default)
- The number of posts to pre-load with post_data

#### Process:
- Return an object containing all the columns from the wp_postworld_feeds table
- If $preload (integer) is provided, then use `pw_get_posts()` on that number of the first posts in the feed_outline, return in **post_data** Object
- Use fields value from **feed_query** column under key fields 

**return** : *Array*
``` php
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

**$templates_object** : *Array* (optional)

Options:
- *Array* containing **['posts']** : indicates to return a **Post Templates Object**
  - **post_types** : *Array* (optional) - Array of post_types which to return template paths for  
    **default** : Get all registered post types with `get_post_types()` WP Method :  
	`get_post_types( array( array( 'public' => true, '_builtin' => false ) ), 'names' )`

  - **post_views** : *Array* (optional) - Array of 'feed views' which to retrieve templates for  
    **default** : `array( 'list', 'detail', 'grid', 'full' )`

- *Array* containing **['panels']** : indicates to return a **Panel Templates Object**
  - **panel_id** : Return the url for the given panel_id

- **null** : *default*  
  Returns object with all panels and templates in the default and over-ride folders.


#### Process:

**POST TEMPLATES OBJECT**

``` php
	if($templates_object['posts']) // If it has a posts object
```

- **Default** post templates path :  
  **/plugins**/postworld/templates/posts

- **Over-ride** post templates path :  
  **/theme_name**/postworld/templates/posts


1. Generate list of template names :
  - {{post_type}}-{{post_view}}.html  
  post-list.html  
  post-detail.html  
  etc…

2. For each template name, check over-rides path for templates with `file_exists()` PHP Method

3. If template **post_type** doesn't exist, fallback:
  - For **post_type** default to **post**  
  If ( **link-list.html** ) doesn't exist use ( **post-list.html** )

4. If template **post_view** over-ride doesn't exist, fallback to default templates path
  -  For **post_view** default to plugin path  
  If ( **/theme_name**/.../post-list.html ) doesn't exist, use ( **/plugins**/.../post-list.html )

5. Gather all the template files into an object


**PANEL TEMPLATES OBJECT**

``` php
	if($templates_object['panels']) // If it has a templates object
```

- Default panels template path :  
  **/plugins**/postworld/templates/panels

- Over-ride panels template path:  
  **/theme_name**/postworld/templates/panels


1. Generate a url of the requester panel_id by checking both the Default and Over-ride template folders
  - {{panel_id}}.html  
  Key is **file_name** without the HTML extension, value is the path relative to base domain
   
2. If file exists in **over-ride** paths, overwrite the **default** paths

**return** : *Array* (with requested template paths)

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

- **Post Templates Object** : *Array* - With post_views nested within post_types

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

- **Panel Template Object** : *Array* - With key as panel_id value as panel_url

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
**php/postworld_share.php**

------

### set_shared ( *$user_id, $post_id, $ip_address* )
- Check URL query_vars for user id
- See if user id exists
- Get selected post id
- Check IP address against list of IPs stored in recent_ips in wp_postworld_user_shares
- If the IP is not in the list, add to the list and add 1+ to total_views in wp_postworld_user_shares
- If the IP is in the list, do nothing
- If the array length of IPs is over {{100}}, remove old IPs
**return** : *boolean*
- **true** - if added share
- **false** - if no share added

------

### user_share_report ( *$user_id* )
- Lookup all post shared by user id
- In **user_shares** table, return numerically ordered list of post IDs 

- Run get_post_data method on each post ID, for (title, permalink)
**return** : *Array*

``` php
array(
	'id' => 24,
	'title' => post_title,
	'permalink' => 'http://...',
	'views' => 35 // (number of views user has lead to this post)
)
```

------

### post_share_report ( *$post_id* ) <<< PHONG
…

------

## Images
**php/postworld_images.php**

Contains functions for getting registered images, resizing images and post attachment images.

------

