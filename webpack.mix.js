let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .extract(['vue', 'sweetalert', 'moment', 'bootstrap', 'lodash', 'jquery', 'popper.js', 'axios', 'fullcalendar'])
   .sass('resources/assets/sass/app.scss', 'public/css')
   .copy('node_modules/fullcalendar/dist/fullcalendar.min.css', 'public/css')
   .copyDirectory('resources/assets/js/pages', 'public/js/pages');

if (mix.inProduction()) {
    mix.version();
    mix.disableNotifications();
}
