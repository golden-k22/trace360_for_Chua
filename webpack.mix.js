const mix = require('laravel-mix');
let webpack = require('webpack');

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

mix.react('resources/js/dashboard.js', 'public/js')
    .react('resources/js/currentProductionStatus.js', 'public/js')
    .react('resources/js/overallEquipmentEffectiveness.js', 'public/js')
    .react('resources/js/statisticalProcessControl.js', 'public/js')
    .react('resources/js/monthlyProductionSummary.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
