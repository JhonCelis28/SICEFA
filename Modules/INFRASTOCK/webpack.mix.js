/**
 * @file webpack.mix.js
 * @brief Configuración de Laravel Mix para el módulo INFRASTOCK.
 *
 * Este archivo define cómo se compilan los assets (JavaScript y SASS/CSS)
 * del módulo INFRASTOCK utilizando Laravel Mix. Configura la carga de variables
 * de entorno, las rutas de salida de los assets, la compilación de archivos
 * JavaScript y SASS, y la versión de los assets en producción para cache-busting.
 *
 * @author [Jhon Celis/Equipo]
 * @date [Fecha de Creación/Última Modificación]
 */

const dotenvExpand = require('dotenv-expand');
// Carga las variables de entorno desde el archivo .env principal de la aplicación.
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
// Fusiona los manifiestos de Webpack para que los assets del módulo se integren
// con los assets principales de la aplicación.
require('laravel-mix-merge-manifest');

// Establece la ruta pública para la salida de los assets y fusiona el manifiesto.
mix.setPublicPath('../../public').mergeManifest();

// Compila los archivos JavaScript y SASS del módulo INFRASTOCK.
mix.js(__dirname + '/Resources/assets/js/app.js', 'js/infrastock.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/infrastock.css');

// En producción, añade un hash a los nombres de archivo para cache-busting.
if (mix.inProduction()) {
    mix.version();
}
