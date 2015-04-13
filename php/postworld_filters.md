# Postworld // Filters

## Query Filters
Query filters hook in through the `pw_prepare_query` filter. These are user for high level query modifiers which modify the query in more advanced ways based on user/developer input.

Custom query filters can easily be created by theme developers for theme-specific query variables.

Here is a selection of advanced query keys which hook in through query functions.

### post_parent_from
- Sets the `post_parent` query field
- Value options:
    + `this_post` - Sets value to the current post's ID
    + `this_post_parent` - Sets value to the current post's `post_parent` ID

### exclude_posts_from
- Sets the `post__not_in` query field
- Value options:
    + `this_post_id` - Excludes the current post

### include_posts_from
- Sets the `post__in` query field
- Value options:
    + `this_post_id` - Includes the current post
    + `this_post_parent` - Include's the current post's parent

### author_from
- Sets the `author` query field
- Value options:
    + `this_author` - Queries posts from current post's author









