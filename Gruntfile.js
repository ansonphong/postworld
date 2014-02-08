module.exports = function(grunt) {

  var angular_version = 'angular-1.2-rc2';

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    uglify: {
    	multiple_sourcemaps: {
        files: {
          //'deploy/postworld.min.js': ['js/*.js', 'js/**/*.js'],
          'deploy/postworld.min.js':[
        	  
            ///// LIBRARY /////
            // Angular
            'lib/'+angular_version+'/angular.min.js', // Angular.js has to go first

            // Underscore
            'lib/underscore/underscore.min.js',

          	// Bootstrap
            'lib/bootstrap/bootstrap.min.js',             // Main Bootstrap JS
            //'lib/angular/ui-bootstrap-tpls-0.6.0.min.js', // Angular UI Boostrap
            'lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.10.0.min.js', // Angular UI Boostrap
            'lib/angular-strap/angular-strap.js',         // Angular Strap

            // Angular Utilities
            'lib/angular-ui-utils/angular-ui-utils.min.js',
            
            // Angular Timer
            'lib/angular-timer/timer.js',

            // Angular Extensions
          	'lib/'+angular_version+'/angular-sanitize.min.js',
          	'lib/'+angular_version+'/angular-route.min.js',
          	'lib/'+angular_version+'/angular-resource.min.js',

            // Angular Google Maps
            //'lib/angular-google-maps/angular-google-maps.min.js', // Angular Google Maps

          	///// Postworld /////
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


// options
// //  banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'