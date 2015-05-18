module.exports = function(grunt) {
 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        wp_readme_to_markdown: {
            dist: {
                files: {
                  'readme.md': 'readme.txt'
                },
            },
        },
        makepot: {
            target: {
                options: {
                    include: [],
                    potFilename: '<%= pkg.name %>.pot',
                    type: 'wp-plugin',
                    potHeaders: { 
                        'report-msgid-bugs-to': 'info@pehaa.com' 
                    }
                }
            }
        },
        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: [{
                    expand: true,
                    cwd: 'admin/scss',
                    src: [
                        '*.scss'
                    ],
                    dest: 'admin/css',
                    ext: '.min.css'
                }]
            },
            dev: {
                options: {
                    banner: '/*! <%= pkg.name %> <%= pkg.version %> filename.css <%= grunt.template.today("yyyy-mm-dd h:MM:ss TT") %> */\n',
                    style: 'expanded'
                },
                files: [{
                    expand: true,
                    cwd: 'admin/scss',
                    src: [
                        '*.scss'
                    ],
                    dest: 'admin/css',
                    ext: '.css'
                }]
            }
        },
        jshint: {
            files: [
                'admin/js/simple-post-types-admin.js'
            ],
            options: {
                expr: true,
                globals: {
                    jQuery: true,
                    console: true,
                    module: true,
                    document: true
                }
            }
        },
        uglify: {
            dist: {
                options: {
                    banner: '/*! <%= pkg.name %> <%= pkg.version %> simple-post-types-admin.min.js <%= grunt.template.today("yyyy-mm-dd h:MM:ss TT") %> */\n',
                },
                files: {
                    'admin/js/simple-post-types-admin.min.js' : [
                        'admin/js/simple-post-types-admin.js'
                    ]
                }
            }
        },
        zip: {
            'pht-simple-post-types.zip': [
                '**.php',
                'admin/css/**',
                'admin/js/**',
                'admin/partials/**',
                'admin/**.php',
                'includes/**',
                'public/**',
                'languages/**',
                'images/**',
                '**.txt',
                '**.md'
            ]
        },
        watch: {
            css: {
                files: [ 'admin/scss/*.scss' ],
                tasks: ['sass:dev', 'sass:dist']
            },
            jsjshint: {
                files: [ 'admin/js/simple-post-types-admin.js' ],
                tasks: ['jshint']
            },
            js: {
                files: [ 'admin/js/simple-post-types-admin.js' ],
                tasks: ['uglify:dist']
            }
        }
    });
 

    //grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-curl');
    grunt.loadNpmTasks('grunt-phpdocumentor');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks( 'grunt-zip' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
 
    grunt.registerTask('default', [
        'makepot',
        'wp_readme_to_markdown',
        'sass:dist',
        'sass:dev',
        'jshint',
        'uglify:dist'
    ]);

    // Serve presentation locally
    grunt.registerTask( 'serve', ['watch'] );
 
};