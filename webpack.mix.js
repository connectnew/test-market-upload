const mix = require('laravel-mix');
const config = require('./webpack.config');

require('laravel-mix-eslint');

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
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig(config)
    .eslint({
        enforce: 'pre',
        test: ['js', 'vue'],
        exclude: ['node_modules'],
        loader: 'eslint-loader',
        options: {
            fix: false,
            cache: false,
        },
    })
    .version();
