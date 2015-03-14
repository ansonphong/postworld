module.exports = function(grunt) {
  var angularVersion = 'angular-1.3.13';

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    uglify: {
    	multiple_sourcemaps: {
        files: {
          //'deploy/postworld.min.js': ['js/*.js', 'js/**/*.js'],
          'deploy/postworld.min.js':[
        	  
            ///// ANGULAR /////
            // Angular
            'lib/'+angularVersion+'/angular.min.js', // Angular.js has to go first

            ///// JS LIBRARIES /////
            // Underscore
            'lib/underscore/underscore.min.js',
            // Deep Merge
            'lib/deepmerge/deepmerge.js',
            // PHP.js
            'lib/php.js/php.js',
            // Angular UI Bootstrap
            'lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.12.0.min.js', // Angular UI Boostrap
            
            ///// THIRD PARTY ANGULAR MODULES /////
            // Angular Infinite Scroll
            'lib/ng-infinite-scroll/ng-infinite-scroll.js',
            // Angular Timer
            'lib/angular-timer/timer.js',
            // Angular Parallax
            'lib/angular-parallax/angular-parallax.js',
            // Angular Elastic
            'lib/angular-elastic/angular-elastic.js',
            // Angular Masonry
            'lib/angular-masonry/angular-masonry.js',
            // Angular Checklist Model
            'lib/checklist-model/checklist-model.js',

            ///// ANGULAR NATIVE EXTENSIONS /////
            // Angular Extensions
          	'lib/'+angularVersion+'/angular-sanitize.min.js',
          	'lib/'+angularVersion+'/angular-route.min.js',
          	'lib/'+angularVersion+'/angular-resource.min.js',
            'lib/'+angularVersion+'/angular-touch.min.js',
            'lib/'+angularVersion+'/angular-aria.min.js',

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
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

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