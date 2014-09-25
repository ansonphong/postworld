# Postworld // PHP / Ajax Functions

Functions in this file are made for the Wordpress Ajax API. They are accessed by Javascript by making calls to `admin-ajax.php`. The AJAX functions are generally used as aliases of internal Wordpress and Postworld functions, with minimal data processing depending on each use-case. The function's purpose is to couple the frontend Javascript data service with the PHP backend.

------

## Function Anatomy

#### Function Name
Each function name is related to the internal function which it's an alias of, generally with the postfix `_ajax`.

#### Function Header
Each function begins with a `list()` function which intakes the request data. This is followed by an unpacking of `$args` into a localized variable `$params`.

#### Function Body
The body of the function generally includes a call to the internal function(s), feeding in relevant variables, such as `$params`.

#### Function Footer
The function is ended with a call to the Postworld AJAX API function `pwAjaxRespond($response_data)` which sends the payload of response data back to the Javascript data service.

#### Example
Here is an example of a standard AJAX function with this format.

```php
function pw_get_template_partial_ajax(){
    list($response, $args, $nonce) = initAjaxResponse();
    $params = $args['args'];

    $response_data = pw_get_template_partial( $params ); 

    pwAjaxRespond( $response_data );
}
```


## Post Function

Each function is followed by it's coorosponding calls to add them as Wordpress actions which are picked up by the Wordpress AJAX API.

The `add_action()` includes 2 parameters:
1. The first parameter is the name of the function as it will be called by Javascript, with a prefix. When an action is accessible to all users including guests, the `wp_ajax_nopriv_` prefix is used. For functions only available to logged in users, the `wp_ajax_` prefix is used.
2. The second parameter is the name of the AJAX function to add on that hook.

#### Example

```php
add_action("wp_ajax_nopriv_pw_get_template_partial", "pw_get_template_partial_ajax");
add_action("wp_ajax_pw_get_template_partial", "pw_get_template_partial_ajax");
```


#### More information

For more information and tips on how the Wordpress AJAX API works, see these resources:

http://codex.wordpress.org/AJAX_in_Plugins

http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/


