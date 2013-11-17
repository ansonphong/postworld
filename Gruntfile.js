module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    uglify: {
    	multiple_sourcemaps: {
        files: {
          //'build/postworld.min.js': ['js/*.js', 'js/**/*.js'],
          'build/postworld.min.js':[
        	  'lib/angular/angular.min.js', // Angular.js has to go first
          	'lib/angular/ui-bootstrap-tpls-0.6.0.min.js',
          	'lib/bootstrap/bootstrap.min.js',
          	'lib/angular/angular-ui-utils.min.js',
          	'lib/angular/angular-sanitize.min.js',
          	'lib/angular/angular-route.min.js',
          	'lib/angular/angular-resource.min.js',

          	// Here comes Postworld
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