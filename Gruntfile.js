module.exports = function( grunt ) {

	// Load multiple grunt tasks using globbing patterns
	require( 'load-grunt-tasks' )( grunt );

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
			target: {
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

			target: {
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
					'!bin/**',
					'!tests/**',
					'!build/**',
					'!vendor/**',
					'!.git/**',
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
					'!**/README.md',
					'!**/readme.md',
					'!**/CHANGELOG.md',
					'!**/CONTRIBUTING.md',
					'!**/travis.yml',
					'!**/*~'
				],
				dest: 'build/<%= pkg.name %>/'
			}
		},

		// read version from package.json
		version: {
			readmetxt: {
				options: {
					prefix: 'Stable tag: *'
				},
				src: [ 'readme.txt' ]
			},
			tested_up_to: {
				options: {
					pkg: {
						"version": "<%= pkg.tested_up_to %>"
					},
					prefix: 'Tested up to: *'
				},
				src: [ 'readme.txt', 'readme.md' ]
			},
			plugin: {
				options: {
					prefix: 'Version: *'
				},
				src: [ 'readme.md', 'additional-content.php' ]
			},
			define: {
				options: {
					prefix: "'ADDITIONAL_CONTENT_VERSION', '*"
				},
				src: [ 'additional-content.php' ]
			},

		},

		// composer update
		composer: {
			build: {
				options: {
					cwd: 'build/additional-content',
				}
			},
			main: {
				options: {
					cwd: '',
				}
			}
		}

	} );

	grunt.loadNpmTasks( 'grunt-composer' );

	grunt.registerTask( 'composer_update', function( key, value ) {

		// build composer.json
		var projectFile = "build/additional-content/composer.json";

		if ( !grunt.file.exists( projectFile ) ) {
			grunt.log.error( "file " + projectFile + " not found" );
			return true; //return false to abort the execution
		}

		//get file as json object
		var project = grunt.file.readJSON( projectFile );

		project[ 'autoload' ][ 'classmap' ] = [ "includes/" ];

		//serialize it back to file
		grunt.file.write( projectFile, JSON.stringify( project, null, 2 ) );
		grunt.log.ok( 'updated composer.json in the build directory' );

		// composer update (in build and main directory)
		grunt.task.run( 'composer:build:install' );
		grunt.task.run( 'composer:main:update' );

	} );

	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );
	grunt.registerTask( 'build', [ 'uglify', 'version', 'makepot', 'clean', 'copy', 'composer_update' ] );

	grunt.util.linefeed = '\n';

};