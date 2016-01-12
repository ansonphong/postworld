module.exports = function(grunt) {
	var angularVersion = 'angular-1.4.8';

	// Project configuration.
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			multiple_sourcemaps: {
				files: {

					/**
					 * TOUCH
					 * Adds support packages specialized for touch devices
					 */
					'deploy/package-touch.min.js':[
						'lib/fastclick.js/fastclick.js',
					],

					/**
					 * MASONRY.JS
					 * Includes dependencies for Masonry.js
					 */
					'deploy/package-masonry.min.js':[
						'lib/masonry/masonry.pkgd.min.js',
						'lib/masonry/imagesloaded.pkgd.min.js',
					],

					 /**
					 * ANGULAR MOMENT
					 * Includes all the dependencies for Angular Moment.js
					 * As well as some additional packages for
					 * Time keeping
					 */
					'deploy/package-angular-moment.min.js':[
						'lib/moment.js/moment.min.js',
						'lib/angular-moment/angular-moment.min.js',
						'lib/moment.js/moment-timezone.min.js',
						'lib/jsTimezoneDetect/jstz.min.js',
						'lib/angular-timer/angular-timer.js',
						'lib/HumanizeDuration.js/humanize-duration.js',
					],

					 /**
					 * ANGULAR FULL CALENDAR
					 * Includes all the dependencies for Angular Moment.js
					 * Requires jQuery also included seperately.
					 */
					'deploy/package-angular-fullcalendar.min.js':[
						'lib/fullcalendar-2.2.5/lib/moment.min.js',
						'lib/fullcalendar-2.2.5/fullcalendar.min.js',
						'lib/fullcalendar-2.2.5/lib/jquery-ui.custom.min.js',
						'lib/ui-calendar/src/calendar.js'
					],
					

					/**
					 * POSTWORLD ADMIN
					 * All admin required javascript
					 */
					'deploy/postworld-admin.min.js':[
						'admin/js/*.js',
						'admin/js/**/*.js',
						'lib/angular-jquery-slider/slider.js'
					],

					/**
					 * POSTWORLD
					 * The primary core Postworld Javascript set.
					 * Includes Angular.js, and all core dependencies.
					 */

					//'deploy/postworld.min.js': ['js/*.js', 'js/**/*.js'],
					'deploy/postworld.min.js':[
						
						///// ANGULAR /////
						// Angular
						'lib/'+angularVersion+'/angular.min.js', // Angular.js has to go first

						///// JS LIBRARIES /////
						// Deep Merge
						'lib/deepmerge/deepmerge.js',
						// PHP.js
						'lib/php.js/php.js',
						// Angular UI Bootstrap
						'lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.14.3.min.js', // Angular UI Boostrap
						
						///// THIRD PARTY ANGULAR MODULES /////
						// Angular Infinite Scroll
						'lib/ng-infinite-scroll/ng-infinite-scroll.js',
						// Angular Timer
						//'lib/angular-timer/timer.js',
						// Angular Parallax
						'lib/angular-parallax/angular-parallax.js',
						// Angular Elastic
						'lib/angular-elastic/angular-elastic.js',
						// Angular Masonry
						'lib/angular-masonry/angular-masonry.js',
						// Angular Checklist Model
						'lib/checklist-model/checklist-model.js',
						// Angular Infinite Scroll
						'lib/ng-infinite-scroll/ng-infinite-scroll-1.2.js',

						///// ANGULAR NATIVE EXTENSIONS /////
						// Angular Extensions
						'lib/'+angularVersion+'/angular-sanitize.min.js',
						'lib/'+angularVersion+'/angular-route.min.js',
						'lib/'+angularVersion+'/angular-resource.min.js',
						'lib/'+angularVersion+'/angular-touch.min.js',
						'lib/'+angularVersion+'/angular-aria.min.js',
						'lib/'+angularVersion+'/angular-animate.min.js',

						///// POSTWORLD /////
						'js/*.js',
						'js/**/*.js',

					],
				},
				options: {
					mangle: false,
				},
			},
		},


		ngdocs: {
			//all: ['js/**/*.js'],
			api: {
				src: ['js/**/*.js'], //, '!app/**/*-spec.js'
				//src: ['js-testdocs/**/*.js'],
				title: 'AngularJS Documentation'
			},
			options:{
				dest: 'docs/ngdocs',
				//scripts: ['deploy/postworld.min.js'],
				html5Mode: false,
				startPage: '#/api',
				titleLink: "#/api",
				title: "Postworld",
				//image: "path/to/my/image.png",
				//imageLink: "http://my-domain.com",
				//bestMatch: true,
				/*
				analytics: {
					account: 'UA-08150815-0',
					domainName: 'my-domain.com'
				},
				discussions: {
					shortName: 'my',
					url: 'http://my-domain.com',
					dev: false
				}
				*/
			},
			/*
			tutorial: {
				src: ['content/tutorial/*.ngdoc'],
				title: 'Tutorial'
			},
			*/

		}



	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);

	// AngularJS Docs
	grunt.loadNpmTasks('grunt-ngdocs');

};


/*
						
// jQuery
'lib/jquery/jquery.min.js',

// Masonry
'lib/masonry.js/masonry.js',
'lib/masonry.js/imagesloaded.js',

// Moment.js
'lib/moment.js/moment.min.js',
// Moment-Timezone.js
'lib/moment.js/moment-timezone.min.js',
// Moment-Timezone Data.js
'lib/moment.js/moment-timezone-data.js',

// Angular Moment
'lib/angular-moment/angular-moment.min.js',

*/

// options
// //  banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'