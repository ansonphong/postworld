module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    uglify: {
    	multiple_sourcemaps: {
        files: {
          //'build/postworld.min.js': ['js/*.js', 'js/**/*.js'],
          'deploy/postworld.min.js':[
        	  
            // Angular
            'lib/angular/angular.min.js', // Angular.js has to go first

            // Underscore
            'lib/underscore/underscore.min.js',

          	// Bootstrap
            'lib/bootstrap/bootstrap.min.js',             // Main Bootstrap JS
            //'lib/angular/ui-bootstrap-tpls-0.6.0.min.js', // Angular UI Boostrap
            'lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.10.0.min.js', // Angular UI Boostrap
            'lib/angular-strap/angular-strap.js',         // Angular Strap

            // Angular Extensions
          	'lib/angular/angular-ui-utils.min.js',
          	'lib/angular/angular-sanitize.min.js',
          	'lib/angular/angular-route.min.js',
          	'lib/angular/angular-resource.min.js',

            // Angular Google Maps
            //'lib/angular-google-maps/angular-google-maps.min.js', // Angular Google Maps

          	// Postworld
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