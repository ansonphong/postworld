Postworld // PHP / MySQL Functions
=========

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

### get_post_points( *$post_id* )
Get the total number of points of the given post from the points column in **wp_postworld_post_meta**

**return** : *integer* (number of points)

------

### calculate_post_points ( *$post_id* )
- Adds up the points from the specified post, stored in wp_postworld_post_points
- Stores the result in the points column in wp_postworld_post_meta
**return** : *integer* (number of points)

------

### cache_post_points ( *$post_id* ) 
- Calculates given post's current points with `calculate_post_points()`
- Stores points it in **wp_postworld_post_meta** table in the **post_points** column

**return** : *integer* (number of points)

------

**USER POINTS**

### get_user_posts_points ( *$user_id* )
- Get the number of points voted to posts authored by the given user
- Get cached points of user from wp_postworld_user_meta table post_points column

**return** : *integer* (number of points)

------

### calculate_user_posts_points ( *$user_id* )
- Adds up the points voted to given user's posts, stored in wp_postworld_post_points
- Stores the result in the post_points column in wp_postworld_user_meta

**return** : *integer* (number of points)

------

### cache_user_posts_points ( *$user_id* )
- Runs calculate_user_post_points() Method
- Caches value in post_points column in wp_postworld_user_meta table

**return** : *integer* (number of points)

------

**COMMENT POINTS**

### get_user_comments_points ( *$user_id* )

- Get the number of points voted to comments authored by the given user
- Get cached points of user from wp_postworld_user_meta table comment_points column

**return** : *integer* (number of points)

------

### calculate_user_comments_points ( *$user_id* )
- Adds up the points voted to given user's comments, stored in wp_postworld_comment_points
- Stores the result in the post_points column in wp_postworld_user_meta

**return** : *integer* (number of points)

------

### cache_user_comments_points ( *$user_id* )
- Runs calculate_user_comment_points() Method
- Caches value in comment_points column in wp_postworld_user_meta table

**return** : *integer* (number of points)

------

**GENERAL POINTS**

### set_post_points( *$post_id, $user_id, $add_points* )

#### Process
1. $add_points is an integer
2. Write row in wp_postworld_points
3. Passing 0 deletes row
4. Check that user role has permission to write that many points (wp_options) <<<< HAIDY
5. Check that user has not voted too many times recently <<<< Concept method <<< PHONG
6. Check is the user has already voted points on that post
7. Also update cached points in wp_postworld_meta directly
8. Add Unix timestamp to time column in wp_postworld_points

**return** : *Object*
``` php
     'points_added' => {{integer}} // (points which were successfully added)
     'points_total' => {{integer}} // (from wp_postworld_meta)
```

------

### has_voted_on_post ( *$post_id, $user_id* ) 
- Check wp_postworld_points to see if the user has voted on the post
- Return the number of points voted

**return** : *integer* (number of points voted)

------

### has_voted_on_comment ( *$comment_id, $user_id* ) 
- Check wp_postworld_comment_points to see if the user has voted on the comment
- Return the number of points voted

**return** : *integer*

------

### get_user_points_voted_to_posts ( *$user_id* )
- Get total points voted to posts authored by the given user
- Get points of each post from **wp_postworld_post_meta**
- Add all the points up

**return** : *integer* (number of points)

------

### get_user_votes_on_posts ( *$user_id* )
- Get all posts which user has voted on from wp_postworld_points

**return** : *Object*
```
     #for_each
     post_id : {{integer}}
     votes : {{integer}}
     time : {{timestamp}}
```

------

### get_user_votes_report ( *$user_id* )
#### Description
- Returns the 'recent/active' points activity of the user

#### Process
1. Get all posts which user has recently voted on from wp_postworld_post_points ( total_posts )
2. Add up all points cast (total_points)
3. Generate average (total_points/total_posts) 

**return** : *Object*
```
     total_posts: {{integer}} (number of posts voted on)
     total_points: {{integer}} (number of points cast by up/down votes)
     average_points: {{decimal}} (average number of points per post)
```

------

### get_user_vote_power ( *$user_id* )
- Checks to see user's WP roles with get_user_role()
- Checks how many points the user's role can cast, from wp_postworld_user_roles table, under vote_points column

**return** : *integer* (the number of points the user can cast)
