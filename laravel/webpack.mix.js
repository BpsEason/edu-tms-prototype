const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/scss/app.scss', 'public/css')
   .webpackConfig({
       resolve: {
           alias: {
               '@': path.resolve(__dirname, 'resources/js')
           }
       }
   })
   .copyDirectory('resources/images', 'public/images')
   .version();

if (mix.inProduction()) {
    mix.sourceMaps();
}
