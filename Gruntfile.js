module.exports = function( grunt ) { // 1
	grunt.initConfig( { // 2
		pkg: grunt.file.readJSON( 'package.json' ), // 3

		uglify: { // 4
			options: { // 5
				banner: '/*\n' + // 6
					' * ' + '<%= pkg.name %>\n' + // 7
					' * ' + 'v<%= pkg.version %>\n' + // 8
					' * ' + '<%= grunt.template.today("yyyy-mm-dd") %>\n' + // 9
					' **/\n'
			},

			my_target: {
				files: {
					'assets/js/additional-content.min.js': [ 'assets/js/additional-content.js' ] // 10
				}
			}
		},
		watch: {
			scripts: {
				files: [ 'assets/js/additional-content.js' ],
				tasks: [ 'uglify' ]
			},
		}
	} );

	grunt.loadNpmTasks( 'grunt-contrib-uglify' ); // 11
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.registerTask( 'default', [ 'uglify', 'watch' ] );
}