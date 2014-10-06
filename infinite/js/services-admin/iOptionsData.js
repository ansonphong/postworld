///// SERVICE /////
infinite.factory( 'iOptionsData', [ '_', function( $_ ){
	return {
		options: {
			'general':{
				'none' : false,
				'doubleSwitch':[
					{
						value: true,
						name: "Yes",
					},
					{
						value: false,
						name: "No",
					},
				],
				'tripleSwitch':[
					{
						value: "default",
						name: "Default",
					},
					{
						value: true,
						name: "Yes",
					},
					{
						value: false,
						name: "No",
					},
				],
				'customSwitch':[
					{
						value: false,
						name: "None",
					},
					{
						value: 'custom',
						name: 'Custom',
					}
				],
				'defaultAndCustomDoubleSwitch':[
					{
						value: "default",
						name: "Default",
					},
					{
						value: false,
						name: "No",
					},
					{
						value: true,
						name: "Yes",
					},
					{
						value: 'custom',
						name: 'Custom',
					}
				],
				'defaultCustomSwitch':[
					{
						value: 'default',
						name: 'Default',
					},
					{
						value: 'custom',
						name: 'Custom',
					}
				],
			},
			'share':{
				meta:[
					{
						name: 'Facebook',
						id: 'facebook',
						icon: 'icon-facebook-square',
						selected: true,
					},
					{
						name: 'Twitter',
						id: 'twitter',
						icon: 'icon-twitter-square',
						selected: true,
					},
					{
						name: 'Reddit',
						id: 'reddit',
						icon: 'icon-reddit-square',
						selected: true,
					},
					{
						name: 'Google Plus',
						id: 'google_plus',
						icon: 'icon-google-plus-square',
						selected: false,
					},
					{
						name: 'Pinterest',
						id: 'pinterest',
						icon: 'icon-pinterest-square',
						selected: false,
					},
				],
			},
			'header':{
				'type':[
					{
						slug: 'default',
						name: 'Default',
					},
					{
						slug: 'featured_image',
						name: 'Featured Image',
					},
					{
						slug: 'slider',
						name: 'Slider',
					},
				],
			},
			'slider':{
				'transition':[
					{
						slug: false,
						name: 'No Transition',
					},
					{
						slug: 'fade',
						name: 'Fade',
					},
					{
						slug: 'slide',
						name: 'Slide',
					},
				]
			},
			'gallery':{
				'template':[
					{
						slug: 'inline',
						name: 'Inline',
						description: 'Galleries appear inline with the post content as a grid of images.',
					},
					{
						slug: 'horizontal',
						name: 'Horizontal',
						description: 'All galleries in the post are merged into a single horizontal infinite scrolling gallery.',
					},
					{
						slug: 'vertical',
						name: 'Vertical',
						description: 'All galleries in the post are merged into a single vertical infinite scrolling gallery.',
					},
				],
			},
			'post_content':{
				columns:[
					{
						value: 1,
						name: '1 Column',
					},
					{
						value: 2,
						name: '2 Columns',
					},
					{
						value: 3,
						name: '3 Columns',
					},
				],
			},
			'link_url':{
				show_label:[
					{
						value: 'default',
						name: 'Default',
					},
					{
						value: false,
						name: 'No',
					},
					{
						value: true,
						name: 'Yes',
					},
					{
						value: 'custom',
						name: 'Custom',
					},
				],
			},
			'icon':{
				'iconx':[
					{
						class: "icon-merkaba",
					},
					{
						class: "icon-cloud",
					},
					{
						class: "icon-home",
					},
					{
						class: "icon-globe",
					},
					{
						class: "icon-seal-1",
					},
					{
						class: "icon-seal-2",
					},
					{
						class: "icon-triadic-1",
					},
					{
						class: "icon-triadic-2",
					},
					{
						class: "icon-triadic-3",
					},
					{
						class: "icon-triadic-4",
					},
					{
						class: "icon-triadic-5",
					},
					{
						class: "icon-atom",
					},
					{
						class: "icon-seed-of-life",
					},
					{
						class: "icon-seed-of-life-fill",
					},
					{
						class: "icon-light-bulb",
					},
					{
						class: "icon-moon",
					},
					{
						class: "icon-info",
					},
					{
						class: "icon-target",
					},
					{
						class: "icon-alert",
					},
					{
						class: "icon-office",
					},
					{
						class: "icon-newspaper",
					},
					{
						class: "icon-pin",
					},
					{
						class: "icon-pencil",
					},
					{
						class: "icon-quill",
					},
					{
						class: "icon-image",
					},
					{
						class: "icon-images",
					},
					{
						class: "icon-dice",
					},
					{
						class: "icon-book",
					},
					{
						class: "icon-book-2",
					},
					{
						class: "icon-books",
					},
					{
						class: "icon-info",
					},
					{
						class: "icon-cart",
					},
					{
						class: "icon-notebook",
					},
					{
						class: "icon-keyboard",
					},
					{
						class: "icon-drawer",
					},
					{
						class: "icon-bubble",
					},
					{
						class: "icon-bubbles",
					},
					{
						class: "icon-key",
					},
					{
						class: "icon-wrench",
					},
					{
						class: "icon-settings",
					},
					{
						class: "icon-eq",
					},
					{
						class: "icon-aid",
					},
					{
						class: "icon-wand",
					},
					{
						class: "icon-pie",
					},
					{
						class: "icon-stats",
					},
					{
						class: "icon-bars",
					},
					{
						class: "icon-bars-2",
					},
					{
						class: "icon-dashboard",
					},
					{
						class: "icon-lab",
					},
					{
						class: "icon-magnet",
					},
					{
						class: "icon-lightning",
					},
					{
						class: "icon-bookmark",
					},
					{
						class: "icon-bookmarks",
					},
					{
						class: "icon-heart",
					},
					{
						class: "icon-heart-o",
					},
					{
						class: "icon-heart-broken",
					},
					{
						class: "icon-share",
					},
					{
						class: "icon-atlas",
					},
					{
						class: "icon-atlas-o",
					},
					{
						class: "icon-gears",
					},
					{
						class: "icon-gamepad",
					},
					{
						class: "icon-cube",
					},
					{
						class: "icon-cubes",
					},
					{
						class: "icon-mail",
					},
					{
						class: "icon-circle-thick",
					},
					{
						class: "icon-circle-medium",
					},
					{
						class: "icon-circle-thin",
					},
					{
						class: "icon-triangle-up-thick",
					},
					{
						class: "icon-triangle-up-medium",
					},
					{
						class: "icon-triangle-up-thin",
					},
					{
						class: "icon-triangle-down-thick",
					},
					{
						class: "icon-triangle-down-medium",
					},
					{
						class: "icon-triangle-down-thin",
					},
					{
						class: "icon-square-thick",
					},
					{
						class: "icon-square-medium",
					},
					{
						class: "icon-square-thin",
					},
					{
						class: "icon-hexagon-thick",
					},
					{
						class: "icon-hexagon-medium",
					},
					{
						class: "icon-hexagon-thin",
					},
				],
				'glyphicons':[
					{
						class: "glyphicon glyphicon-star",
					},
					{
						class: "glyphicon glyphicon-film",
					},
					{
						class: "glyphicon glyphicon-cog",
					},
					{
						class: "glyphicon glyphicon-home",
					},
					{
						class: "glyphicon glyphicon-file",
					},
					{
						class: "glyphicon glyphicon-time",
					},
					{
						class: "glyphicon glyphicon-flag",
					},
					{
						class: "glyphicon glyphicon-tag",
					},
					{
						class: "glyphicon glyphicon-tags",
					},
					{
						class: "glyphicon glyphicon-book",
					},
					{
						class: "glyphicon glyphicon-bookmark",
					},
					{
						class: "glyphicon glyphicon-camera",
					},
					{
						class: "glyphicon glyphicon-font",
					},
					{
						class: "glyphicon glyphicon-picture",
					},
					{
						class: "glyphicon glyphicon-facetime-video",
					},
					{
						class: "glyphicon glyphicon-tint",
					},
					{
						class: "glyphicon glyphicon-screenshot",
					},
					{
						class: "glyphicon glyphicon-gift",
					},
					{
						class: "glyphicon glyphicon-calendar",
					},
					{
						class: "glyphicon glyphicon-eye-open",
					},
					{
						class: "glyphicon glyphicon-fire",
					},
					{
						class: "glyphicon glyphicon-leaf",
					},
					{
						class: "glyphicon glyphicon-globe",
					},
					{
						class: "glyphicon glyphicon-bell",
					},
					{
						class: "glyphicon glyphicon-bullhorn",
					},
					{
						class: "glyphicon glyphicon-link",
					},
					{
						class: "glyphicon glyphicon-pushpin",
					},
					{
						class: "glyphicon glyphicon-phone",
					},
					{
						class: "glyphicon glyphicon-usd",
					},
					{
						class: "glyphicon glyphicon-gbp",
					},
					{
						class: "glyphicon glyphicon-flash",
					},
					{
						class: "glyphicon glyphicon-stats",
					},
					{
						class: "glyphicon glyphicon-tree-conifer",
					},
					{
						class: "glyphicon glyphicon-tree-deciduous",
					},
					{
						class: "glyphicon glyphicon-warning-sign",
					},
				],
			},
		},

	}

}]);