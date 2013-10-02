
------

### cache_user_posts_share_report( *$user_id* )
- Run `generate_user_posts_share_report($user_id)`
- Save the result in __User Meta__ table, __share_report__ column

__return__ : *Array* (same as `generate_user_posts_share_report()`)

------

### cache_user_share_report( *$user_id* )
- Run `generate_user_share_report($user_id)` and save the result in __User Meta__ table, __share_report__ column

__return__ : *Array* (same as `generate_user_share_report()`)



------

CONDENSE BOTH INTO ONE FUNCTION FOR LOAD

------

### load_user_share_report( *$user_id, [$real_time]* )
- Loads the user share report

#### Parameters

__$user_id__ : *integer*
- ID of the user of whose share report is being loaded

__$real_time__ : *boolean*  
Default : *false*

#### Process
- Check value of `$real_time`
  - If __true__ : Load by running `generate_user_share_report()`
  - If __false__ : Load by getting column __share_report__ in table __User Meta__  
  - If the __share_report__ column is empty, run `cache_user_share_report()` and return the result

__return__ : *Array* (same as `generate_user_share_report()`)  
See : Database Structure on __share_report__ column in __User Meta__ table



------

### cache_post_share_report ( *$post_id* )

- Run `post_share_report()`
- Store the result in __post_shares_meta__ column in __Post Meta__ table

__return__ : *Array* (same as `post_share_report()`)

