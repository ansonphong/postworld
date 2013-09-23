POSTWORLD // Angular / JS Functions
=========


##General Functions


####AngularJS
The Javascript methods for Postworld are build using the AngularJS framework.


###*wp_ajax* ( function, args )
• A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
• Sends 'function' command with args to Wordpress function which has been registered with `wp_ajax_` action hooks
• See : http://codex.wordpress.org/AJAX_in_Plugins 

**return** : JSON encoded DATA response


###*o_embed* ( url, args )
• Uses `wp_oembed_get()` WP function via AJAX
• See : http://codex.wordpress.org/wp_oembed_get 

**return** : Object
