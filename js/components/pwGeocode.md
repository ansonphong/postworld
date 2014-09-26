# Postworld Geocode // Angular Component

------

## *Directive:* pw-geo-autocomplete
- Invokes the `pwGeoAutocompleteCtrl` controller in the specified scope.
- For use in combination with the *Angular UI Bootstrap Typeahead* module
	+ http://angular-ui.github.io/bootstrap/#/typeahead

### Example
Within the scope of this directive, the following snippet can be used:

```html
<input
	ng-model="post.post_meta[ eventKey ].location.geocode"
	placeholder="Type Location..."
	typeahead="address.formatted_address as address.formatted_address for address in getLocation($viewValue) | filter:$viewValue"
	typeahead-loading="loadingLocations"
	typeahead-on-select="addGeocode($item);toggleView('searchInput');">
```

## Scope Functions

### getLocation( *$viewValue* )
- Uses the `Google Maps Geocode API` to generate autocomplete results

### addGeocode( *$item* )
- Emits an action `pwAddGeocode` with the value of the selected item

------

## *Directive:* pw-geo-input

- Invokes the `pwGeoInputCtrl` controller in the specified scope.
- Works hand-in-hand with the `pw-geo-autocomplete` directive

#### Secondary Directives:

### geo-post="*[expression]*"
- This is typically linked to the Postworld Post Object : `post`
- Primarily links it to the post to auto-populate the `geo_latitude` and `geo_longitude` keys

### geo-location-obj="*[expression]*"
- This is used to link the result of a location autocomplete selection to the specified location object

