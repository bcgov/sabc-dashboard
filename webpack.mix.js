const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .copy('resources/js/bootstrap.bundle.min.js', 'public/js')
    .copy('resources/js/popper.min.js', 'public/js')
    // .copy('resources/js/jquery-3.5.1.min.js', 'public/js')
    .copy('resources/js/jquery-3.6.0.min.js', 'public/js')
    .copy('resources/js/es6-promise.auto.min.js', 'public/js')
    .copy('resources/js/jquery.dataTables.min.js', 'public/js')
    .copy('resources/js/sabc_institution.js', 'public/js')
    .copy('resources/js/dropzone.js', 'public/js')
    .copy('resources/js/dropzone_extra.js', 'public/js')
    .copy('resources/js/iCheck.js', 'public/js')
    .copy('resources/js/lc_application.js', 'public/js')

    .copy('resources/css/bootstrap.min.css', 'public/css')
    .copy('resources/css/sabc_institution.css', 'public/css')
    //.copy('resources/css/dropzone.css', 'public/css')
    //.copy('resources/css/dropzone_extra.css', 'public/css')
    .copy('resources/css/lc-application.css', 'public/css')
    .copy('resources/css/iggy_bootstrap.css', 'public/css')
    .copy('resources/css/iggy_overrides.css', 'public/css')

    .copyDirectory('resources/assets', 'public/assets')
    .copyDirectory('resources/fonts', 'public/fonts')
    //admin directories
    .copyDirectory('resources/js/admin', 'public/js/admin')
    .copyDirectory('resources/css/admin', 'public/css/admin')
    .copyDirectory('resources/plugins/admin', 'public/plugins/admin')
//    .copyDirectory('resources/fonts/admin', 'public/fonts/admin')
    .copyDirectory('resources/images/admin', 'public/images/admin')

    //admin vuejs
    .js('resources/js/admin-app.js', 'public/js/admin')
    .js('resources/js/app-support-app.js', 'public/js/admin')

    .sass('resources/sass/custom_bootstrap.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css');
