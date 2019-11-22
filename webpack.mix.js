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

mix.webpackConfig(require('./webpack.config'))
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .js('resources/assets/js/app_client.js', 'public/js')
    .sass('resources/assets/sass/app_client/app.scss', 'public/css/client')
    .version();
    // .babelConfig({
    //     plugins: ["syntax-dynamic-import"],
    //     presets: [
    //         'es2015'
    //     ]
    // });

