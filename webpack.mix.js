const mix = require('laravel-mix'),    path = require('path');
mix.webpackConfig({
    stats: {
        children: true,
        warnings: false,
    },
});
const CompressionPlugin = require('compression-webpack-plugin');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */


// auto reload after save
mix.browserSync('http://127.0.0.1:8000');

// mix.alias({
//     'vue$': 'vue/dist/vue.esm.js',
//     '@': path.resolve('resources'),
//     'ext': path.resolve('node_modules'),
// })

//mix.vue({ version: 2, extractVueStyles: true });
mix.disableNotifications();
mix.disableSuccessNotifications();
mix.copy("resources/images", "public/images");
mix.copy("resources/fonts", "public/fonts");

mix.sass("resources/sass/admin/admin.scss", "public/css")    .options({
    processCssUrls: false,
}) .webpackConfig({
    plugins: [
        new CompressionPlugin({
            test: /\.(js|css)$/, // Compress JS and CSS files
            filename: '[path][base].gz', // Output compressed files
            algorithm: 'gzip',
            threshold: 10240, // Only compress files larger than 10KB
            minRatio: 0.8, // Compress if compression ratio is better than 80%
        }),
    ]}).version();
mix.sass("resources/sass/student/student.scss", "public/css")
    .options({
        processCssUrls: false,
    }) .webpackConfig({
    plugins: [
        new CompressionPlugin({
            test: /\.(js|css)$/, // Compress JS and CSS files
            filename: '[path][base].gz', // Output compressed files
            algorithm: 'gzip',
            threshold: 10240, // Only compress files larger than 10KB
            minRatio: 0.8, // Compress if compression ratio is better than 80%
        }),
    ]}).version();

mix.sass("resources/sass/fonts.scss", "public/css");
mix.sass("resources/sass/fonts_pl.scss", "public/css");
mix.js(["resources/js/admin/admin.js"], "public/js").version();


mix.copy("resources/js/admin/dragula.min.js", "public/js");


mix.copy("resources/js/admin/polyfill.min.js", "public/js");
mix.copy("resources/js/ui/jquery.ui.touch-punch.min.js", "public/js/ui");
mix.copy("resources/js/ui/1.8.21/jquery-ui.min.js", "public/js/ui");
mix.copy("resources/js/ajax/jquery.templates/beta1/jquery.tmpl.js", "public/js/");
mix.copy("resources/js/ajax/cookiealert.js", "public/js");

mix.vue();

//mix.compress();

//if (mix.inProduction())
//{
  mix.version();
//}
