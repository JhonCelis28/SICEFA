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
    .react()
    .sass('resources/sass/app.scss', 'public/css');

// Compilación de Tailwind para el módulo INFRASTOCK
mix.postCss('Modules/INFRASTOCK/Resources/assets/css/app.css', 'public/modules/infrastock/css', [
    require('tailwindcss')('Modules/INFRASTOCK/tailwind.config.js'),
]);
