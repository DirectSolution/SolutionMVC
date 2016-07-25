module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // Copy web assets from bower_components to more convenient directories.
        copy: {
            main: {
                files: [
                    // Vendor scripts.
                    {
                        expand: true,
                        cwd: 'bower_components/bootstrap-sass/assets/javascripts',
                        src: ['**/*.js'],
                        dest: 'public/js/bootstrap/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/jquery/dist/',
                        src: ['**/*.js'],
                        dest: 'public/js/jquery/'
                    },
//                    {
//                        expand: true,
//                        cwd: 'bower_components/jquery-ui/ui/',
//                        src: ['**/*.js'],
//                        dest: 'Application/View/assets/js/jquery-ui/'
//                    },
                    {
                        expand: true,
                        cwd: 'bower_components/matchHeight/',
                        src: ['jquery.matchHeight-min.js'],
                        dest: 'public/js/matchHeight/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/select2/dist/js/',
                        src: ['select2.min.js'],
                        dest: 'public/js/select2/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/moment/min/',
                        src: ['moment.min.js'],
                        dest: 'public/js/moment/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/eonasdan-bootstrap-datetimepicker/build/js/',
                        src: ['bootstrap-datetimepicker.min.js'],
                        dest: 'public/js/datepicker/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/bootstrap-switch/dist/js/',
                        src: ['bootstrap-switch.min.js'],
                        dest: 'public/js/bootstrap-switch/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/rangeslider.js/dist/',
                        src: ['rangeslider.min.js'],
                        dest: 'public/js/rangeslider/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/dropzone/dist/',
                        src: ['dropzone.js'],
                        dest: 'public/js/dropzone/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/ckeditor/',
                        src: ['ckeditor.js'],
                        dest: 'public/js/ckeditor/'
                    },
//                    {
//                        expand: true,
//                        cwd: 'bower_components/datatables/media/js/',
//                        src: ['dataTables.bootstrap.min.js'],
//                        dest: 'public/js/datatables/'
//                    },
                    // Fonts.
                    {
                        expand: true,
                        filter: 'isFile',
                        flatten: true,
                        cwd: 'bower_components/',
                        src: ['font-awesome/fonts/**'],
                        dest: 'public/fonts/'
                    },
                    {
                        expand: true,
                        filter: 'isFile',
                        flatten: true,
                        cwd: 'bower_components/',
                        src: ['tinymce/skins/lightgrey/fonts/**'],
                        dest: 'public/fonts/'
                    },
                    // Stylesheets
                    {
                        expand: true,
                        cwd: 'bower_components/bootstrap-sass/assets/stylesheets/',
                        src: ['**/*.scss'],
                        dest: 'public/scss/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/font-awesome/css/',
                        src: ['**'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/select2/dist/css/',
                        src: ['select2.min.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/bootstrap-switch/dist/css/bootstrap3/',
                        src: ['bootstrap-switch.min.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/select2-bootstrap-theme/dist/',
                        src: ['select2-bootstrap.min.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/eonasdan-bootstrap-datetimepicker/build/css/',
                        src: ['bootstrap-datetimepicker.min.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/rangeslider.js/dist/',
                        src: ['rangeslider.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/dropzone/dist/',
                        src: ['dropzone.css'],
                        dest: 'public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/ckeditor/',
                        src: ['contents.css'],
                        dest: 'public/css/'
                    }
                    
//                    {
//                        expand: true,
//                        cwd: 'bower_components/datatables/media/css/',
//                        src: ['dataTables.bootstrap.min.css'],
//                        dest: 'public/css/'
//                    }
                ]
            }
        },
        // Compile SASS files into minified CSS.
        sass: {
            options: {
                includePaths: ['bower_components/bootstrap-sass/assets/stylesheets']
            },
            dist: {
                options: {
                    options: {
                        outputStyle: 'compressed'
                    },
                },
                files: {
                    'public/css/app.css': 'public/scss/app.scss'
                }
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: [{
                        expand: true,
                        cwd: 'public/css/',
                        src: ['*.css', '!*.min.css'],
                        dest: 'public/css',
                        ext: '.min.css'
                    }],           
            },
            combine:{
                files:{
                    'public/css/build.min.css' : ['!public/css/**.css','public/css/**.min.css']
                }
            }
//            bootstrapcss: {
//                src: 'public/css/app.css',
//                dest: 'public/css/app.min.css'
//            }
        },
        // Watch these files and notify of changes.
        watch: {
            grunt: {files: ['Gruntfile.js']},
            sass: {
                files: [
                    'public/scss/**'
                ],
                tasks: ['sass']
            }
        }
    });

    // Load externally defined tasks. 
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // Establish tasks we can run from the terminal.
    grunt.registerTask('build', ['sass', 'copy', 'cssmin']);
    grunt.registerTask('default', ['build', 'watch', 'cssmin']);
};