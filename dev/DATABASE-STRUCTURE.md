Postworld Database Structure
======

## Table Structure

### Post Meta
**Default Name:** *wp_posworld_* **post_meta**

### User Meta
**Default Name:** *wp_posworld_* **user_meta**

#### Columns

##### **user_id** : *integer*

##### **post_relationships** : *JSON*
``` javascript
{
	viewed:[12,25,23,16,47,24,58,112,462,78,234,25,128],
	favorites:[12,16,25],
	read_later:[58,78],
	has_voted:{
		recent : {
			{
				post_id:242,
				time_voted:{{UNIX TIMESTAMP}}
			},
			{
				post_id:942,
				time_voted:{{UNIX TIMESTAMP}}
			},
		}
	}
}

```

##### **comment_relationships** : *JSON*
``` javascript
{
	has_voted:{
		recent : {
			{
				comment_id:24152,
				time_voted:{{UNIX TIMESTAMP}}
			},
			{
				comment_id:43532,
				time_voted:{{UNIX TIMESTAMP}}
			},
		}
	}
}
```