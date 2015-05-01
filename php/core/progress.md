# Postworld // PHP / Progress API

## Overview
PW Progress is a simple internal API which stores the progress of current long-running functions in the `wp_options` table under the `postworld-progress` key, which contains a JSON object of long-running administrative processes.

This is primarily used grouped with polling mechanisms for checking on the status of long running processes.

## Functions

### pw_update_progress( `$key`, `$current`, `$total`, `$meta` )

Passing there parameters to the function:
```php
$key = 'function_name';
$current = 0;
$total = 2000;
$meta = array( 'key' => 'value' );
```

Will cause this in the the `wp_options` table under the `postworld-progress` key.

```javascript
{
    'function_name':{
        'status' : 'active',
        'items' : {
            'current' : 0,
            'total' : 20000
        },
        'meta'  :  {
            'key' : 'value'
        },
    }
}
```

### pw_get_progress( `$key` )
- Returns the contents of the progress key

### pw_end_progress( `$key` )
- Changes the status to `done`

### pw_delete_progress( `$key` )
- Removes the progress key


