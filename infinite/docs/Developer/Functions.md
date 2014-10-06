# Functions

### i_get_templates( $args )
- Returns a final set of templates as an object after scanning both defult and over-ride templte folders

#### Parameters : $args (optional)

__path_type__ : *string* (optional)
- Options:
    + __url__ (default): Returns absolute URL string of template file
    + __dir__ : Returns absolute directory path of template file

__source__ : *string* (optional)
- Which source to use
- Options:
    + __merge__ (default) : Return an object of the merged default and override template objects
    + __default__ : Return only template object of the default (infinite) templates folder
    + __override__ : Return only the template object of the override (child theme) templates folder

__templates_object__ : *array* (optional) // NOT  IMPLIMENTED
- Values of the array represent which subfolders are to be scanned and returned
- This will scan and return the contents of header and footer directories
``` php
    $templates_object = array('header','footer');
```
- If no value is given, all folders will be scanned

__meta_data__ : *boolean* ( Default: false ) // NOT  IMPLIMENTED
- Option to return objects rather than just paths
    + __true__ : Will return objects as sub-values with name, description, url, path
    + __false__ : Will return strings as sub-values with the specified path type


return - 

```php
    array(
        'header'    =>  array(
            'mainheader'    =>  array(
                'name'          =>  'Main Header',
                'description'   =>  'The main header for all pages.'
                'url'           =>  'http://...php',
                'path'          =>  '',
                )
            )
        )

```


### i_construct_template_obj( $path, $ext, $path_type )
- Constructs an object with the contents of the given path, with the given extension, in the form of an object which reflects the directory structure
- Goes one folder level deep

#### Example
This input:
```php
    i_construct_template_obj( '/var/.../views/', ".php" )
```

Will return this:
```php
    array(
        'header' => array(
            'default-header'    =>  '/var/.../views/default-header.php',
            //...
            )
        //...
        )
```




