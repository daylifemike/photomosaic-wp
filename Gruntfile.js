module.exports = function(grunt) {

    var plugin_name = 'photomosaic-for-wordpress';
    var plugin_path = '../wordpress/wp-content/plugins/' + plugin_name + '/';
    var nonwp_path = 'app/non-WP/download/files/';
    var demo_path = 'app/non-WP/demo3/';
    var dist_path = 'app/dist/';
    var release_path = '../' + plugin_name + '/';
    var files = [
        // base
        'app/js/app.js',
        // dependencies
        'app/includes/vendor/modernizr.js',
        'app/includes/vendor/imagesloaded.js',
        'app/includes/vendor/waypoints.js',
        'app/includes/vendor/prettyphoto/jquery.prettyphoto.js',
        // utils
        'app/js/utils.js',
        'app/js/error_checks.js',
        'app/js/inputs.js',
        'app/js/loader.js',
        // view constructors
        'app/js/layouts/common.js',
        'app/js/layouts/columns.js',
        'app/js/layouts/rows.js',
        'app/js/layouts/grid.js',
        // react
        'app/js/layouts/react.js',
        // photomosaic
        'app/js/core.js'
    ];
    var files_w_react = files.slice();
    files_w_react.unshift('app/includes/vendor/react.js');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        clean : {
            dist : {
                src : dist_path
            },
            plugin : {
                src : plugin_path + '/**/*',
                options : {
                    force : true
                }
            },
            release : {
                src : release_path + '/**/*',
                options : {
                    force : true
                }
            },
            codecanyon : {
                src : plugin_name + '-<%= pkg.version %>/'
            }
        },
        concat : {
            wp : {
                src : files,
                dest : dist_path + 'js/photomosaic.js',
                nonull : true
            },
            with_react : {
                src : files_w_react,
                dest : dist_path + 'js/photomosaic.js',
                nonull : true
            },
            without_react : {
                src : files,
                dest : dist_path + 'js/photomosaic-wo-react.js',
                nonull : true
            }
        },
        copy : {
            plugin : {
                expand : true,
                cwd : dist_path,
                src : '**/*',
                dest : plugin_path
            },
            release : {
                expand : true,
                cwd : dist_path,
                src : '**/*',
                dest : release_path
            },
            nonwp : {
                files : [
                    {
                        expand : true,
                        cwd : dist_path,
                        src : [
                            'js/**/*', 'css/**/*', 'images/**/*',
                            '!**/admin-page-icon.gif', '!**/photomosaic.admin.css', '!**/photomosaic.admin.js', '!**/photomosaic.editor.js'
                        ],
                        dest : nonwp_path,
                        filter : 'isFile'
                    },
                    {
                        expand: true,
                        cwd : dist_path + '/includes/vendor/',
                        src : ['prettyphoto/**/*'],
                        dest : nonwp_path + 'includes/',
                        filter : 'isFile'
                    }
                ]
            },
            demo : {
                files : [
                    {
                        expand : true,
                        cwd : dist_path,
                        src : [
                            'js/**/*', 'css/**/*', 'images/**/*',
                            '!**/admin-page-icon.gif', '!**/photomosaic.admin.css', '!**/photomosaic.admin.js', '!**/photomosaic.editor.js'
                        ],
                        dest : demo_path,
                        filter : 'isFile'
                    },
                    {
                        expand: true,
                        cwd : dist_path + '/includes/vendor/',
                        src : ['prettyphoto/**/*'],
                        dest : demo_path + 'includes/',
                        filter : 'isFile'
                    }
                ]
            },
            changelog : {
                expand : true,
                cwd : dist_path + 'includes/admin-markup/',
                src : 'whatsnew.txt',
                dest : release_path,
                filter : 'isFile',
                rename : function (dest, src) {
                    return dest + 'CHANGES.md';
                },
                options : {
                    process: function (content, srcpath) {
                        return content.replace(/(#{2,3})/g,"#$1");
                    }
                }
            },
            readme : {
                expand : true,
                cwd : 'app/',
                src : 'readme.txt',
                dest : plugin_name + '-<%= pkg.version %>/'
            },
            dist : {
                files : [
                    {
                        expand : true,
                        cwd : 'app/',
                        src : ['css/**/*', 'images/**/*', 'includes/**/*'],
                        dest : dist_path,
                        filter : 'isFile'
                    },
                    {
                        expand : true,
                        cwd : 'app/js/admin/',
                        src : ['photomosaic.admin.js', 'photomosaic.editor.js'],
                        dest : dist_path + 'js/',
                        filter : 'isFile'
                    },
                    {
                        expand: true,
                        cwd : 'app/includes/vendor/',
                        src : ['markdown.php'],
                        dest : dist_path + 'includes/',
                        filter : 'isFile',
                        rename : function (dest, src) {
                            return dest + 'markdown.php';
                        }
                    },
                    {
                        expand: true,
                        cwd : 'app/',
                        src : ['photomosaic.php'],
                        dest : dist_path,
                        filter : 'isFile'
                    }
                ]
            }
        },
        uglify : {
            dist : {
                src : dist_path + 'js/photomosaic.js',
                dest : dist_path + 'js/photomosaic.min.js',
                options : {
                    mangle: true,
                    sourceMap : true,
                    banner : '' +
                        '/*\n' +
                            '<%= pkg.name %> v<%= pkg.version %>\n' +
                            '<%= grunt.template.today("dddd, mmmm d, yyyy h:MM:ss TT Z") %>\n' +
                        '*/\n'
                }
            },
            lite : {
                src : dist_path + 'js/photomosaic-wo-react.js',
                dest : dist_path + 'js/photomosaic-wo-react.min.js',
                options : {
                    mangle: true,
                    sourceMap : true,
                    banner : '' +
                        '/*\n' +
                            '<%= pkg.name %> v<%= pkg.version %>\n' +
                            '<%= grunt.template.today("dddd, mmmm d, yyyy h:MM:ss TT Z") %>\n' +
                            'ReactJS CDN : //cdnjs.cloudflare.com/ajax/libs/react/0.11.2/react.min.js\n' +
                        '*/\n'
                }
            }
        },
        compress : {
            wordpress : {
                expand : true,
                cwd : dist_path,
                src : ['**/*'],
                options : {
                    archive : plugin_name + '-<%= pkg.version %>/' + plugin_name + '.zip'
                }
            },
            codecanyon : {
                expand : true,
                cwd : plugin_name + '-<%= pkg.version %>/',
                src : ['**/*'],
                options : {
                    archive : plugin_name + '-<%= pkg.version %>.zip'
                }
            }
        },
        replace : {
            dev : {
                src : dist_path + 'photomosaic.php',
                overwrite : true,
                replacements : [{
                    from : 'photomosaic.min.js',
                    to : 'photomosaic.js'
                }]
            },
            nonwp : {
                src : [
                    dist_path + 'js/photomosaic.js',
                    dist_path + 'js/photomosaic-wo-react.js'
                ],
                overwrite : true,
                replacements : [{
                    from : 'JQPM',
                    to : 'jQuery'
                },
                {
                    from : 'var $sub = jQuery.sub();',
                    to : ''
                },
                {
                    from : "registerNamespace('jQuery', $sub || {});",
                    to : ''
                },
                {
                    from : "$sub",
                    to : "jQuery"
                }]
            }
        },
        watch : {
            dev : {
                files : [ 'app/**/*', '!app/dist/**/*', 'Gruntfile.js' ],
                tasks : [ 'concat:wp', 'copy:dist', 'replace:dev', 'clean:plugin', 'copy:plugin', 'clean:dist' ]
            },
            // demo : {
            //     files : [ 'app/js/**/*', 'app/css/*', 'Gruntfile.js' ],
            //     tasks : [ 'demo' ]
            // }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-text-replace');

    grunt.registerTask('dist', [ 'concat:wp', 'copy:dist', 'uglify:dist' ]);
    grunt.registerTask('default', [ 'dist', 'clean:plugin', 'copy:plugin', 'clean:dist' ]);
    grunt.registerTask('release', [ 'dist', 'clean:release', 'copy:release', 'copy:changelog', 'clean:dist' ]);
    grunt.registerTask('codecanyon', [ 'dist', 'compress:wordpress', 'copy:readme', 'compress:codecanyon', 'clean:codecanyon', 'clean:dist' ]);
    grunt.registerTask('nonwp', [ 'concat:with_react', 'concat:without_react', 'copy:dist', 'replace:nonwp', 'uglify:dist', 'uglify:lite', 'copy:nonwp', 'clean:dist' ]);
    grunt.registerTask('demo', [ 'concat:with_react', 'copy:dist', 'replace:nonwp', /*'uglify:dist',*/ 'copy:demo', 'clean:dist' ]);
};