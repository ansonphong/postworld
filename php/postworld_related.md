# Postworld // Related
A series of functions for retreiving and caching posts related to the current post.

## pw_related_posts( *$vars* )
- Retreives posts related to a specified post

```php
// Default Values
$vars = array(
    'post_id'       =>  $post->ID,
    'number'        =>  10,
    'order_by'      =>  'relevance',
    'query'         =>  array(),
    'related_by'    =>  array(),
    );
```

### `query`
- The `$vars['query']` is both the fallback query, incase the requested number of related posts are not found, as well as the required query variables added to each related_by query.
- Contains any standard `PW_Query` variables

### `related_by`
- The `$vars['realated_by']` value contains an array of objects which query in order until the number of desired related posts has been retreived.


```javascript
[
    {
        type: 'taxonomy',
        taxonomies: [ 'post_tag', 'category' ],
        fields: [           // The fields in order which to search
            'terms',        // Searches in other terms
            'post_title',   // Searches for the term titles in post titles
            'post_excerpt', // Searches for the term titles in post excerpts
            ],
    },
    {
        type: 'fields',
        fields: ['post_title','post_excerpt','post_parent','post_author']
    },
]
```


// Each post_id, store it in an intermediate array, and score the post_ids, every time they come up, add one to the score.





