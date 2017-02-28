const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
       .webpack('app.js')
       .copy('node_modules/font-awesome/fonts/', 'public/fonts/')
       .styles([
            'bootstrap-datepicker/dist/css/bootstrap-datepicker.css'
        ], 'public/css/vendors.css', 'node_modules')
       .scripts([
            'bootstrap-datepicker/dist/js/bootstrap-datepicker.js'
        ], 'public/js/vendors.js', 'node_modules')
});
