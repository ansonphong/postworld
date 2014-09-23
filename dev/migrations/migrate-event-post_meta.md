# Migrate Event Data Model
- Primary use-case:
	+ _Reality Sandwich_


## Old Model

```javascript
post.post_meta : {
	"venue": "Test",
	"event_address": "address",
	"event_phone": "phone",
	"event_cost": "event cost",
	"event_city": "city",
	"event_region": "region",
	"event_country": "country",
	"event_postcode": "zip code",
	"event_start_date_obj": "2014-06-11T07:00:00.000Z",
	"event_end_date_obj": "2014-06-10T07:00:00.000Z",
	"event_all_day": "true"
};
```

## New Model

```javascript

post.post_meta : {
	date_obj : {
		"event_start_date_obj":"2014-02-24T08:23:37.389Z",
		"event_end_date_obj":"2014-02-24T08:23:37.389Z",
		"event_start_date":"2014-02-24 00:23",
		"event_end_date":"2014-02-24 00:23",
		"event_all_day":"true"
	},
	location_obj : {
		"city":"Portland",
		"city_code":"Portland",
		"region":"Oregon",
		"region_code":"OR",
		"country":"United States",
		"country_code":"US",
		"location_name":"Portland, OR, USA"
	}
}

```


## Method

- Query all posts in `event` post type
- Get all the IDs
- Sequence through each id:
	+ Check for post meta for `event_obj` and `date_obj`
	+ If they exist, load them and convert from JSON, create new empty objects
	+ Check for each old model field in meta data
	+ If it exists, 1. move it into new model, 2. delete the old post meta entry
	+ Save UNIX entries in wp_postworld_post_meta -> event_start / event_end
	+ Save the new post_meta fields as JSON
- Output report

DONE


