# Postworld : LESS Breakpoint Variables

The Postworld breakpoint variables are based on the Bootstrap screen sizes. Variables are defined to make it easy to define responsive CSS styling.

```less
	@media @sm { .myMobileStyles() }
```

---

The standard Bootstrap Breakpoints are defined as such:
- **lg** : 1200px
- **md** : 992px
- **sm** : 768px
- **xs** : 480px

---

## @xl
**lg** : 1200px (+ and above)
- Includes:
	+ Desktop / laptop with full screen or wide browser window
	+  Nexus 10 tablet on landscape orientation

## @lg
**md** : 992px - **lg** : 1200px
- Includes:
	+ Desktop / laptop with browser window not at full screen width
	+ Wide screen tablets on landscape orientation

## @md
**sm** : 768px - **md** : 992px
- Includes:
	+ iPhone 6 Plus or Nexus 7 on landscape orientation
	+ Nexus 10 tablet on portrait orientation

## @sm
**xs** : 480px - **sm** : 768px

## @xs
**xs** : 480px (- and below)

--- 

All the breakpoints are as follows:
```LESS
@xl:		~"only screen and (min-width: @{screen-lg})";

@lg-up:		~"only screen and (min-width: @{screen-md})";
@lg:		~"only screen and (min-width: @{screen-md}) and (max-width: @{screen-lg})";
@lg-down:	~"only screen and (max-width: @{screen-lg})";

@md-up:		~"only screen and (min-width: @{screen-sm})";
@md:		~"only screen and (min-width: @{screen-sm}) and (max-width: @{screen-md})";
@md-down:	~"only screen and (max-width: @{screen-md})";

@sm-up:		~"only screen and (min-width: @{screen-xs})";
@sm:		~"only screen and (min-width: @{screen-xs}) and (max-width: @{screen-sm})";
@sm-down:	~"only screen and (max-width: @{screen-sm})";

@xs:		~"only screen and (max-width: @{screen-xs})";
```

