
# Recursive Term Query
-----

## Input:

1. Initial Query

2. Secondary Query

## Input

```php
pw_recursive_query(
	array(
        'terms' => array(
            'taxonomies'    =>  [array] // Pass to get_terms()
            'args'          =>  [array] // Pass to get_terms()
            ),
        'query'  =>  array(          // Pass to pw_query()
            'fields'    =>  [array]     
            ),
    )
)
```

## Process

- The content of `terms` passes that data into the `get_terms()` function, and recursively queries on that result
- Performs a unique term query for each term provided
- Return the results in a sub object


## Output

```php

{
    terms:[
        {
            term:[
                // Status about the query, number of results, etc
                "term_id": "13",
                "name": "birds",
                "slug": "birds",
                "term_group": "0",
                "term_taxonomy_id": "13",
                "taxonomy": "post_tag",
                "description": "",
                "parent": "0",
                "count": "2",
                "post_count":"2",
                "url": "http://..."
            ],
            posts:[
                // Array of posts
            ]
        }
    ]
}


    

```