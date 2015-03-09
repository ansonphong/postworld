# Postworld // Changelog

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