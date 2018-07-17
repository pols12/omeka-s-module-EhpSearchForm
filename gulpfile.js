// gulpfile.js for EhpSearchForm module
'use strict';

var gulp = require('gulp-util');
var gulp = require('gulp');

var sass = require('gulp-sass');
//var postcss = require('gulp-postcss');
//var autoprefixer = require('autoprefixer');

gulp.task('sass', function() {
  return gulp.src('asset/scss/*.scss')
      .pipe(sass({
          outputStyle: 'compressed',
          includePaths: ['../../node_modules/susy/sass']
      }).on('error', sass.logError))
      .pipe(gulp.dest('asset/css'));
});
