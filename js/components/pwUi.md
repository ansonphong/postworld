# Postworld UI // Angular Directive

------

## *Directive:* pw-ui

The Postworld UI directive invokes the `pwUiCtrl` controller in the specified scope.

#### Secondary Directives:

### ui-views="*[expression]*"

The UI Views expression initializes the state of the views. The value / expression contains single level associative array where the key represents a _View ID_ and the values are booleans.

__Example__
```javascript
    {
        searchBar:true,
    }
```

## Scope Functions

### showView( *viewId* )
- Returns a boolean value of the specified view
- Ideal for use in combination with `ng-show` / `ng-hide` Angular Directives

### toggleView( *viewId* )
- Inverts the value of the specified view

### toggleElementDisplay( *jqLiteSelector* )
- Inverts the CSS `display` property of the specified selector
- Example values: `#myElementId`, `.myElementClass`

### focusElement( *jqLiteSelector* )
- Sets the browser focus to the specified element
- Ideal for use to focus on text inputs, etc.


