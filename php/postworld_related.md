# Postworld // Related
A series of functions for retreiving and caching posts related to the current post.

## pw_related_posts( *$vars* )
- Retreives posts related to a specified post

```php
// Default Values
$vars = array(
    'post_id'       =>  $post->ID,
    'number'        =>  10,
    'depth'         =>  1000,
    'order_by'      =>  'relevance',
    'query'         =>  array(),
    'related_by'    =>  array(),
    );
```

__Parameters: *$vars*__

### `query`
- The fallback query, incase the requested number of related posts are not found, as well as the required query variables added to each `related_by` clause query.
- Contains any standard `PW_Query` variables

### `related_by`
- Contains an array of objects which represent related query clauses

```javascript
[
    ///// TAXONOMY CLAUSE /////
    {
        type: 'taxonomy',
        weight: 2,
        taxonomies: [
            {
                taxonomy: 'post_tag',
                weight: 1.5
            },
            {
                taxonomy: 'category',
                weight: 1
            }
        ]
    },
    ///// FIELD CLAUSE /////
    {
        type: 'field',
        weight: 1,
        fields: [
            {
                field: 'post_title',
                weight: 2
            },
            {
                field: 'post_excerpt',
                weight: 1.5
            },
            {
                field: 'post_parent',
                weight: 1.25
            },
            {
                field: 'post_author',
                weight: 1.5
            },
        ]
    },
]
```

#### Related By Clauses
- There is no limit to how many clauses are listed
- Each clause in the array is executed in the order it is listed
- All clauses are executed, and each time a post is found to relate to muliple clauses or elements within the clause, it is given a score value

#### Types of Clauses
- __taxonomy__ - Queries for posts with related terms
- __fields__ - Queries for posts with related contents in the specified fields


// Each post_id, store it in an intermediate array, and score the post_ids, every time they come up, add one to the score.





