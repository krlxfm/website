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

mix.webpackConfig({
        resolve: {
            alias: {
                'jquery-ui': 'jquery-ui-dist/jquery-ui.js'
            }
        }
    })
   .js('resources/assets/js/app.js', 'public/js')
   .js('resources/assets/js/schedule.js', 'public/js')
   .extract(['vue', 'sweetalert', 'moment', 'lodash', 'jquery', 'jquery-ui', 'popper.js', 'bootstrap', 'axios', 'fullcalendar'])
   .sass('resources/assets/sass/app.scss', 'public/css')
   .copy('node_modules/fullcalendar/dist/fullcalendar.min.css', 'public/css')
   .copyDirectory('resources/assets/js/pages', 'public/js/pages');

if (mix.inProduction()) {
    mix.version();
    mix.disableNotifications();
}
