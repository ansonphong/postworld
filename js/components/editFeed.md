Postworld // Edit Feed Directive
=========

## editFeed ( edit-feed ) *Isolated Scope*

The Edit Feed directive is made for modifying and updating the query of a live feed. It does this by interacting directly with the feed data in the `pwData` service, inside the `feeds[feedId]` object.

The directive plays well with `ng-options`, and any other `ng-model` modifying directives.

### Example Usage : Search Field
This is an example of a usage of the Edit Feed modifying the search keyword.

```html
<input
    type="text"
    placeholder="Search Keyword"
    edit-feed="search_results"
    feed-key="query.s"
    feed-reload="enter"
    ng-model="feed.query.s">
```

The attributes include:

#### edit-feed *(required)*
- This defines the `feedId` of the feed which is being modified

#### feed-key *(optional)*
- This indicates the sub-key inside that feed which is being modified
- Supported primary keys are `view` and `query`

#### feed-reload *(optional)*
- This indicates how the feed reloads
- Options:
    + __enter__ - The feed is updated when the element is focused and enter is pressed.
    + __click__ - The feed is updated when the element is clicked. This is useful for creating submit buttons which don't even include an ng-model
    + *Undefined* - If no feed-reload method is defined, the default method is to reload when the ng-model is changed

#### ng-model *(optional)*
- If any data is being passed to the feed, this is required field. This represents the local scope model where the data is orginating.


### Example Usage : Taxonomies



