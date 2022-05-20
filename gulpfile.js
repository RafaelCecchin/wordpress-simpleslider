'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const minify = require('gulp-minify');

const sass_src_dir = "assets/styles/scss/*.scss";
const sass_dest_dir = "./assets/styles";

const js_src_dir = "assets/scripts/js/*.js";
const js_dest_dir = "./assets/scripts";

function compilaSass(){
    return gulp
    .src(sass_src_dir)
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(sourcemaps.write("./"))
    .pipe(gulp.dest(sass_dest_dir));
}

function compilaJs() {
    return gulp.src(js_src_dir)
    .pipe(minify())
    .pipe(gulp.dest(js_dest_dir))
}

function watch() {
    gulp.watch(sass_src_dir, compilaSass)
    gulp.watch(js_src_dir, compilaJs)
}

gulp.task('sass', compilaSass)
gulp.task('watch', watch)