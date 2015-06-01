module.exports = function( grunt ) {

	// Load multiple grunt tasks using globbing patterns
require('load-grunt-tasks')(grunt);

	'use strict';
	var banner = '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';
	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'additional-content',
			},
			target: {
				files: {
					src: [ '*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**' ]
				}
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					mainFile: 'additional-content.php',
					potFilename: 'additional-content.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		uglify: {
			options: {
				banner: '/*\n' +
					' * ' + '<%= pkg.name %>\n' +
					' * ' + 'v<%= pkg.version %>\n' +
					' * ' + '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
					' **/\n'
			},

			my_target: {
				files: {
					'includes/assets/js/additional-content.min.js': [ 'includes/assets/js/additional-content.js' ] // 10
				}
			}
		},

		// Clean up build directory
		clean: {
			main: [ 'build/<%= pkg.name %>' ]
		},

		// Copy the theme into the build directory
		copy: {
			main: {
				src: [
					'**',
					'!node_modules/**',
					'!vendor/composer/**',
					'!bin/**',
					'!tests/**',
					'!build/**',
					'!.git/**',
					'!vendor/autoload.php',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!.gitmodules',
					'!.gitattributes',
					'!.editorconfig',
					'!.tx/**',
					'!**/Gruntfile.js',
					'!**/package.json',
					'!**/phpunit.xml',
					'!**/composer.lock',
					'!**/README.md',
					'!**/readme.md',
					'!**/CHANGELOG.md',
					'!**/CONTRIBUTING.md',
					'!**/travis.yml',
					'!**/*~'
				],
				dest: 'build/<%= pkg.name %>/'
			}
		}

	} );
	
	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );
	grunt.registerTask( 'build', [ 'uglify', 'makepot', 'clean', 'copy' ] );

	grunt.util.linefeed = '\n';

};