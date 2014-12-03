# Postworld Changelog

### Version 1.6.6
- __Breaking Change:__ Changed __feed__ object convention, `feed_template` value no longer required
    + Feed template is automatically selected based on the current view, with the fallback to 'list'
    + View of 'grid' will automatically use template `templates/feeds/feed-grid.html`
    + __To Fix:__ Make sure feed templates coorospond to their respective view names
    + __To Update:__ All references of the `feed_template` key can be removed

- __Breaking Change:__ Changed modal window template to auto-select based on post type
    + Formerly `templates/modals/modal-view-post.html` now would use `templates/posts/{{post_type}}-modal.html`
    + Fallback for all post types to `post-modal.html`
    + __To Fix:__ 1. Add post-modal template, 2. Add modal to `pwSiteGlobals.post_views`