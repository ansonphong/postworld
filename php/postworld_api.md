# Postworld // PHP / API

------

## API : Object Editing

Functions which simplify basic array operations.

------

### pw_get_obj( *$obj, $key* )
- Gets the value of the speficied key within the specified object

This function is made to simplify and standardize basic sub-object value *getting* operations within associative arrays. This function eliminates the need to deep-test the existance of a value's path into an object, bypassing potential errors caused by calls to non-existant sub-objects.

#### Parameters
__$obj__ : *array*
- The object which to get the value from

__$key__ : *string*
- The sub-object whose value is being gotten, specified in the period deliniated format `key.subkey.subsubkey`

__return__ : `mixed` or `false`
- If the specified sub-object does exist within the provided object, that object is directly returned
- If it does not exist, `false` is returned

#### Example

```php
    $myObj = array(
        'key'   =>  array(
            'subkey'    =>  'my value'
            )
        );

    $myValue = pw_get_obj( $myObj, 'key.subkey' );  // Returns 'my value'
    $myValue = pw_get_obj( $myObj, 'key.other' );  // Returns false
```

------

### pw_set_obj( *$obj, $key, $value* )
- Sets the value of the speficied key within the specified object and returns the new object

This function is made to simplify and standardize basic sub-object value *setting* operations within associative arrays. This function eliminates the need to deep-test the existance of the desired value's path into an object, bypassing potential errors caused by setting values to non-existant sub-objects.

#### Parameters
__$obj__ : *array*
- The object in which the value is being set

__$key__ : *string*
- The sub-object being set, specified in the period deliniated format `key.subkey.subsubkey`

__$value__ : *string* / *array*
- The value being set into the object

__return__ : `array`
- The new array with the value set within it


#### Example

```php
    $myObj = array(
        'key'   =>  array(
            'subkey'    =>  'my value'
            )
        );

    $myObj = pw_set_obj( $myObj, 'other.subkey', 'other value' );

    // The new value of $myObj is now:
    array(
        'key'   =>  array(
            'subkey'    =>  'my value'
            ),
        'other' =>  array(
            'subkey'    =>  'other value'
            )
        );

```

------

## API : User Meta

Reads and writes from the `wp_usermeta` table.

------

### pw_set_wp_usermeta( *$vars* )
- Sets meta key for the given user under the given key in the `wp_usermeta` table
- Object values passed in can be passed as PHP objects or Arrays, and they will automatically be converted and stored as JSON

#### Parameters : *$vars*

__meta_key__ : *string* (optional)
- Which meta key to use, stored as the `meta_key` column in `wp_usermeta`
- If no key is given, the default `pw_meta` key is used

__user_id__ : *integer* (optional)
- The user ID with which to associate the user meta
- If no user ID is provided, the current logged-in user ID is used

__sub_key__ : *string* (optional)
- Which subkey to set the value in
- If a sub-key is not specified, it will set the value as a string
- If a sub-key is specified, it will set it into a JSON object stored as a string

__value__ : *mixed* (required)
- The actual value to store in the database
- Strings, arrays, objects are accepted, and encoded into JSON

#### Example
```php
$vars = array(
    'user_id'   =>  1,
    'sub_key'   =>  'siteOptions.colors.background',
    'value'     =>  '@blue',
    'meta_key'  =>  'pw_meta'
    );
pw_set_wp_usermeta( $vars )
```

This will update or create a JSON object string in the `wp_usermeta` table under the `meta_key` : `pw_meta`, with the value:

```javascript
{
    "siteOptions":{
        "colors":{
            "background":"@blue"
        }
    }
}
```

If data previously existed within that JSON object, it will be preserved and not over-written, unless the sub-key was identical.

------

### pw_get_wp_usermeta( *$vars* )
- Gets meta key for the given user under the given key in the `wp_usermeta` table

#### Parameters : *$vars*

__meta_key__ : *string* (optional)
- Which meta key to access, queried by the `meta_key` column in `wp_usermeta`
- If no key is provided, the default `pw_meta` key is used

__user_id__ : *integer* (optional)
- If no user ID is provided, the current logged-in user ID is used

__sub_key__ : *string* (optional)
- Which subkey to retreive the value of
- If a sub-key is not specified, it will return the whole object

#### Example
```php
    $vars = array(
        'user_id'   =>  1,
        'meta_key'  =>  'pw_meta',
        "sub_key"   =>  'siteOptions.colors.background',
        );
    $value = pw_get_wp_usermeta( $vars );
    // Returns the value '@blue' from previous example
```

------

## API : Post Meta

------

### pw_set_wp_postmeta( *$vars* )
- Sets meta key for the given post under the given key in the `wp_postmeta` table
- Object values passed in can be passed as PHP objects or Arrays, and they will automatically be converted and stored as JSON

#### Parameters : *$vars*

__post_id__ : *integer* (optional)
- If no post ID is provided, then the current global `$post` ID will be used

__sub_key__ : *string*  (optional)
- If no subkey is specified, the value will be stored as a string or JSON if it's an array or object

__value__ : *mixed* (required)
- The value which to store

__meta_key__ : *string* (optional)
- Which meta key to access, queried by the `meta_key` column in `wp_postmeta`
- If no key is provided, the default `pw_meta` key is used

#### Example
```php
    $vars = array(
        'user_id'   =>  1,
        'meta_key'  =>  'pw_meta',
        'sub_key'   =>  'postOptions.colors.background',
        'value'     =>  '@orange'
        );
    pw_set_wp_postmeta( $vars );
```

This will update or create a JSON object string in the `wp_postmeta` table under the `meta_key` : `pw_meta`, with the value:

```javascript
{
    "postOptions":{
        "colors":{
            "background":"@orange"
        }
    }
}
```

If data previously existed within that JSON object, it will be preserved and not over-written, unless the sub-key was identical.

------

### pw_get_wp_postmeta( *$vars* )
- Gets meta key for the given user under the given key in the `wp_postmeta` table

#### Parameters : *$vars*

__post_id__ : *integer* (optional)
- If no post ID is provided, then the current global `$post` ID will be used

__meta_key__ : *string* (optional)
- Which meta key to access, queried by the `meta_key` column in `wp_postmeta`
- If no key is provided, the default `pw_meta` key is used

__sub_key__ : *string* (optional)
- Which subkey to retreive the value of
- If a sub-key is not specified, it will return the whole object

#### Example
```php
    $vars = array(
        'post_id'   =>  55,
        'meta_key'  =>  'pw_meta',
        "sub_key"   =>  'postOptions.colors.background',
        );
    $value = pw_get_wp_postmeta( $vars );
    // Returns the value '@orange' from previous example
```

------
