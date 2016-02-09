# Postworld // Changelog

### Version 1.50
- **Breaking Changes** - Comments Refactor
    + Changed JS `var load_comments` to `pw.comments`
- **Breaking Changes** - Renamed `_` service to `$_`
    + Change all instances of dependency injection of `'_'` to `'$_'`
- **Breaking Changes** - Renamed `pwData` service to `$pwData`
    + Change all instances of dependency injection of `'pwData'` to `'$pwData'`

### Version 1.49
- **Breaking Changes**
    + `pwOptionsData` service/factory removed
    + Replace all instances of `$pwOptionsData['options']` with `$pw.optionsMeta` 
- **Breaking Changes**
    + Removed all instances of PHP methods `pw_set_option_obj`, and `pw_get_option_obj` and their respective Javascript counterparts
    + Use `pw_set_option` and `pw_get_option` in it's place

### Version 1.48
- **Breaking Changes** - Comments
    + Changed `load-comments` directive attribute to `pw-comments`

### Version 1.47
- **Possible Breaking Changes**
    + All instances of `$pwSiteGlobals` have been changed to use `pw_config()` to access configured settings, in preparation for elimination of `$pwSiteGlobals` variable altogether.
    + Any themes using `$pwSiteGlobals` need to migrate to the `pw_config($key)` method to access globally configured settings.
    + All instances of `$pwSiteGlobals` variable need to be renamed to `$pw_config`

### Version 1.42
- **Possible Breaking Changes**
    + Structure of `pw_get_post` input params changed to `pw_get_post( $post_id, $fields, $options )`
    + The third param used to be `$viewer_user_id`, which now can be passed via options under the `viewer_user_id` key.
```php
    $options = array(
        'cache' => null,
        'viewer_user_id' => get_current_user_id()
        )
```

### Version 1.4
- **New Core Feature**
    + Refactored the third party system from Blogosphere into the Postworld core module *Comments*.
    + To enable, add `comments` to supported modules, and configure the comments object under `pw.config.comments`.
```
$pw_config['comments'] => array(
    'supported' => array('facebook', 'disqus'),
);
```

### Version 1.32
- **Best Practices**, **Possible Breaking Changes**
    + Each specified view should come with a complimentary post field model
    + This is done by using `pw_register_post_field_model($viewname,$fields)` for each view
    + The feed now requests fields using getPosts based on the field's current view
    + This allows the minimum data fields to be queried per view, potentially increasing page load and server performance  
    + Below is an example of re-purposing an existing field model `preview` and appending the `post_content` field to it, and calling it `full`.
```php
add_action('init','theme_register_field_models');
function theme_register_field_models(){
    $preview_fields = pw_get_post_field_model('preview');
    pw_register_post_field_model('full', array_merge( $preview_fields, array(
        'post_content'
    )));
}
```


### Version 1.26
- **Added features** to site options module, in admin under *Postworld › Site Options*
    + Option to disable XMLRPC API site-wide
    + Option to require login
    + Option to set image memory limit
    + Option to disble WP Emojicons

### Version 1.21
- __Breaking Change__ - CONFIG - `postworld-config.php`
    + Added manditory 'widgets' module if widgets are being used
    + Add 'widgets' to supported and required widgets arrays
    + Include which widgets are available under `pwSiteGlobals.widgets` key
```
    'widgets' => array(
        'labels' => array(
            'prefix' => '(ThemeName)'
            ),
        'supported' => array(
            'related_posts',
            'term_feed',
            'feed',
            'menu_kit',
            'module',
            'user'
            ),
        ),    
```


### Version 1.16
- __Breaking Change__ - CONFIG - `postworld-config.php`
    + Changed format of `post_views` key
    + Old format example: `array('list','detail','grid','modal','full')`
    + New format example:
    ```
        array(
            'supported' => array('list','detail','grid','modal','full'),
            'options' => array(
                'feeds' => array('list','grid','full'),
                'related_posts' => array('list'),
                ),
            'meta' => array(
                'list' => array(
                    'label' => 'List'
                    ),
                // ...
                )
            )
    ```
    + New format allows the definition of a seperate array of views to show on the feed. 


### Version 1.109
- __Breaking Change__ - JAVASCRIPT - `postworld_includes.inject` requires jQuery to be specified if it's to be used, otherwise jQuery is omitted.

To update, add `jquery` string to the inject array when calling `postworld_includes`:
```php
    postworld_includes( array( 'inject' => array( 'jquery' ) ) )
```

### Version 1.107
- __Breaking Change__ - JAVASCRIPT - Changed all instances of `window.pwSiteglobals` to `pw.config`, and imported into Angular service as `$pw.config` 
  + All instances of `$window.pwSiteglobals` in theme Javascript must be changed to `$pw.config`

### Version 1.106
- __Breaking Change__ - Moved `postworld/infinite/less/ghost.less` to `postworld/less/ghost.less` - All referrences to previous location in themes must be updated.
- Moved all `postworld/infinite/less/` to `postworld/less/`

### Version 1.105
- __Breaking Change__ - Moved `postworld/infinite/packages/bootstrap` to `postworld/lib/bootstrap` - All referrences to previous location in themes must be updated.

### Version 1.104
- __Performance Enhancement__ - Added template preloading to `pw_feed` PHP method. Now post and feed templates are automatically preloaded via `text/ng-template` script tag.

### Version 1.99
- __Breaking Change__ - Renamed `pw-terms-feed` directive to `pw-term-feed`.

### Version 1.98
- __Breaking Change__ - Refactored `pw-user` and `pw-users` directives.
- __Breaking Change__ - Renamed `pw-user` directive to `pw-current-user`.

### Version 1.97
- __Breaking Change__ - Replaced `load-post` directive with `pw-load-post`

### Version 1.96
- __Depreciated__ : PHP Functions Depreciated
    + `pw_live_feed` ›› becomes ›› `pw_feed`
- Added (but not yet integrated) `WP Prism` Library for code syntax highlighting

### Version 1.95
- Added documentation with `@ngdoc`
- __Depreciated__ : Directives depreciated:
    + `stopPropagationClick` ›› becomes ›› `pwClickStopPropagation`
    + `preventDefaultClick` ›› becomes ›› `pwClickPreventDefault`
- __Depreciated__ : Service Methods depreciated:
    + `pwData.wp_ajax` ›› becomes ›› `pwData.wpAjax`

### Version 1.93
- __Breaking Change__ - Replaced `load-panel` with `pw-include`
    + Change `load-panel="widget"` to `pw-include="panels/widget"` 

### Version 1.87
- __Breaking Change__ - Changed `post.image` model structure
    + Now instead of nesting the image's post (`post_title`, `post_excerpt`) under `post.image.post`, it's not located directly under `post.image`
    + For example, all instances of `post.image.post.post_title` are replaced with `post.image.post_title`

### Version 1.86
- __Breaking Change__ - Changed Sliders template format
    + Template context has changed from a basic include, to an `ob_include`, with all variables being passed through the `$var` array
    + Refactor local templates by changing instances of `$posts` to `$var['posts']` 

### Version 1.85
- Added unified caching system to `pw_get_posts()` method, iconsets and term feeds
    + __5-10x__ PHP execution time performance increase can be expected on pages with feeds or term feeds

### Version 1.84
- Iconsets Module
    + Refactored Postworld Iconsets to allow for custom enabling and disabling of iconsets within the theme
- Restructure Postworld Icons font
    + Changed Postworld Icon name from `icomoon` to `Postworld-Icons`
    + Changed prefix from `icon-` to `pwi-`, since the `icon-` class prefix was having collisions with plugins using *Font Awesome 3*
    + __Breaking Change__ - All instances of `icon-` must be renamed to `pwi-`

### Version 1.81
- Replace `ng-controller="tagsAutocomplete"` and `pw-autocomplete-tags` with `pw-input-tags` directive

### Version 1.80
- Updated to numeric versioning, so use `1.80` format, instead of `1.8.0` when attributing postworld versions
- __Breaking Change__ - All instances of `tagsAutocomplete` controller must be updated to new structure
    + Use directive `pw-autocomplete-tags` in place of controller
    + In `typeahead` directive, use `tag.slug as tag.name for tag in queryTags($viewValue)`
    + In `typeahead-on-select attribute`, use `addTag($item)`
    + `$scope.tags_input_obj` renamed to `$scope.tagsInput`
    + When adding a new tag, use `ng-click='newTag(inputModel)'` where `inputModel` is the `ng-model` of the typeahead input field

### Version 1.7.9
- __Breaking Change__ - Feed Posts object is now a function, not an array, so all instances of feeds must be changed:
    + FROM :  `post in posts`
    + TO :  `post in posts()`
- Addiitonally, all instances of `feed` as a scope variable, have been switched to `feed()` as a function, and so must be updated from, for example, `feed.options...` to `feed().options...`

### Version 1.7.8
- __Breaking Change__ - In themes using bootstrap base styles, require addition of `bootstrap` into theme injectors when calling `postworld_includes`
    + If LESS is also included, a custom build will happen locally
    + If LESS is not included, bootstrap will be used from CDN

### Version 1.7.7
- Refactor Icon Core
    + Require `iconset` in modules
    + Add `iconset` key registry in postworld config
```php
'iconsets'  =>  array(
    'required'  =>  array(
        'icomoon',
        'glyphicon-halflings',
        ),
    ),
```

### Version 1.7.5
- Restructured method of using `postworldAdmin` AngularJS module to Bootstrap metaboxes in WordPress Admin
    + In Admin, `postworldAdmin` is Bootstrapped to the document `onLoad`
    + All admin controllers can build and run controllers within `postworldAdmin` module

### Version 1.7.4
- Added support in Postworld config for `wp_admin.usermeta.contact_methods`
    + Adding fields here, adds additional fields to the user profile
    + Fields are stored using the given keys as the `meta_key` in `wp_usermeta`
Example usage of `wp_admin.usermeta.contact_methods`:
```php
array(
    'twitter'   =>  'Twitter Username',
    'facebook'  =>  'Facebook URL',
    'gplus'     =>  'Google+ URL',
    )
```

- Added support in Postworld config for `wp_admin.usermeta.pw_avatar`.
    + To enable, set value to `true`
    + This will automatically add the postworld avatar image input box to user profiles, and it saves the selected attachment ID under `wp_usermeta.pw_avatar`
    + To change the avatar usermeta key, set the value to `array('meta_key'=>'my_meta_key')`

### Version 1.7.3
- **R.I.P. Postworld Panel Widget** - has now been renamed to **Postworld Module Widget**, and uses `.php` files directly included rather than using angular to `ng-include` files with the extension `.html`
    + __Breaking Change__ : Must rename all instances of `/templates/modules/`, as `/templates/modules` is now used as the seat of module widget options

### Version 1.7.2
- Changed `wp_get_user()` field model, replacing 'buddypress()' with 'xprofile()'
    + Example usage `wp_get_user( $user_id, array('xprofile(all)') )`

### Version 1.7.1
- Changed the structure and timing of Postworld bootup. `postworld_includes()` is now to be run exclusively on the `wp_enqueue_scripts` and `admin_enqueue_scripts` action hooks
- Added a filter to selectively activate and include AngularJS modules, with the `pw_angular_modules` filter. Simply add the string of the AngularJS module to enable to the array, and it will be selectively added and it's dependencies included.

### Version 1.6.6
- __Breaking Change:__ Changed __feed__ object convention, `feed_template` value no longer required
    + Feed template is automatically selected based on the current view, with the fallback to 'list'
    + View of 'grid' will automatically use template `templates/feeds/feed-grid.html`
    + __To Fix:__ Make sure feed templates coorospond to their respective view names
    + __To Update:__ All references of the `feed_template` key can be removed

- __Breaking Change:__ Changed modal window template to auto-select based on post type
    + Formerly `templates/modals/modal-view-post.html` now would use `templates/posts/{{post_type}}-modal.html`
    + Fallback for all post types to `post-modal.html`
    + __To Fix:__ 1. Rename `/templates/modals/modal-view-post.html` to `templates/post/post-modal.html` post-modal template, 2. Add modal to `pwSiteGlobals.post_views`