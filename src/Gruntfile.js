module.exports = function (grunt) {
  'use strict';

  require('load-grunt-tasks')(grunt);

  var projectDir = __dirname + '/..';
  var dist = projectDir + '/dist';

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

  grunt.registerTask('release', [
    'clean',
    'build',
    'compress:releaseZip'
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
          { expand: true, src: 'client/img/**', dest: dist + '/' },
          { expand: true, src: 'client/libs/prism/components/*.min.js', dest: dist + '/' },
          { expand: true, src: 'data/articles/**', dest: dist + '/' },
          { expand: true, src: [ 'server/**', '!**/less/**' ], dest: dist + '/' }
        ]
      }
    },

    less: {
      dist: {
        files: {
          '.tmp/app-view.css': 'client/less/app-view.less',
          'server/theme/slim/css/theme.css': 'server/theme/slim/less/theme.less'
        }
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

    compress: {
      releaseZip: {
        options: {
          archive: dist + '/slim-wiki.zip'
        },
        files: [
          {
            expand: true,
            cwd: projectDir,
            src: [ 'LICENSE', 'README-for-release-zip.md' ],
            dest: 'slim-wiki/',
            rename: function(dest, path, rule) {
              return dest + path.replace('-for-release-zip', '');
            }
          },
          {
            expand: true,
            cwd: dist,
            src: [ '.htaccess', 'config-example.php', 'index.php', 'client/**', 'data/articles/**', 'server/**' ],
            dest: 'slim-wiki/'
          }
        ]
      }
    },

    watch: {
      less: {
        files: [
          'client/less/*.less',
          'server/theme/*/less/*.less'
        ],
        tasks: 'less'
      },
      copy: {
        files: 'client/images/**',
        tasks: 'copy'
      }
    }

  });

};
