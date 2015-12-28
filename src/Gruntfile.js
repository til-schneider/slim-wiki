module.exports = function (grunt) {
  'use strict';

  require('load-grunt-tasks')(grunt);

  var dist = __dirname + '/../dist';

  grunt.registerTask('build', [
    'copy',
    'less',
    'useminPrepare',
    'concat',
    'uglify',
    'cssmin',
    'usemin'
  ]);

  grunt.registerTask('default', [
    'clean',
    'build'
  ]);

  grunt.initConfig({

    clean: {
      options: { force: true }, // Allow cleaning '../dist' (which is outside the working directory)
      stuff: [ '.tmp', dist ]
    },

    copy: {
      dist: {
        files: [
          { src: '.htaccess', dest: dist + '/' },
          { expand: true, src: '*.php', dest: dist + '/' },
          { expand: true, src: 'articles/**', dest: dist + '/' },
          { expand: true, src: 'client/img/**', dest: dist + '/' },
          { expand: true, src: 'client/libs/prism/components/*.min.js', dest: dist + '/' },
          { expand: true, src: 'server/**', dest: dist + '/' }
        ]
      }
    },

    less: {
      dist: {
        files: (function () {
          var files = {};
          files['.tmp/app-view.css'] = 'client/less/app-view.less';
          return files;
        })()
      }
    },

    useminPrepare: {
      html: 'server/layout/page.php',

      options: {
        root: '.',
        dest: dist
      }
    },

    usemin: {
      html: dist + '/server/layout/page.php'
    },

    watch: {
      less: {
        files: 'client/less/*.less',
        tasks: 'less'
      },
      copy: {
        files: 'client/images/**',
        tasks: 'copy'
      }
    }

  });

};
