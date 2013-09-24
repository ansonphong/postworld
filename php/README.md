Postworld // PHP / MySQL Functions
=========

## META FUNCTIONS

### pw_get_post_meta( *$post_id* )

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

------

### pw_set_post_meta ( *$post_meta* )

#### Description
Used to set Postworld values in the **wp_postworld_post_meta**

#### Parameters:
All parameters, except post_id, are optional.

**$post_meta** : *Array*
- post_id (required)
- post_class
- post_format
- link_url

#### Usage:
```php
$post_meta = array(
     'post_id' => integer,
     'post_class' => string,
     'post_format' => string,
     'link_url' => string
);
```