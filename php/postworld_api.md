# Postworld // PHP / API

Functions in this file are custom Postworld API functions which simplify basic data operations, and reading and writing from the database.

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



