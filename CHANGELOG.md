# Postworld Changelog

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